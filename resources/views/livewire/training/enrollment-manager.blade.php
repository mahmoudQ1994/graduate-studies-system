<div class="container-fluid px-4 py-4" style="background-color: #f8f9fa; min-height: 100vh;" dir="rtl">

    <!-- رأس الصفحة وزر مشاركة رابط التسجيل -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-clipboard-check-fill fs-5"></i>
                </div>
                <span>إدارة تسجيلاّت المتدربين في الدورات</span>
            </h4>
            <p class="text-muted small mb-0 ms-5">لوحة التحكم والمتابعة المتقدمة لكشوف الحضور، مواقف الاعتماد، والشهادات.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <input type="text" id="publicRegistrationLink" value="{{ route('public.trainings.register') }}" class="d-none">

            <button type="button" onclick="copyRegistrationLink()" class="btn btn-success px-4 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2 fw-semibold text-white transition-all" style="background: linear-gradient(135deg, #28a745, #20c997); border: none;">
                <i class="bi bi-whatsapp fs-5"></i>
                <span>نسخ رابط التسجيل العام</span>
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 border-0 mb-4 bg-success text-white" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <span class="fw-semibold">{{ session('message') }}</span>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- بطاقات الإحصائيات -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-primary border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">إجمالي المتدربين المسجلين</span>
                        <h3 class="fw-bold text-dark mb-0">{{ $totalEnrollmentsCount }}</h3>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-success border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">اجمالى من اجتازوا التدريب </span>
                        <h3 class="fw-bold text-success mb-0">{{ $attendedCount }}</h3>
                    </div>
                    <div class="bg-success-subtle text-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-person-check-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-danger border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">تجاوزوا نسبة الغياب (راسبون)</span>
                        <h3 class="fw-bold text-danger mb-0">{{ $failedAttendanceCount ?? 0 }}</h3>
                    </div>
                    <div class="bg-danger-subtle text-danger rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقة الفلاتر -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <h6 class="fw-bold text-secondary mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-search text-primary"></i>
                <span>فلترة وبحث البيانات</span>
            </h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label text-muted fw-semibold small">بحث سريع:</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 rounded-end-pill pe-3" wire:model.live.debounce.300ms="search" placeholder="الاسم، القومي، الموبايل...">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted fw-semibold small">اسم البرنامج:</label>
                    <select class="form-select form-select-sm bg-light rounded-pill px-3" wire:model.live="filter_program_id">
                        <option value="">-- كل البرامج التدريبية --</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}">{{ $prog->name_ar }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted fw-semibold small">تاريخ التنفيذ:</label>
                    <input type="date" class="form-control form-control-sm bg-light rounded-pill px-3" wire:model.live="filter_date">
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted fw-semibold small">موقف الاعتماد:</label>
                    <select class="form-select form-select-sm bg-light rounded-pill px-3" wire:model.live="filter_status">
                        <option value="">-- الكل --</option>
                        <option value="approved">مقبول</option>
                        <option value="rejected">مستبعد</option>
                        <option value="pending">قيد المراجعة</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted fw-semibold small">حالة الحضور:</label>
                    <select class="form-select form-select-sm bg-light rounded-pill px-3" wire:model.live="filter_attendance">
                        <option value="">-- الكل --</option>
                        <option value="pending">قيد الانتظار</option>
                        <option value="passed">اجتاز الحضور (مقبول)</option>
                        <option value="failed">تجاوز نسبة الغياب (راسب)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول البيانات -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 65vh; overflow-x: auto;">
                <table class="table table-hover align-middle mb-0 text-center" style="font-size: 0.88rem; white-space: nowrap;">
                    <thead class="table-light text-secondary sticky-top shadow-sm" style="z-index: 1;">
                        <tr style="height: 50px;">
                            <th class="py-3 px-3">م</th>
                            <th class="text-start py-3 px-3">الاسم</th>
                            <th class="py-3 px-3">الرقم القومي</th>
                            <th class="py-3 px-3">الوظيفة</th>
                            <th class="py-3 px-3">جهة العمل</th>
                            <th class="text-start py-3 px-3">اسم البرنامج</th>
                            <th class="py-3 px-3">بداية البرنامج</th>
                            <th class="py-3 px-3">موقف الاعتماد والقبول</th>
                            <th class="py-3 px-3">موقف الحضور</th>
                            <th class="py-3 px-3">موقف المتدرب نهاية البرنامج</th>
                            <th class="py-3 px-3">الشهادة</th>
                            <th class="py-3 px-3">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $index => $row)
                            <tr style="height: 60px;" class="border-bottom transition-all">
                                <td class="text-muted px-3 fw-semibold">{{ $enrollments->firstItem() + $index }}</td>
                                <td class="fw-bold text-dark text-start px-3">{{ $row->trainee_name }}</td>
                                <td class="font-monospace text-secondary px-3">{{ $row->national_id }}</td>
                                <td class="text-secondary px-3">{{ $row->job_title ?? '---' }}</td>
                                <td class="text-secondary px-3">{{ $row->workplace ?? '---' }}</td>
                                <td class="fw-semibold text-primary text-start px-3">
                                    {{ $row->program_name }}
                                </td>
                                <td class="font-monospace text-muted px-3">{{ $row->start_date }}</td>

                                <!-- موقف الاعتماد والقبول المبدئي -->
                                <td class="px-3">
                                    @if(($row->status ?? 'approved') == 'approved')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">مقبول</span>
                                    @elseif(($row->status ?? '') == 'rejected')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-bold" title="سبب الاستبعاد: {{ $row->rejection_reason ?? 'غير محدد' }}">
                                            مستبعد <i class="bi bi-info-circle ms-1"></i>
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill fw-bold">قيد المراجعة</span>
                                    @endif
                                </td>

                                <!-- حالة الحضور -->
                                <td class="px-3">
                                    @if($row->attendance_status == 'passed')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">
                                            <i class="bi bi-check-circle me-1"></i> اجتاز الحضور
                                        </span>
                                    @elseif($row->attendance_status == 'failed')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-bold">
                                            <i class="bi bi-exclamation-triangle me-1"></i> تجاوز نسبة الغياب
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill">قيد الانتظار</span>
                                    @endif
                                </td>

                                <td class="px-3">
                                    <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill">{{ $row->evaluation_status ?: 'قيد المتابعة' }}</span>
                                </td>

                                <td class="px-3">
                                    @if($row->certificate_issued)
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 rounded-pill fw-bold" title="كود: {{ $row->certificate_code }}">تم الإصدار</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill">لم تصدر</span>
                                    @endif
                                </td>

                                <td class="px-3">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" wire:click="openEditModal({{ $row->id }})" class="btn btn-outline-primary btn-sm rounded-circle p-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="تعديل الموقف والإجراء">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" wire:click="deleteEnrollment({{ $row->id }})" wire:confirm="هل أنت متأكد من إزالة هذا المتدرب من الدورة؟" class="btn btn-outline-danger btn-sm rounded-circle p-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-5 text-muted">
                                    <div class="py-4">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-black-50"></i>
                                        <p class="mb-0 fw-semibold">لا توجد تسجيلات مطابقة للبحث أو الفلترة الحالية.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($enrollments->hasPages())
            <div class="card-footer bg-white py-3 border-top">
                {{ $enrollments->links() }}
            </div>
        @endif
    </div>

    <!-- مودال تعديل حالة التسجيل والقبول/الاستبعاد -->
    <div wire:ignore.self class="modal fade" id="enrollmentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-bold fs-6 d-flex align-items-center gap-2">
                        <i class="bi bi-sliders"></i>
                        <span>تعديل موقف الاعتماد وتفاصيل المتدرب</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <!-- خيار القبول أو الاستبعاد -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">موقف الاعتماد والقبول:</label>
                        <select class="form-select form-select-sm rounded-pill px-3 py-2" wire:model.live="status">
                            <option value="approved">قبول المتدرب (مقبول)</option>
                            <option value="rejected">استبعاد المتدرب (مستبعد)</option>
                            <option value="pending">قيد المراجعة</option>
                        </select>
                    </div>

                    <!-- حقل سبب الاستبعاد عند التحديد -->
                    @if($status === 'rejected')
                        <div class="mb-3">
                            <label class="form-label fw-bold text-danger small">سبب الاستبعاد:</label>
                            <input type="text" class="form-control form-control-sm rounded-pill px-3 py-2 border-danger" wire:model="rejection_reason" placeholder="مثال: حصل على الدورة سابقاً في نفس العام / غير مطابق للشروط...">
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">موقف الموافقة على الحضور:</label>
                        <select class="form-select form-select-sm rounded-pill px-3 py-2" wire:model="attendance_status">
                            <option value="pending">قيد الانتظار</option>
                            <option value="passed">اجتاز الحضور (مقبول)</option>
                            <option value="failed">تجاوز نسبة الغياب (راسب)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">موقف المتدرب نهاية البرنامج (التقييم/الاجتياز):</label>
                        <input type="text" class="form-control form-control-sm rounded-pill px-3 py-2" wire:model="evaluation_status" placeholder="اجتاز بنجاح، ممتاز، لم يجتاز...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">حالة الشهادة:</label>
                        <select class="form-select form-select-sm rounded-pill px-3 py-2" wire:model="certificate_issued">
                            <option value="0">لم تصدر بعد</option>
                            <option value="1">تم إصدار الشهادة</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-white py-3 px-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" wire:click="updateEnrollment" class="btn btn-success btn-sm px-4 rounded-pill fw-bold">حفظ التعديلات</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table-responsive::-webkit-scrollbar { height: 8px; width: 8px; }
    .table-responsive::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
    .table-responsive::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
</style>
@endpush

@script
<script>
    function copyRegistrationLink() {
        var copyText = document.getElementById("publicRegistrationLink");
        copyText.classList.remove('d-none');
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        try {
            navigator.clipboard.writeText(copyText.value);
            alert('تم نسخ رابط التسجيل العام بنجاح! يمكنك لصقه الآن في رسائل الواتس آب.');
        } catch (err) {
            document.execCommand("copy");
            alert('تم نسخ رابط التسجيل بنجاح!');
        }
        copyText.classList.add('d-none');
    }

    $wire.on('show-enrollment-modal', () => {
        var myModal = new bootstrap.Modal(document.getElementById('enrollmentModal'));
        myModal.show();
    });
    $wire.on('close-enrollment-modal', () => {
        var myModalEl = document.getElementById('enrollmentModal');
        var modal = bootstrap.Modal.getInstance(myModalEl);
        if (modal) modal.hide();
    });
</script>
@endscript
