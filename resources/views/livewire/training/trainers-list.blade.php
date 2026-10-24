<div class="container-fluid py-3" dir="rtl" style="font-size: 0.9rem;">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <!-- رسائل النجاح والتنبيهات -->
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show text-end shadow-sm border-0 py-2 small" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-gradient bg-primary text-white d-flex justify-content-between align-items-center py-2 px-3">
                    <h6 class="mb-0 fw-bold fs-6">
                        <i class="bi bi-people-fill me-1"></i> إدارة وعرض قائمة المحاضرين والاستشاريين
                    </h6>
                    <a href="{{ route('training.trainers') }}" class="btn btn-light btn-sm text-primary fw-bold px-3 py-1 shadow-sm" style="font-size: 0.8rem;">
                        <i class="bi bi-person-plus-fill me-1"></i> إضافة مدرب جديد
                    </a>
                    <button wire:click="exportExcel" class="btn btn-light btn-sm text-primary fw-bold px-3 py-1 shadow-sm" style="font-size: 0.8rem;">
                        <i class="bi bi-file-earmark-excel-fill me-1"></i> تصدير قائمة المحاضرين
                    </button>
                </div>

                <div class="card-body bg-body-tertiary p-3">

                    <!-- حقول البحث والتصفية المتقدمة -->
                    <div class="row g-2 mb-3 bg-white p-3 rounded-3 shadow-sm border">
                        <div class="col-md-3">
                            <label class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8rem;">البحث بالاسم</label>
                            <input type="text" wire:model.live.debounce.300ms="searchName" class="form-control form-control-sm shadow-none" placeholder="اسم المدرب...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8rem;">الرقم القومي</label>
                            <input type="text" wire:model.live.debounce.300ms="searchNationalId" class="form-control form-control-sm shadow-none" placeholder="14 رقماً...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8rem;">التخصص الوظيفي</label>
                            <select wire:model.live="filterSpecialization" class="form-select form-select-sm shadow-none">
                                <option value="">-- جميع التخصصات --</option>
                                @foreach($specializations as $spec)
                                    <option value="{{ $spec->id }}">{{ $spec->name ?? $spec->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8rem;">حالة الاعتماد</label>
                            <select wire:model.live="filterStatus" class="form-select form-select-sm shadow-none">
                                <option value="">-- جميع الحالات --</option>
                                <option value="pending">قيد الانتظار</option>
                                <option value="approved">معتمد</option>
                                <option value="rejected">مرفوض</option>
                            </select>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8rem;">أُضيف / عُتمد بالفترة (من - إلى)</label>
                            <div class="input-group input-group-sm">
                                <input type="date" wire:model.live="dateFrom" class="form-control shadow-none">
                                <span class="input-group-text bg-light">إلى</span>
                                <input type="date" wire:model.live="dateTo" class="form-control shadow-none">
                            </div>
                        </div>
                    </div>

                    <!-- جدول العرض مع تفعيل السكرول الأفقي المريح -->
                    <div class="table-responsive bg-white rounded-3 shadow-sm border" style="max-height: 70vh; overflow-x: auto;">
                        <table class="table table-hover table-striped align-middle mb-0 text-nowrap" style="font-size: 0.88rem; min-width: 1200px;">
                            <thead class="table-light text-center sticky-top">
                                <tr>
                                    <th class="py-2" style="width: 50px;">#</th>
                                    <th class="py-2 text-start">الاسم</th>
                                    <th class="py-2">الرقم القومي</th>
                                    <th class="py-2">رقم المحمول</th>
                                    <th class="py-2 text-start">جهة العمل</th>
                                    <th class="py-2">الصف</th>
                                    <th class="py-2 text-start">التخصص</th>
                                    <th class="py-2">حالة الاعتماد</th>
                                    <th class="py-2">أضيف بواسطة</th>
                                    <th class="py-2">آخر تعديل بواسطة والتاريخ</th>
                                    <th class="py-2" style="width: 130px;">الإجراءات والتحكم</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trainers as $index => $trainer)
                                    <tr>
                                        <td class="text-center fw-bold text-muted">{{ $trainers->firstItem() + $index }}</td>
                                        <td class="fw-bold text-dark text-start">
                                            <i class="bi bi-person-badge text-primary me-1"></i> {{ $trainer->name_ar }}
                                        </td>
                                        <td class="text-center font-monospace text-secondary">{{ $trainer->national_id }}</td>
                                        <td class="text-center font-monospace">{{ $trainer->phone ?? '---' }}</td>
                                        <td class="text-start text-truncate" style="max-width: 180px;" title="{{ $trainer->workplace }}">
                                            {{ $trainer->workplace ?? 'غير محدد' }}
                                        </td>
                                        <td class="text-center">
                                            @if($trainer->trainer_type == 'trainer')
                                                <span class="badge bg-info-subtle text-info-emphasis border border-info px-2 py-1">مدرب</span>
                                            @else
                                                <span class="badge px-2 py-1" style="background-color: #f3e8ff; color: #6b21a8; border: 1px solid #d8b4fe;">استشاري</span>
                                            @endif
                                        </td>
                                        <td class="text-start">{{ $trainer->specialization->name ?? 'غير محدد' }}</td>
                                        <td class="text-center">
                                            @if($trainer->status == 'approved')
                                                <span class="badge bg-success-subtle text-success border border-success px-2 py-1"><i class="bi bi-check-circle me-1"></i> معتمد</span>
                                            @elseif($trainer->status == 'rejected')
                                                <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1" title="{{ $trainer->rejection_reason }}"><i class="bi bi-x-circle me-1"></i> مرفوض</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2 py-1"><i class="bi bi-clock-history me-1"></i> قيد الانتظار</span>
                                            @endif
                                        </td>
                                        <td class="text-center text-muted small">
                                            {{ $trainer->creator->name ?? 'النظام' }}
                                        </td>
                                        <td class="text-center text-muted" style="font-size: 0.78rem;">
                                            @if($trainer->editor)
                                                <span class="fw-semibold text-dark">{{ $trainer->editor->name }}</span><br>
                                                <small class="text-secondary">{{ $trainer->updated_at ? $trainer->updated_at->format('Y-m-d H:i') : '' }}</small>
                                            @else
                                                <span class="text-secondary fst-italic">لا يوجد تعديل</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm shadow-sm rounded-2 overflow-hidden" role="group">
                                                <a href="{{ route('training.trainers.edit', $trainer->id) }}" class="btn btn-light text-primary border px-2" title="تعديل البيانات">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-light text-info border px-2" title="عرض موقف المدرب " data-bs-toggle="modal" data-bs-target="#trainerStatusModal{{ $trainer->id }}">
                                                    <i class="bi bi-eye-fill"></i>
                                                </button>
                                                <button type="button" wire:click="deleteTrainer({{ $trainer->id }})" wire:confirm="هل أنت متأكد من حذف هذا المحاضر نهائياً؟" class="btn btn-light text-danger border px-2" title="حذف">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </div>

                                            <!-- Modal عرض موقف المدرب (تصميم عصري ومضغوط مريح للعين) -->
                                            <div class="modal fade" id="trainerStatusModal{{ $trainer->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" dir="rtl">

                                                        <div class="modal-header bg-gradient bg-primary text-white py-2.5 px-3 d-flex justify-content-between align-items-center">
                                                            <h6 class="modal-title fw-bold fs-6 mb-0 d-flex align-items-center">
                                                                <i class="bi bi-person-badge-fill me-2 fs-5"></i>
                                                                <span>ملف وموقف المحاضر: <span class="fw-normal opacity-90">{{ $trainer->name_ar }}</span></span>
                                                            </h6>
                                                            <button type="button" class="btn-close btn-close-white m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <div class="modal-body bg-light p-3 p-md-4" style="font-size: 0.88rem;">
                                                            <div class="row g-3">

                                                                <!-- 1. بطاقة البيانات الأساسية -->
                                                                <div class="col-md-6">
                                                                    <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                                                                        <div class="card-header bg-white border-bottom-0 pt-2 pb-1 px-3 d-flex align-items-center text-primary">
                                                                            <i class="bi bi-person-vcard fs-6 me-2"></i>
                                                                            <h6 class="fw-bold mb-0" style="font-size: 0.88rem;">البيانات الشخصية والاتصال</h6>
                                                                        </div>
                                                                        <div class="card-body pt-0 px-3 pb-2">
                                                                            <ul class="list-group list-group-flush" style="font-size: 0.83rem;">
                                                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-1 border-dashed">
                                                                                    <span class="text-muted">الاسم بالعربي:</span>
                                                                                    <span class="fw-bold text-dark">{{ $trainer->name_ar }}</span>
                                                                                </li>
                                                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-1 border-dashed">
                                                                                    <span class="text-muted">الاسم بالإنجليزي:</span>
                                                                                    <span class="fw-semibold text-secondary">{{ $trainer->name_en ?? '---' }}</span>
                                                                                </li>
                                                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-1 border-dashed">
                                                                                    <span class="text-muted">الرقم القومي:</span>
                                                                                    <span class="font-monospace fw-bold text-dark dir-ltr">{{ $trainer->national_id }}</span>
                                                                                </li>
                                                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-1 border-dashed">
                                                                                    <span class="text-muted">الميلاد / النوع:</span>
                                                                                    <span class="fw-semibold text-dark">{{ $trainer->birth_date }} ({{ $trainer->gender == 'male' ? 'ذكر' : 'أنثى' }})</span>
                                                                                </li>
                                                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-1 border-dashed">
                                                                                    <span class="text-muted">رقم الهاتف:</span>
                                                                                    <span class="font-monospace fw-bold text-dark dir-ltr">{{ $trainer->phone ?? 'غير متوفر' }}</span>
                                                                                </li>
                                                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-1 border-0">
                                                                                    <span class="text-muted">البريد الإلكتروني:</span>
                                                                                    <span class="text-primary text-break fw-semibold">{{ $trainer->email ?? 'غير متوفر' }}</span>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- 2. بطاقة الموقف الإداري والاعتماد -->
                                                                <div class="col-md-6">
                                                                    <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                                                                        <div class="card-header bg-white border-bottom-0 pt-2 pb-1 px-3 d-flex align-items-center text-primary">
                                                                            <i class="bi bi-shield-check fs-6 me-2"></i>
                                                                            <h6 class="fw-bold mb-0" style="font-size: 0.88rem;">الموقف الإداري والتخصص</h6>
                                                                        </div>
                                                                        <div class="card-body pt-0 px-3 pb-2">
                                                                            <ul class="list-group list-group-flush" style="font-size: 0.83rem;">
                                                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-1.5 border-dashed">
                                                                                    <span class="text-muted">الصفة التدريبية:</span>
                                                                                    @if($trainer->trainer_type == 'trainer')
                                                                                        <span class="badge bg-info-subtle text-info-emphasis border border-info px-2 py-0.5 rounded-pill" style="font-size: 0.75rem;">مدرب</span>
                                                                                    @else
                                                                                        <span class="badge px-2 py-0.5 rounded-pill" style="background-color: #f3e8ff; color: #6b21a8; border: 1px solid #d8b4fe; font-size: 0.75rem;">استشاري</span>
                                                                                    @endif
                                                                                </li>
                                                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-1.5 border-dashed">
                                                                                    <span class="text-muted">حالة الاعتماد:</span>
                                                                                    @if($trainer->status == 'approved')
                                                                                        <span class="badge bg-success-subtle text-success border border-success px-2 py-0.5 rounded-pill" style="font-size: 0.75rem;"><i class="bi bi-check-circle-fill me-1"></i> معتمد</span>
                                                                                    @elseif($trainer->status == 'rejected')
                                                                                        <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-0.5 rounded-pill" style="font-size: 0.75rem;" title="{{ $trainer->rejection_reason }}">مرفوض</span>
                                                                                    @else
                                                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2 py-0.5 rounded-pill" style="font-size: 0.75rem;">قيد الانتظار</span>
                                                                                    @endif
                                                                                </li>
                                                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-1.5 border-dashed">
                                                                                    <span class="text-muted">جهة العمل:</span>
                                                                                    <span class="fw-bold text-dark text-end text-truncate" style="max-width: 55%;">{{ $trainer->workplace ?? 'غير محدد' }}</span>
                                                                                </li>
                                                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-1.5 border-dashed">
                                                                                    <span class="text-muted">التخصص الفني:</span>
                                                                                    <span class="fw-semibold text-dark">{{ $trainer->specialization->name ?? 'غير محدد' }}</span>
                                                                                </li>
                                                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-1.5 border-0">
                                                                                    <span class="text-muted">شهادة TOT:</span>
                                                                                    <span class="fw-semibold {{ $trainer->has_tot_certificate ? 'text-success' : 'text-secondary' }}">
                                                                                        {{ $trainer->has_tot_certificate ? 'حاصل عليها' : 'غير حاصل عليها' }}
                                                                                    </span>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- 3. شريط سجل التدقيق والأنشطة -->
                                                                <div class="col-12">
                                                                    <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                                                                        <div class="d-flex align-items-center text-secondary mb-2 border-bottom pb-2">
                                                                            <i class="bi bi-clock-history fs-5 me-2 text-primary"></i>
                                                                            <h6 class="fw-bold mb-0" style="font-size: 0.88rem;">سجل الحركات والتدقيق الإداري</h6>
                                                                        </div>
                                                                        <div class="row g-2 text-muted small">
                                                                            <div class="col-md-4 d-flex align-items-center">
                                                                                <i class="bi bi-person-plus text-secondary me-1.5 fs-6"></i>
                                                                                <span>أُضيف بواسطة: <strong class="text-dark">{{ $trainer->creator->name ?? 'النظام' }}</strong></span>
                                                                            </div>
                                                                            <div class="col-md-4 d-flex align-items-center">
                                                                                <i class="bi bi-calendar-check text-secondary me-1.5 fs-6"></i>
                                                                                <span>تاريخ الإضافة: <strong class="text-dark font-monospace dir-ltr">{{ $trainer->created_at ? $trainer->created_at->format('Y-m-d H:i') : '---' }}</strong></span>
                                                                            </div>
                                                                            <div class="col-md-4 d-flex align-items-center">
                                                                                <i class="bi bi-pencil-square text-secondary me-1.5 fs-6"></i>
                                                                                <span>آخر تعديل: <strong class="text-dark">{{ $trainer->editor->name ?? 'لا يوجد' }}</strong>
                                                                                @if($trainer->editor)
                                                                                    <span class="font-monospace dir-ltr">({{ $trainer->updated_at->format('Y-m-d H:i') }})</span>
                                                                                @endif
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>

                                                        <div class="modal-footer bg-white border-top-0 py-2 px-3 justify-content-between">
                                                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i> آخر تحديث للبيانات: {{ $trainer->updated_at ? $trainer->updated_at->diffForHumans() : 'حديثاً' }}</small>
                                                            <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill shadow-sm fw-bold" data-bs-dismiss="modal">
                                                                <i class="bi bi-x-lg me-1"></i> إغلاق
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-3 d-block mb-1"></i> لا توجد بيانات محاضرين مطابقة لفلاتر البحث.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- الترقيم (Pagination) -->
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $trainers->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
