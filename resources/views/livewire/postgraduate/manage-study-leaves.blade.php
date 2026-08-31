<div class="container-fluid py-2">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div>
            <h6 class="fw-bold mb-2 text-primary" style="font-size: 22px">
                <i class="bi bi-briefcase-fill me-2"></i>إدارة الإجازات والتفرغ الدراسي
            </h6>
            <p class="text-muted small mb-2" style="font-size: 0.9rem;">متابعة الإجازات الدراسية، النسب الإحصائية للتفرغ، وتنبيهات استلام العمل.</p>
        </div>
    </div>

    <!-- 📊 كروت الإحصائيات (تصميم مدمج ومريح للعين) -->
    <div class="row g-2 mb-3">
        <!-- كارت إجمالي الحاصلين على تفرغ -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 text-white h-100 position-relative overflow-hidden"
                 style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                <div class="card-body p-2 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-white-65 d-block mb-0" style="font-size: 1rem; font-weight: 700;">إجمالي الحاصلين على تفرغ</span>
                        <h4 class="fw-bold mb-0 font-monospace text-white">{{ $stats['total_with_leave'] }}</h4>
                        <span class="text-white-50 d-block" style="font-size: 0.9rem;">من أصل {{ $stats['total_registered'] }} مرشح مسجل</span>
                    </div>
                    <div class="rounded-3 bg-white bg-opacity-25 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-journal-check fs-4 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- كارت تفرغ بمرتب -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 border-start border-3 border-success">
                <div class="card-body p-2 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-0" style="font-size: 1rem; font-weight: 700;">تفرغ بمرتب</span>
                        <h4 class="fw-bold mb-0 text-success font-monospace">{{ $stats['paid_leave'] }}</h4>
                        <span class="text-muted d-block" style="font-size: 0.9rem;">إجمالي المتفرغين بمرتب</span>
                    </div>
                    <div class="rounded-3 bg-success bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-cash-stack fs-4 text-success"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- كارت تفرغ بدون مرتب -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 border-start border-3 border-warning">
                <div class="card-body p-2 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-0" style="font-size: 1rem; font-weight: 700;">تفرغ بدون مرتب</span>
                        <h4 class="fw-bold mb-0 text-warning font-monospace">{{ $stats['unpaid_leave'] }}</h4>
                        <span class="text-muted d-block" style="font-size: 0.9rem;">إجازة بدون مرتب</span>
                    </div>
                    <div class="rounded-3 bg-warning bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-person-exclamation fs-4 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- كارت بدون تفرغ -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 border-start border-3 border-secondary">
                <div class="card-body p-2 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-0" style="font-size: 1rem; font-weight: 700;">على رأس العمل</span>
                        <h4 class="fw-bold mb-0 text-secondary font-monospace">{{ $stats['no_leave'] }}</h4>
                        <span class="text-muted d-block" style="font-size: 0.9rem;">بدون تفرغ دراسي</span>
                    </div>
                    <div class="rounded-3 bg-secondary bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-person-workspace fs-4 text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Smart Notice Banner (تنبيه انتهاء التفرغ) -->
    @if($expiringLeaves->count() > 0)
        <div class="alert alert-warning border-0 shadow-sm rounded-3 mb-3 p-2 px-3 bg-warning bg-opacity-10 text-dark">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <div class="bg-warning text-dark rounded-circle p-1 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-bell-fill fs-6"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size: 0.85rem;">تنبيهات استلام العمل العاجلة!</h6>
                        <p class="mb-0 small" style="font-size: 0.75rem;">
                            يوجد <span class="badge bg-danger rounded-pill px-2 fs-7">{{ $expiringLeaves->count() }}</span> مرشحين انتهت فترة تفرغهم أو متبقي عليها 48 ساعة أو أقل.
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" wire:click="toggleExpiringFilter" class="btn btn-sm {{ $filter_expiring_soon ? 'btn-danger' : 'btn-outline-dark' }} py-1 px-2 fw-bold rounded-2" style="font-size: 0.75rem;">
                        <i class="bi {{ $filter_expiring_soon ? 'bi-x-circle' : 'bi-funnel' }} me-1"></i>
                        {{ $filter_expiring_soon ? 'إلغاء التصفية' : 'تصفية هؤلاء فقط' }}
                    </button>
                    <button type="button" class="btn btn-sm btn-dark py-1 px-2 fw-bold rounded-2 shadow-sm" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#expiringDetailsModal">
                        <i class="bi bi-eye me-1"></i>عرض القائمة بالتفصيل
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-2">
        <div class="card-body p-2">
            <div class="row g-2">
                <div class="col-md-8">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0 rounded-start-2"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-0 bg-light rounded-end-2" placeholder="ابحث باسم المرشح، الرقم القومي، أو رقم الموبايل...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select wire:model.live="filter_leave_type" class="form-select form-select-sm border-0 bg-light rounded-2 fw-bold">
                        <option value="">-- جميع حالات التفرغ --</option>
                        <option value="تفرغ بمرتب">تفرغ بمرتب</option>
                        <option value="تفرغ بدون مرتب">تفرغ بدون مرتب</option>
                        <option value="بدون تفرغ">بدون تفرغ</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

