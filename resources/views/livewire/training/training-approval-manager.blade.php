<div>
    <div class="container-fluid py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-primary mb-0">
                <i class="bi bi-check2-square me-1"></i> اعتمادات وموافقات البرامج التدريبية
            </h5>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-secondary">البرامج التدريبية بانتظار الاعتماد أو المراجعة</span>
                <div style="width: 300px;">
                    <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="بحث بالاسم أو الكود...">
                </div>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover table-striped mb-0 text-center align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الكود</th>
                            <th>اسم البرنامج</th>
                            <th>نوع البرنامج</th>
                            <th>الساعات / الأيام</th>
                            <th>حالة الاعتماد</th>
                            <th>آخر إجراء / المسؤول</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programs as $item)
                            <tr wire:key="program-approval-{{ $item->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $item->code }}</span></td>
                                <td class="fw-bold text-start">{{ $item->name_ar }}</td>
                                <td><span class="badge bg-info text-dark">{{ $item->programType->name ?? '-' }}</span></td>
                                <td>{{ $item->training_hours }} ساعة / {{ $item->duration_days }} أيام</td>
                                <td>
                                    @if($item->approval_status == 'approved')
                                        <span class="badge bg-success">معتمد</span>
                                    @elseif($item->approval_status == 'rejected')
                                        <span class="badge bg-danger">مرفوض</span>
                                    @else
                                        <span class="badge bg-warning text-dark">قيد المراجعة / مسودة</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $lastApproval = $item->approvals->first();
                                    @endphp
                                    @if($lastApproval)
                                        <div class="fw-bold text-dark">{{ $lastApproval->user->name ?? '-' }}</div>
                                        <div class="text-muted" style="font-size: 11px;">{{ $lastApproval->action_at?->format('Y-m-d H:i') }}</div>
                                        @if($lastApproval->status == 'rejected' && $lastApproval->rejectionReason)
                                            <span class="badge bg-danger text-wrap mt-1" style="font-size: 10px;" title="{{ $lastApproval->notes }}">
                                                {{ $lastApproval->rejectionReason->name }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <button wire:click="openApprovalModal({{ $item->id }})" class="btn btn-outline-primary btn-sm py-0 px-2" data-bs-toggle="modal" data-bs-target="#approvalModal" title="اتخاذ قرار">
                                            <i class="bi bi-shield-check"></i>
                                        </button>
                                        <button wire:click="showProgramDetails({{ $item->id }})" class="btn btn-outline-info btn-sm py-0 px-2 text-dark" data-bs-toggle="modal" data-bs-target="#detailsModal" title="عرض التفاصيل والسجل">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">لا توجد برامج تدريبية مسجلة.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($programs->hasPages())
                <div class="card-footer bg-white py-2">
                    {{ $programs->links() }}
                </div>
            @endif
        </div>

        <!-- Modal اتخاذ القرار -->
        <div wire:ignore.self class="modal fade" id="approvalModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white py-2">
                        <h6 class="modal-title fw-bold">اعتماد أو مراجعة البرنامج التدريبي</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if($currentProgram)
                            <div class="mb-3">
                                <span class="text-muted small">اسم البرنامج:</span>
                                <p class="fw-bold text-dark mb-1">{{ $currentProgram->name_ar }}</p>
                                <span class="badge bg-light text-dark border">{{ $currentProgram->code }}</span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">القرار:</label>
                                <select class="form-select form-select-sm" wire:model.live="actionStatus">
                                    <option value="approved">اعتماد البرنامج (Approved)</option>
                                    <option value="rejected">رفض البرنامج (Rejected)</option>
                                </select>
                            </div>

                            @if($actionStatus == 'rejected')
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-danger">سبب الرفض الأساسي: *</label>
                                    <select class="form-select form-select-sm" wire:model="rejection_reason_id">
                                        <option value="">-- اختر سبب الرفض --</option>
                                        @foreach($rejectionReasons as $reason)
                                            <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('rejection_reason_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label small fw-bold">ملاحظات إضافية (اختياري):</label>
                                <textarea class="form-control form-control-sm" wire:model="notes" rows="2" placeholder="أي تفاصيل أو ملاحظات إضافية..."></textarea>
                                @error('notes') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer py-1">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                        <button type="button" wire:click="submitApproval" class="btn btn-success btn-sm">حفظ القرار</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal عرض تفاصيل البرنامج مقسمة إلى ثلاثة أجزاء (Tabs) -->
        <div wire:ignore.self class="modal fade" id="detailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-dark py-2">
                        <h6 class="modal-title fw-bold">
                            <i class="bi bi-info-circle me-1"></i> ملف البرنامج التدريبي وسجل الإجراءات
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if($viewingProgram)
                            <!-- أزرار التبويبات الثلاثة -->
                            <ul class="nav nav-pills nav-fill mb-3 bg-light p-1 rounded" id="programTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-1 small fw-bold" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic-pane" type="button" role="tab">
                                        <i class="bi bi-card-checklist me-1"></i> 1. البيانات الأساسية
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-1 small fw-bold" id="objectives-tab" data-bs-toggle="tab" data-bs-target="#objectives-pane" type="button" role="tab">
                                        <i class="bi bi-bullseye me-1"></i> 2. الأهداف والمتطلبات
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-1 small fw-bold" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-pane" type="button" role="tab">
                                        <i class="bi bi-clock-history me-1"></i> 3. سجل الإجراءات
                                    </button>
                                </li>
                            </ul>

                            <!-- محتوى التبويبات -->
                            <div class="tab-content" id="programTabsContent">
                                <!-- الجزء الأول: البيانات الأساسية والقاعات والجدول الزمني -->
                                <div class="tab-pane fade show active" id="basic-pane" role="tabpanel">
                                    <div class="card border shadow-sm">
                                        <div class="card-body small">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <span class="text-muted d-block">اسم البرنامج (عربي):</span>
                                                    <span class="fw-bold text-dark fs-6">{{ $viewingProgram->name_ar }}</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="text-muted d-block">اسم البرنامج (إنجليزي):</span>
                                                    <span class="fw-bold text-dark fs-6">{{ $viewingProgram->name_en ?? '-' }}</span>
                                                </div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <span class="text-muted">كود البرنامج:</span>
                                                    <span class="badge bg-light text-dark border d-block mt-1">{{ $viewingProgram->code }}</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <span class="text-muted">نوع البرنامج:</span>
                                                    <span class="fw-bold text-dark d-block mt-1">{{ $viewingProgram->programType->name ?? '-' }}</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <span class="text-muted">القاعة التدريبية:</span>
                                                    <span class="fw-bold text-dark d-block mt-1">{{ $viewingProgram->hall->name ?? '-' }}</span>
                                                </div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <span class="text-muted">المستوى:</span>
                                                    <span class="fw-bold text-dark d-block">{{ $viewingProgram->level ?? '-' }}</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <span class="text-muted">الاستشاري / المحاضر:</span>
                                                    <span class="fw-bold text-dark d-block">{{ $viewingProgram->consultant_name ?? '-' }}</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <span class="text-muted">عدد الدورات السنوية:</span>
                                                    <span class="fw-bold text-dark d-block">{{ $viewingProgram->courses_count_per_year ?? '-' }} دورات</span>
                                                </div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <span class="text-muted">الساعات التدريبية:</span>
                                                    <span class="fw-bold text-primary">{{ $viewingProgram->training_hours }} ساعة</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="text-muted">المدة بالأيام:</span>
                                                    <span class="fw-bold text-primary">{{ $viewingProgram->duration_days }} أيام</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- الجزء الثاني: الأهداف والمعارف والمتطلبات -->
                                <div class="tab-pane fade" id="objectives-pane" role="tabpanel">
                                    <div class="card border shadow-sm">
                                        <div class="card-body small">
                                            <div class="mb-3">
                                                <span class="text-muted fw-bold d-block text-primary mb-1"><i class="bi bi-lightbulb"></i> الأهداف المعرفية:</span>
                                                <div class="bg-light p-2 rounded text-dark">{{ $viewingProgram->objective_knowledge ?? 'غير متوفر' }}</div>
                                            </div>
                                            <div class="mb-3">
                                                <span class="text-muted fw-bold d-block text-primary mb-1"><i class="bi bi-gear"></i> الأهداف المهارية:</span>
                                                <div class="bg-light p-2 rounded text-dark">{{ $viewingProgram->objective_skills ?? 'غير متوفر' }}</div>
                                            </div>
                                            <div class="mb-3">
                                                <span class="text-muted fw-bold d-block text-primary mb-1"><i class="bi bi-heart"></i> الأهداف السلوكية:</span>
                                                <div class="bg-light p-2 rounded text-dark">{{ $viewingProgram->objective_attitude ?? 'غير متوفر' }}</div>
                                            </div>
                                            <div>
                                                <span class="text-muted fw-bold d-block text-primary mb-1"><i class="bi bi-check-circle"></i> المتطلبات المسبقة:</span>
                                                <div class="bg-light p-2 rounded text-dark">{{ $viewingProgram->prerequisites ?? 'لا توجد متطلبات مسبقة' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- الجزء الثالث: سجل الموافقات والإجراءات السابقة -->
                                <div class="tab-pane fade" id="history-pane" role="tabpanel">
                                    <div class="card border shadow-sm">
                                        <div class="card-body p-0 table-responsive">
                                            <table class="table table-sm table-striped mb-0 text-center small">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>المستخدم</th>
                                                        <th>المرحلة</th>
                                                        <th>القرار</th>
                                                        <th>سبب الرفض / الملاحظات</th>
                                                        <th>الوقت</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($viewingProgram->approvals as $approval)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td class="fw-bold">{{ $approval->user->name ?? '-' }}</td>
                                                            <td><span class="badge bg-secondary">{{ $approval->role_stage }}</span></td>
                                                            <td>
                                                                @if($approval->status == 'approved')
                                                                    <span class="badge bg-success">معتمد</span>
                                                                @elseif($approval->status == 'rejected')
                                                                    <span class="badge bg-danger">مرفوض</span>
                                                                @else
                                                                    <span class="badge bg-warning text-dark">قيد الانتظار</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-start">
                                                                @if($approval->rejectionReason)
                                                                    <span class="text-danger fw-bold">[{{ $approval->rejectionReason->name }}]</span>
                                                                @endif
                                                                {{ $approval->notes }}
                                                            </td>
                                                            <td>{{ $approval->action_at?->format('Y-m-d H:i') }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center py-3 text-muted">لا توجد إجراءات مسجلة حتى الآن.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer py-1">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('close-approval-modal', () => {
            var myModalEl = document.getElementById('approvalModal');
            var modal = bootstrap.Modal.getInstance(myModalEl);
            if (modal) {
                modal.hide();
            }
        });
    </script>
    @endscript
</div>
