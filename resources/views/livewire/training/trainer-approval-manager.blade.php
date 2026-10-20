<div class="container-fluid py-3" dir="rtl" style="font-size: 0.9rem;">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show text-end shadow-sm border-0 py-2 small" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-gradient bg-primary text-white d-flex justify-content-between align-items-center py-2 px-3">
                    <h6 class="mb-0 fw-bold fs-6">
                        <i class="bi bi-shield-check me-1"></i> إدارة واعتماد المحاضرين ومتابعة السجل (Audit Trail)
                    </h6>
                    <a href="{{ route('training.trainers') }}" class="btn btn-light btn-sm text-primary fw-bold px-2 py-1 shadow-sm" style="font-size: 0.8rem;">
                        <i class="bi bi-person-plus me-1"></i> إضافة مدرب جديد
                    </a>
                </div>

                <div class="card-body bg-body-tertiary p-3">

                    <!-- شريط البحث والفلاتر -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm shadow-none" placeholder="بحث بالاسم رباعي، الرقم القومي، أو رقم المحمول...">
                        </div>
                        <div class="col-md-4">
                            <select wire:model.live="statusFilter" class="form-select form-select-sm shadow-none">
                                <option value="">-- كل حالات الاعتماد --</option>
                                <option value="pending">قيد المراجعة</option>
                                <option value="approved">معتمد</option>
                                <option value="rejected">مرفوض</option>
                            </select>
                        </div>
                    </div>

                    <!-- جدول عرض المدربين -->
                    <div class="table-responsive bg-white rounded-3 shadow-sm border">
                        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>#</th>
                                    <th>اسم المدرب</th>
                                    <th>الرقم القومي</th>
                                    <th>الصفة</th>
                                    <th>موقف الاعتماد</th>
                                    <th>المدخل وتاريخ الرفع</th>
                                    <th>آخر تعديل</th>
                                    <th>الإجراءات وقرار الاعتماد</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trainers as $index => $trainer)
                                    <tr>
                                        <td class="text-center">{{ $trainers->firstItem() + $index }}</td>
                                        <td class="fw-bold text-dark">{{ $trainer->name_ar }}</td>
                                        <td class="text-center font-monospace">{{ $trainer->national_id }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $trainer->trainer_type == 'trainer' ? 'مدرب' : 'استشاري' }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($trainer->status == 'approved')
                                                <span class="badge bg-success px-2 py-1">معتمد</span>
                                            @elseif($trainer->status == 'rejected')
                                                <span class="badge bg-danger px-2 py-1" title="{{ $trainer->rejection_reason }}">مرفوض</span>
                                            @else
                                                <span class="badge bg-warning text-dark px-2 py-1">قيد المراجعة</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-dark fw-semibold">{{ $trainer->creator->name ?? 'غير متوفر' }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">{{ $trainer->created_at ? $trainer->created_at->format('Y-m-d H:i') : '' }}</div>
                                        </td>
                                        <td>
                                            <div class="text-dark fw-semibold">{{ $trainer->editor->name ?? '---' }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">{{ $trainer->updated_at != $trainer->created_at ? $trainer->updated_at->format('Y-m-d H:i') : '' }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                @if($trainer->status != 'approved')
                                                    <button wire:click="approve({{ $trainer->id }})" class="btn btn-outline-success py-0 px-2" title="اعتماد">
                                                        <i class="bi bi-check-lg"></i> اعتماد
                                                    </button>
                                                @endif
                                                @if($trainer->status != 'rejected')
                                                    <button wire:click="openRejectModal({{ $trainer->id }})" class="btn btn-outline-danger py-0 px-2" title="رفض">
                                                        <i class="bi bi-x-lg"></i> رفض
                                                    </button>
                                                @endif
                                                <button wire:click="viewAuditLog({{ $trainer->id }})" class="btn btn-outline-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#auditModal" title="سجل التدقيق">
                                                    <i class="bi bi-clock-history"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">لا توجد بيانات مدربين مسجلة حتى الآن.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- الترقيم (Pagination) -->
                    <div class="mt-3">
                        {{ $trainers->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- نافذة منسدلة (Modal) لعرض سجل التدقيق التفصيلي -->
    <div class="modal fade" id="auditModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title fs-6">سجل التدقيق والإجراءات للمدرب</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="font-size: 0.85rem;">
                    @if($selectedTrainer)
                        <p><strong>اسم المدرب:</strong> {{ $selectedTrainer->name_ar }}</p>
                        <hr>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bi bi-person-plus text-primary me-1"></i> <strong>تم الرفع بواسطة:</strong>
                                {{ $selectedTrainer->creator->name ?? 'غير متوفر' }}
                                <span class="text-muted">({{ $selectedTrainer->created_at }})</span>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-pencil-square text-warning me-1"></i> <strong>آخر تعديل بواسطة:</strong>
                                {{ $selectedTrainer->editor->name ?? 'لا توجد تعديلات سابقة' }}
                                <span class="text-muted">({{ $selectedTrainer->updated_at != $selectedTrainer->created_at ? $selectedTrainer->updated_at : '---' }})</span>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-shield-check text-success me-1"></i> <strong>قرار الاعتماد:</strong>
                                @if($selectedTrainer->status == 'approved')
                                    <span class="text-success fw-bold">معتمد</span> بواسطة ({{ $selectedTrainer->approver->name ?? '---' }}) بتاريخ {{ $selectedTrainer->approval_date }}
                                @elseif($selectedTrainer->status == 'rejected')
                                    <span class="text-danger fw-bold">مرفوض</span> بواسطة ({{ $selectedTrainer->approver->name ?? '---' }})
                                    <br><span class="text-danger">السبب: {{ $selectedTrainer->rejection_reason }}</span>
                                @else
                                    <span class="text-warning fw-bold">قيد المراجعة (لم يُبت فيه بعد)</span>
                                @endif
                            </li>
                        </ul>
                    @else
                        <p class="text-center text-muted">جاري تحميل البيانات...</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- نافذة سبب الرفض -->
    @if($showRejectModal)
        <div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white py-2">
                        <h6 class="modal-title fs-6">سبب رفض اعتماد المدرب</h6>
                        <button type="button" wire:click="$set('showRejectModal', false)" class="btn-close btn-close-white"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label text-secondary mb-1">اكتب سبب الرفض بالتفصيل:</label>
                        <textarea wire:model="rejectionReason" class="form-control form-control-sm" rows="3" placeholder="أدخل سبب الرفض..."></textarea>
                    </div>
                    <div class="modal-footer py-1">
                        <button type="button" wire:click="$set('showRejectModal', false)" class="btn btn-secondary btn-sm">إلغاء</button>
                        <button type="button" wire:click="reject" class="btn btn-danger btn-sm">تأكيد الرفض</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