<!-- Main Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap" style="font-size: 13px;">
                    <thead class="text-white text-center "
                    style="background: #111827 !important; border-bottom: 3px solid #3b82f6;">                    <tr>
                        <th class="py-3 px-3">#</th>
                        <th class="py-3 text-start ps-4">اسم المرشح</th>
                        <th class="py-3">الوظيفة</th>
                        <th class="py-3">الرقم القومي</th>
                        <th class="py-3">نوع الدراسة</th>
                        <th class="py-3">تاريخ القيد</th>
                        <th class="py-3">اسم الجامعة</th>
                        <th class="py-3">جهة العمل</th>
                        <th class="py-3">موقف الدراسة</th>
                        <th class="py-3">نوع إجازة التفرغ</th>
                        <th class="py-3">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrations as $reg)
                        <tr>
                            <td class="px-3 fw-bold text-muted text-center">{{ $loop->iteration + ($registrations->currentPage() - 1) * $registrations->perPage() }}</td>
                            <td class="fw-bold text-dark text-start ps-4">{{ $reg->healthProfessional->name ?? 'غير محدد' }}</td>
                            <td class="text-center"><span class="badge bg-light text-dark border px-2 py-1">{{ $reg->healthProfessional->profession ?? '-' }}</span></td>
                            <td class="font-monospace text-center text-muted">{{ $reg->healthProfessional->national_id ?? '-' }}</td>
                            <td class="text-center fw-medium text-secondary">{{ $reg->required_degree ?? '-' }}</td>
                            <td class="font-monospace text-center text-muted">{{ $reg->registration_date ?? '-' }}</td>
                            <td class="text-center">{{ $reg->required_university ?? '-' }}</td>
                            <td class="text-center text-muted">{{ $reg->healthProfessional->secondment_facility ?? '-' }}</td>

                            <!-- عمود موقف الدراسة -->
                            <td class="text-center">
                                @php
                                    $status = $reg->study_status;
                                    $badgeClass = 'bg-secondary-subtle text-secondary border';
                                    if(str_contains($status, 'مستمر')) $badgeClass = 'bg-info-subtle text-info border border-info-subtle';
                                    elseif(str_contains($status, 'حصل')) $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                                    elseif(str_contains($status, 'اعتذار') || str_contains($status, 'إلغاء')) $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                                @endphp
                                <span class="badge {{ $badgeClass }} px-2.5 py-1 rounded-pill fw-normal">
                                    {{ $status ?? 'غير محدد' }}
                                </span>
                            </td>

                            <!-- نوع إجازة التفرغ -->
                            <td class="text-center">
                                @if($reg->study_leave_type === 'تفرغ بمرتب')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill">بمرتب</span>
                                @elseif($reg->study_leave_type === 'تفرغ بدون مرتب')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill">بدون مرتب</span>
                                @else
                                    <span class="badge bg-light text-muted border px-2.5 py-1 rounded-pill">{{ $reg->study_leave_type ?? 'بدون تفرغ' }}</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <button type="button"
                                        wire:click="openLeavesModal({{ $reg->id }})"
                                        data-bs-toggle="modal"
                                        data-bs-target="#leavesHistoryModal"
                                        class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 shadow-none"
                                        style="font-size: 11px;">
                                    <i class="bi bi-gear-fill me-1"></i>الإدارة
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <i class="bi bi-inbox fs-1 text-secondary opacity-50 mb-2 d-block"></i>
                                    لا توجد بيانات مطابقة للعرض حالياً.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 bg-light border-top d-flex justify-content-center">
            {{ $registrations->links() }}
        </div>
    </div>
