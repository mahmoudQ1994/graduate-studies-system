<div class="container-fluid px-4 py-4" style="background-color: #f4f6f9; min-height: 100vh;">

    <!-- رأس الصفحة -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 bg-white p-3 px-4 rounded-4 shadow-sm border border-light">
        <div>
            <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <div class="bg-success-subtle text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-calendar-check-fill fs-6"></i>
                </div>
                <span>إدارة الحضور والغياب اليومي</span>
            </h5>
            <p class="text-muted small mb-0 ms-5">تسجيل ومتابعة حضور وغياب المتدربين يوماً بيوماً ضمن تواريخ البرنامج المعتمدة.</p>
        </div>

@if($selected_schedule_id)
            <div class="d-flex gap-2">
                <button type="button" wire:click="generateQrCodeLink" class="btn btn-outline-primary px-3 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2 fw-semibold btn-sm">
                    <i class="bi bi-qr-code fs-6"></i>
                    <span>إظهار كيو آر الحضور الذاتي</span>
                </button>
                <button type="button" wire:click="generateEvalQrCodeLink" class="btn btn-outline-warning text-dark px-3 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2 fw-semibold btn-sm">
                    <i class="bi bi-star-fill text-warning fs-6"></i>
                    <span>إظهار كيو آر استمارة التقييم والشهادات</span>
                </button>
                <button type="button" wire:click="saveAttendance" class="btn btn-success px-4 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2 fw-semibold btn-sm">
                    <i class="bi bi-check2-all fs-6"></i>
                    <span>حفظ كشف حضور اليوم</span>
                </button>
                <button type="button" wire:click="calculateAttendanceStatuses" wire:confirm="هل أنت متأكد من احتساب واعتماد حالات الحضور لجميع المتدربين بناءً على شرط 25%؟" class="btn btn-outline-warning text-dark px-3 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2 fw-semibold btn-sm">
                    <i class="bi bi-percent fs-6"></i>
                    <span>احتساب حالات الحضور (25%)</span>
                </button>
            </div>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 mb-3 shadow-sm py-2 px-3 small" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 mb-3 shadow-sm py-2 px-3 small" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- الفلاتر العلوية -->
    <div class="card border-0 shadow-sm rounded-4 mb-3 bg-white">
        <div class="card-body p-3 px-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-4 position-relative">
                    <label class="form-label fw-bold text-secondary small mb-1">1. اسم البرنامج التدريبي:</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 rounded-end-pill px-3 py-2" wire:model.live.debounce.300ms="programSearch" placeholder="ابحث باسم البرنامج...">
                    </div>

                    @if(strlen(trim($programSearch)) >= 1 && !empty($this->matchingPrograms) && $this->matchingPrograms->count() > 0 && !$selected_program_id)
                        <ul class="list-group position-absolute w-100 shadow-sm rounded-4 mt-1 overflow-hidden" style="z-index: 1050; max-height: 200px; overflow-y: auto; font-size: 0.85rem;">
                            @foreach($this->matchingPrograms as $prog)
                                <li wire:click="selectProgram({{ $prog->id }}, '{{ $prog->name_ar }}')" class="list-group-item list-group-item-action py-2 px-3 bg-white border-0 border-bottom" style="cursor: pointer;">
                                    <i class="bi bi-journal-code text-primary me-2"></i> {{ $prog->name_ar }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-secondary small mb-1">2. تاريخ التنفيذ (الموعد):</label>
                    <select class="form-select form-select-sm rounded-pill border-light bg-light px-3 py-2" wire:model.live="selected_schedule_id" @if(!$selected_program_id) disabled @endif>
                        <option value="">-- اختر موعد البرنامج --</option>
                        @foreach($availableSchedules as $sch)
                            <option value="{{ $sch->id }}">من {{ $sch->start_date }} إلى {{ $sch->end_date }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-secondary small mb-1">3. تاريخ اليوم التدريبي:</label>
                    <input type="date"
                           class="form-control form-control-sm rounded-pill border-light bg-light px-3 py-2"
                           wire:model.live="attendance_date"
                           @if($selectedScheduleInfo)
                               min="{{ $selectedScheduleInfo->start_date }}"
                               max="{{ $selectedScheduleInfo->end_date }}"
                           @else
                               disabled
                           @endif>
                    @if($selectedScheduleInfo)
                        <div class="form-text text-muted text-nowrap px-2" style="font-size: 0.75rem;">
                            مسموح بين: <strong>{{ $selectedScheduleInfo->start_date }}</strong> و <strong>{{ $selectedScheduleInfo->end_date }}</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقات الإحصائيات المدمجة والمريحة للعين -->
    @if($selectedScheduleInfo)
        <div class="row g-3 mb-3">
            <!-- معلومات الفترة التدريبية -->
            <div class="col-xl-4 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-primary border-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted fw-bold" style="font-size: 0.78rem;">بيانات الفترة التدريبية</span>
                            @if($dayInfo && $dayInfo['is_valid'])
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                                    اليوم الحالي: ({{ $dayInfo['current_day'] }} من {{ $dayInfo['total_days'] }})
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border px-2 py-1 rounded-pill" style="font-size: 0.72rem;">خارج الفترة</span>
                            @endif
                        </div>
                        <h6 class="fw-bold text-dark mb-2 text-truncate">{{ $selectedScheduleInfo->program_name }}</h6>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top text-secondary" style="font-size: 0.78rem;">
                        <span>البداية: <strong>{{ $selectedScheduleInfo->start_date }}</strong></span>
                        <span>النهاية: <strong>{{ $selectedScheduleInfo->end_date }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- بطاقات المؤشرات المصغرة -->
            <div class="col-xl-8 col-lg-7">
                <div class="row g-2 h-100">
                    <div class="col-3">
                        <div class="card border-0 shadow-sm rounded-4 p-2.5 bg-white h-100 text-center d-flex flex-column justify-content-center border-bottom border-success border-3">
                            <span class="text-muted fw-semibold" style="font-size: 0.75rem;">الحاضرون</span>
                            <h5 class="fw-bold text-success mb-0 mt-1">{{ $stats['present'] }}</h5>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card border-0 shadow-sm rounded-4 p-2.5 bg-white h-100 text-center d-flex flex-column justify-content-center border-bottom border-danger border-3">
                            <span class="text-muted fw-semibold" style="font-size: 0.75rem;">الغائبون</span>
                            <h5 class="fw-bold text-danger mb-0 mt-1">{{ $stats['absent'] }}</h5>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card border-0 shadow-sm rounded-4 p-2.5 bg-white h-100 text-center d-flex flex-column justify-content-center border-bottom border-warning border-3">
                            <span class="text-muted fw-semibold" style="font-size: 0.75rem;">أعذار</span>
                            <h5 class="fw-bold text-warning mb-0 mt-1">{{ $stats['excused'] }}</h5>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card border-0 shadow-sm rounded-4 p-2.5 bg-white h-100 text-center d-flex flex-column justify-content-center border-bottom border-primary border-3">
                            <span class="text-muted fw-semibold" style="font-size: 0.75rem;">نسبة الحضور</span>
                            <h5 class="fw-bold text-primary mb-0 mt-1" style="font-size: 1.1rem;">
                                @if($stats['total'] > 0)
                                    {{ round((($stats['present'] + $stats['excused']) / $stats['total']) * 100) }}%
                                @else
                                    0%
                                @endif
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- جدول إدارة الحضور مع السكرول الداخلي والهيدر الثابت المنظم -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-body p-0">
            @if(!$selected_schedule_id)
                <div class="text-center py-5 text-muted">
                    <div class="py-4">
                        <i class="bi bi-calendar2-check fs-1 d-block mb-2 text-primary opacity-50"></i>
                        <h6 class="fw-bold text-secondary">يرجى اختيار البرنامج والموعد لعرض كشف الحضور والغياب اليومي.</h6>
                    </div>
                </div>
            @else
                <div class="table-responsive" style="max-height: 55vh; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0 text-center" style="font-size: 0.85rem; white-space: nowrap;">
                        <thead class="sticky-top shadow-sm" style="z-index: 10; background-color: #f8f9fa !important;">
                            <tr style="height: 45px; background-color: #f8f9fa !important;">
                                <th class="py-2 px-3 text-secondary" style="background-color: #f8f9fa !important;">م</th>
                                <th class="text-start py-2 px-3 text-secondary" style="background-color: #f8f9fa !important;">اسم المتدرب</th>
                                <th class="py-2 px-3 text-secondary" style="background-color: #f8f9fa !important;">الرقم القومي</th>
                                <th class="py-2 px-3 text-secondary" style="background-color: #f8f9fa !important;">الوظيفة</th>
                                <th class="py-2 px-3 text-secondary" style="width: 260px; background-color: #f8f9fa !important;">حالة الحضور اليومي</th>
                                <th class="py-2 px-3 text-secondary" style="background-color: #f8f9fa !important;">المسؤول عن التسجيل</th>
                                <th class="py-2 px-3 text-secondary" style="background-color: #f8f9fa !important;">وقت الإدخال</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($approvedTrainees as $index => $row)
                                <tr style="height: 55px;" class="border-bottom">
                                    <td class="text-muted px-3 fw-semibold">{{ $approvedTrainees->firstItem() + $index }}</td>
                                    <td class="fw-bold text-dark text-start px-3">{{ $row->name_ar }}</td>
                                    <td class="font-monospace text-secondary px-3">{{ $row->national_id }}</td>
                                    <td class="text-secondary px-3">{{ $row->job_title ?? '---' }}</td>
                                    <td class="px-3">
                                        <div class="btn-group w-100 shadow-sm rounded-pill overflow-hidden" role="group">
                                            <input type="radio" class="btn-check" name="attendance_{{ $row->trainee_id }}" id="present_{{ $row->trainee_id }}" value="present" wire:model.live="attendanceStatuses.{{ $row->trainee_id }}">
                                            <label class="btn btn-outline-success btn-sm fw-bold px-2 py-1" style="font-size: 0.78rem;" for="present_{{ $row->trainee_id }}">حاضر</label>

                                            <input type="radio" class="btn-check" name="attendance_{{ $row->trainee_id }}" id="absent_{{ $row->trainee_id }}" value="absent" wire:model.live="attendanceStatuses.{{ $row->trainee_id }}">
                                            <label class="btn btn-outline-danger btn-sm fw-bold px-2 py-1" style="font-size: 0.78rem;" for="absent_{{ $row->trainee_id }}">غائب</label>

                                            <input type="radio" class="btn-check" name="attendance_{{ $row->trainee_id }}" id="excused_{{ $row->trainee_id }}" value="excused" wire:model.live="attendanceStatuses.{{ $row->trainee_id }}">
                                            <label class="btn btn-outline-warning btn-sm fw-bold px-2 py-1" style="font-size: 0.78rem;" for="excused_{{ $row->trainee_id }}">عذر</label>
                                        </div>
                                    </td>
                                    <td class="text-primary fw-semibold small px-3">{{ $row->creator_name }}</td>
                                    <td class="font-monospace text-muted small px-3">{{ $row->recorded_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <p class="mb-0 fw-semibold">لا يوجد متدربين مقبولين في هذا الموعد.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if($selected_schedule_id && $approvedTrainees->hasPages())
            <div class="card-footer bg-white py-2 border-top">
                {{ $approvedTrainees->links() }}
            </div>
        @endif
    </div>
    <!-- Modal عرض QR Code والرابط -->
    @if($showQrModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1060;" dir="rtl">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 p-3 bg-white">
                    <div class="modal-header border-0 pb-0">
                        <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-qr-code text-primary fs-5"></i>
                            <span>كود وحفظ رابط الحضور الذاتي للمتدربين</span>
                        </h6>
                        <button type="button" class="btn-close" wire:click="closeQrModal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <p class="text-muted small mb-3">يمكن للمتدربين مسح الكود التالي بهواتفهم لتسجيل حضورهم الذاتي فوراً في هذه الجلسة:</p>

                        <!-- توليد باركود تفاعلي عبر خدمة خارجية مجانية تعتمد على الرابط -->
                        <div class="bg-light p-3 rounded-4 d-inline-block border mb-3 shadow-sm">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($generatedQrUrl) }}" alt="QR Code" class="rounded-3">
                        </div>

                        <div class="input-group input-group-sm mb-2">
                            <input type="text" class="form-control bg-light text-center font-monospace" value="{{ $generatedQrUrl }}" readonly id="qrLinkInput">
                            <button class="btn btn-outline-secondary px-3" type="button" onclick="navigator.clipboard.writeText(document.getElementById('qrLinkInput').value); alert('تم نسخ الرابط بنجاح!');">
                                <i class="bi bi-clipboard"></i> نسخ الرابط
                            </button>
                        </div>
                        <span class="text-muted" style="font-size: 0.75rem;">الرابط صالح ومخصص لهذا البرنامج والموعد فقط.</span>
                    </div>
                    <div class="modal-footer border-0 pt-0 justify-content-center">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 btn-sm" wire:click="closeQrModal">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showEvalQrModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1060;" dir="rtl">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 p-3 bg-white">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-star-fill text-warning fs-5"></i>
                        <span>كود ورابط استمارة التقييم الشاملة للشهادات</span>
                    </h6>
                    <button type="button" class="btn-close" wire:click="closeEvalQrModal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <p class="text-muted small mb-3">امسح الكود التالي بهاتفك لفتح استمارة التقييم الرسمية وتأكيد بريد الشهادة:</p>

                    <div class="bg-light p-3 rounded-4 d-inline-block border mb-3 shadow-sm">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=190x190&data={{ urlencode($generatedEvalUrl) }}" alt="Evaluation QR Code" class="rounded-3">
                    </div>

                    <div class="input-group input-group-sm mb-2">
                        <input type="text" class="form-control bg-light text-center font-monospace" value="{{ $generatedEvalUrl }}" readonly id="evalLinkInput">
                        <button class="btn btn-outline-secondary px-3" type="button" onclick="navigator.clipboard.writeText(document.getElementById('evalLinkInput').value); alert('تم نسخ رابط التقييم بنجاح!');">
                            <i class="bi bi-clipboard"></i> نسخ الرابط
                        </button>
                    </div>
                    <span class="text-muted" style="font-size: 0.75rem;">هذا الرابط مخصص لتقييم الدورة وتوثيق الإيميل الرسمي للشهادة.</span>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 btn-sm" wire:click="closeEvalQrModal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
@endif
</div>