</div>

    <!-- Modal 1: التفاصيل الكاملة للتفرغات المنتهية -->
    <div wire:ignore.self class="modal fade" id="expiringDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-danger bg-gradient text-white py-2 px-3">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-alarm-fill me-2"></i>المرشحون المنتهية إجازاتهم أو متبقي عليها أقل من 30 يوم
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 bg-light-subtle">
                    <div class="table-responsive rounded-3 border bg-white shadow-sm">
                        <table class="table table-hover align-middle mb-0" style="font-size: 12px;">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="py-2">اسم المرشح</th>
                                    <th class="py-2">الوظيفة</th>
                                    <th class="py-2">جهة العمل</th>
                                    <th class="py-2">الموبايل</th>
                                    <th class="py-2 text-center">بداية التفرغ</th>
                                    <th class="py-2 text-center">نهاية التفرغ</th>
                                    <th class="py-2 text-center">الحالة</th>
                                    <th class="py-2 text-center">الإجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expiringLeaves as $exp)
                                    @php
                                        $lastLeave = $exp->studyLeaves->first();
                                        $endDate = \Carbon\Carbon::parse($lastLeave->end_date ?? now());
                                        $isPast = $endDate->isPast();
                                    @endphp
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $exp->healthProfessional->name ?? 'غير محدد' }}</td>
                                        <td>{{ $exp->healthProfessional->job_title ?? $exp->study_program ?? '-' }}</td>
                                        <td>{{ $exp->university ?? $exp->healthProfessional->workplace ?? '-' }}</td>
                                        <td class="font-monospace text-primary fw-bold">{{ $exp->healthProfessional->phone ?? $exp->phone ?? '-' }}</td>
                                        <td class="text-center font-monospace">{{ $lastLeave->start_date ?? '-' }}</td>
                                        <td class="text-center font-monospace fw-bold text-danger">{{ $lastLeave->end_date ?? '-' }}</td>
                                        <td class="text-center">
                                            @if($isPast)
                                                <span class="badge bg-danger text-white rounded-pill px-2">منتهي بالفعل</span>
                                            @else
                                                <span class="badge bg-warning text-dark rounded-pill px-2">متبقي أقل من 30 يوم</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                    wire:click="openLeavesModal({{ $exp->id }})"
                                                    onclick="bootstrap.Modal.getInstance(document.getElementById('expiringDetailsModal')).hide(); (new bootstrap.Modal(document.getElementById('leavesHistoryModal'))).show();"
                                                    class="btn btn-sm btn-primary rounded-2 py-1 px-2 shadow-sm"
                                                    style="font-size: 11px;">
                                                <i class="bi bi-pencil-square me-1"></i>إدارة / استلام
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">لا توجد تنبيهات حالية.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 2: النافذة الرئيسية لإدارة وتعديل الإجازات واستلام العمل -->
    <div wire:ignore.self class="modal fade" id="leavesHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-primary bg-gradient text-white py-2 px-3">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-person-badge me-2"></i>إدارة الإجازات واستلام العمل: <span class="text-warning">{{ $selectedReg->healthProfessional->name ?? '' }}</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 bg-light-subtle">

                    <!-- Alerts -->
                    @if (session()->has('modal_success'))
                        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-2 p-2 px-3 d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2 fs-6 text-success"></i>
                            <div style="font-size: 0.85rem;">{{ session('modal_success') }}</div>
                        </div>
                    @endif

                    <!-- نموذج تسجيل/تعديل الإجازات Card -->
                    <div class="card border-0 shadow-sm rounded-3 mb-3">
                        <div class="card-header bg-white border-0 py-2 px-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold {{ $editing_leave_id ? 'text-warning' : 'text-primary' }} mb-0" style="font-size: 0.85rem;">
                                <i class="bi {{ $editing_leave_id ? 'bi-pencil-square' : 'bi-plus-circle-fill' }} me-1"></i>
                                {{ $editing_leave_id ? 'تعديل بيانات التفرغ المسجل' : 'تسجيل إجازة / تفرغ جديد' }}
                            </h6>
                            @if($editing_leave_id)
                                <button type="button" wire:click="resetLeaveForm" class="btn btn-sm btn-outline-secondary rounded-2 py-0 px-2" style="font-size: 0.75rem;">
                                    <i class="bi bi-x-circle me-1"></i>إلغاء التعديل
                                </button>
                            @endif
                        </div>
                        <div class="card-body p-3 pt-0">
                            <form wire:submit.prevent="saveLeave">
                                <div class="row g-2 mb-2">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold mb-1" style="font-size: 0.75rem;">نوع التفرغ <span class="text-danger">*</span></label>
                                        <select wire:model="leave_type" class="form-select form-select-sm border-light-subtle rounded-2 fw-bold">
                                            <option value="تفرغ بمرتب">تفرغ بمرتب</option>
                                            <option value="تفرغ بدون مرتب">تفرغ بدون مرتب</option>
                                            <option value="بدون تفرغ">بدون تفرغ</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold mb-1" style="font-size: 0.75rem;">تاريخ البداية <span class="text-danger">*</span></label>
                                        <input type="date" wire:model.live="start_date" class="form-control form-control-sm rounded-2">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold mb-1" style="font-size: 0.75rem;">تاريخ النهاية المتوقع</label>
                                        <input type="date" wire:model.live="end_date" class="form-control form-control-sm rounded-2">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.75rem;">المدة الحسابية المتوقعة</label>
                                        <input type="text" class="form-control form-control-sm bg-light font-monospace text-primary fw-bold rounded-2 border-0" value="{{ $calculated_duration_text }}" readonly>
                                    </div>
                                </div>
                                <div class="row g-2 mb-1">
                                    <div class="col-md-9">
                                        <label class="form-label small fw-bold mb-1" style="font-size: 0.75rem;">ملاحظات / رقم القرار الإداري</label>
                                        <input type="text" wire:model="notes" class="form-control form-control-sm rounded-2" placeholder="أدخل بيانات القرار الإداري للتفرغ...">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-sm {{ $editing_leave_id ? 'btn-warning text-dark' : 'btn-primary' }} w-100 fw-bold rounded-2 shadow-sm">
                                            <i class="bi {{ $editing_leave_id ? 'bi-check-all' : 'bi-save' }} me-1"></i>
                                            {{ $editing_leave_id ? 'تحديث التفرغ' : 'حفظ التفرغ' }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- سجل الإجازات واستلام العمل Card -->
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-header bg-white border-bottom py-2 px-3">
                            <h6 class="fw-bold text-secondary mb-0" style="font-size: 0.85rem;">
                                <i class="bi bi-clock-history me-1"></i>سجل الإجازات المسجلة واستلام العمل
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 11px;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="py-2 text-center">نوع التفرغ</th>
                                            <th class="py-2 text-center">تاريخ البداية</th>
                                            <th class="py-2 text-center">تاريخ النهاية</th>
                                            <th class="py-2 text-center">مدة التفرغ الحالية</th>
                                            <th class="py-2">الملاحظات</th>
                                            <th class="py-2 text-center" style="width: 240px;">تسجيل استلام العمل</th>
                                            <th class="py-2 text-center">العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($selectedReg && $selectedReg->studyLeaves->count() > 0)
                                            @foreach($selectedReg->studyLeaves as $leave)
                                                <tr>
                                                    <td class="text-center fw-bold">{{ $leave->leave_type }}</td>
                                                    <td class="text-center font-monospace">{{ $leave->start_date }}</td>
                                                    <td class="text-center font-monospace">{{ $leave->end_date ?? 'مستمر' }}</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1 rounded-pill">
                                                            {{ \App\Livewire\Postgraduate\ManageStudyLeaves::getFormattedDuration($leave->start_date, $leave->end_date) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $leave->notes ?? '-' }}</td>
                                                    <td class="text-center bg-light-subtle">
                                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                                            <input type="date" wire:model="return_dates.{{ $leave->id }}"
                                                                class="form-control form-control-sm py-0 font-monospace rounded-2" style="width: 115px; font-size: 10px;">
                                                            <button type="button" wire:click="returnToWork({{ $leave->id }})"
                                                                class="btn btn-sm btn-outline-success py-0 px-2 rounded-2" style="font-size: 10px;">
                                                                <i class="bi bi-box-arrow-in-right me-1"></i>استلام
                                                            </button>
                                                        </div>
                                                        @error("return_dates.{$leave->id}")
                                                            <div class="text-danger small mt-1" style="font-size: 9px;">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" wire:click="editLeave({{ $leave->id }})" class="btn btn-outline-primary py-0 px-2 rounded-start-2"><i class="bi bi-pencil"></i></button>
                                                            <button type="button" wire:click="deleteLeave({{ $leave->id }})" wire:confirm="هل أنت تأكد من الحذف؟" class="btn btn-outline-danger py-0 px-2 rounded-end-2"><i class="bi bi-trash"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="7" class="text-center py-3 text-muted">لا توجد إجازات مسجلة سابقاً لهذا المرشح.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript لاستقبال حدث فتح المودال مباشرة من Livewire (في حال تم استدعائه من السيرفر) -->
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('open-leaves-modal', () => {
            const modalEl = document.getElementById('leavesHistoryModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        });
    });
</script>
