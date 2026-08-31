<!-- العنصر الجذر الوحيد الملتزم بشروط Livewire والمغلف لكافة محتويات المكون -->
<div class="container-fluid py-3">

    <!-- أكواد الـ CSS المخصصة للتحكم في عناصر الشاشة، الاسكرول بار، والطباعة الرسمية -->
    <style>
        .custom-scroll {
            max-height: 75vh;
            overflow-y: auto;
            overflow-x: auto;
        }

        .custom-scroll::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        @media print {
            .no-print, nav, header, footer {
                display: none !important;
            }

            .print-header {
                display: block !important;
            }

            .print-footer {
                display: block !important;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                width: 100%;
                background: white;
                border-top: 1px solid #ddd;
                padding-top: 8px;
            }

            body {
                background-color: #fff !important;
                color: #000 !important;
                font-size: 12px !important;
            }

            #printable-area {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .custom-scroll {
                max-height: none !important;
                overflow: visible !important;
            }

            .card {
                border: 1px solid #dee2e6 !important;
                box-shadow: none !important;
                break-inside: avoid;
            }
        }
    </style>

    <!-- [1] قسم عنوان الصفحة الأساسي وأزرار التحكم الظاهرة على الشاشة (مخفي عند الطباعة) -->
    <div class="d-flex align-items-center justify-content-between mb-3 no-print">
        <div>
            <h5 class="fw-bold mb-1 text-primary">
                <i class="bi bi-search me-2"></i>الاستعلام الشامل عن موقف مرشح
            </h5>
            <p class="text-muted small mb-0">البحث بالرقم القومي أو الاسم لعرض الملف الكامل وسجل الدراسات العليا والأجازات وإيقاف القيد</p>
        </div>
    </div>

    <!-- [2] كارت شريط البحث وإدخال معايير الاستعلام (الرقم القومي أو الاسم) -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 no-print">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <label class="form-label fw-bold extra-small text-secondary mb-1">البحث بالرقم القومي (14 رقم):</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-card-heading text-primary"></i></span>
                        <input type="text"
                               wire:model.live.debounce.400ms="search_national_id"
                               maxlength="14"
                               class="form-control border-start-0"
                               placeholder="أدخل الرقم القومي...">
                    </div>
                </div>

                <div class="col-md-2 text-center pt-3 pt-md-0">
                    <span class="badge bg-light text-muted border px-3 py-1 rounded-pill extra-small">أو</span>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-bold extra-small text-secondary mb-1">البحث باسم المرشح:</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-person text-primary"></i></span>
                        <input type="text"
                               wire:model.live.debounce.400ms="search_name"
                               class="form-control border-start-0"
                               placeholder="أدخل اسم المرشح/الطبيب...">
                    </div>
                </div>
            </div>

            @if(!empty($search_national_id) || !empty($search_name))
                <div class="text-end mt-2 pt-2 border-top">
                    <button class="btn btn-xs btn-outline-secondary py-1 px-3 rounded-2" style="font-size: 12px;" wire:click="resetResults">
                        <i class="bi bi-x-circle me-1"></i>تفريغ البحث
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- [3] قسم معالجة وعرض نتائج البحث المخصصة للشاشة والطباعة الرسمية -->
    @if($searched)
        @if($candidate)

            <!-- زر الطباعة الظاهري على واجهة المستخدم (مخفي عند الطباعة الفعلية) -->
            <div class="d-flex justify-content-end mb-3 no-print">
                <button onclick="window.print()" class="btn btn-sm btn-primary shadow-sm px-3">
                    <i class="bi bi-printer me-1"></i> طباعة النتيجة الرسمية
                </button>
            </div>

            <!-- الحاوية الكبرى المخصصة للطباعة والتي تحتوي على الهيدر والبيانات والفوتر مع دعم الاسكرول -->
            <div id="printable-area">
                <div class="custom-scroll p-1">

                    <!-- [4] الترويسة (الهيدر) الرسمية للطباعة (تظهر فقط عند الطباعة): اسم الوزارة، المحافظة، المديرية، الإدارة، والشعار -->
                    <div class="print-header" style="display: none;">
                        <div class="row align-items-center mb-3 pb-3 border-bottom">
                            <div class="col-8 text-start">
                                <h6 class="fw-bold m-0 text-dark" style="font-size: 13px;">{{ $settings->ministry_name ?? 'وزارة الصحة والسكان' }}</h6>
                                <h6 class="fw-bold m-0 text-dark" style="font-size: 12px;">{{ $settings->governorate_name ?? '' }}</h6>
                                <h6 class="fw-bold m-0 text-primary" style="font-size: 12px;">{{ $settings->directorate_name ?? '' }}</h6>
                                <h6 class="fw-bold m-0 text-secondary" style="font-size: 11px;">{{ $settings->administration_name ?? '' }}</h6>
                            </div>
                            <div class="col-4 text-end">
                                @if(isset($settings->logo_path))
                                    <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Logo" style="max-height: 65px;" class="mb-1">
                                @endif
                            </div>
                        </div>

                        <div class="text-center my-3 p-2 bg-light border rounded">
                            <h5 class="fw-bold text-dark m-0" style="font-size: 14px;">
                                استعلام عن موقف الدراسات العليا للمرشح
                            </h5>
                            <div class="mt-1 fw-bold text-primary" style="font-size: 12px;">
                                اسم المستخدم: {{ $candidate->name }} | الرقم القومي: <span class="font-monospace">{{ $candidate->national_id }}</span>
                            </div>
                            <div class="text-muted extra-small mt-1" style="font-size: 10px;">
                                تاريخ الطباعة: {{ date('Y-m-d') }} | العام المالي: {{ $settings->fiscal_year ?? '—' }}
                            </div>
                        </div>
                    </div>

                    <!-- [5] كارت الملف الشخصي والوظيفي والمؤهل الأكاديمي للطبيب المرشح -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-primary text-white py-2 px-3 d-flex align-items-center justify-content-between">
                            <span class="fw-bold" style="font-size: 14px;">
                                <i class="bi bi-person-badge-fill me-2"></i>الملف الشخصي والوظيفي للمرشح
                            </span>
                            <span class="badge bg-white text-primary fw-bold">{{ $candidate->profession }}</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-6 border-end-md">
                                    <h6 class="fw-bold text-secondary extra-small mb-2 border-bottom pb-1">
                                        <i class="bi bi-info-circle me-1"></i>البيانات الأساسية والوظيفية
                                    </h6>
                                    <div class="row g-2 style-details" style="font-size: 12px;">
                                        <div class="col-6 text-muted">الاسم الكامل:</div>
                                        <div class="col-6 fw-bold text-dark">{{ $candidate->name }}</div>
                                        <div class="col-6 text-muted">الرقم القومي:</div>
                                        <div class="col-6 fw-bold text-primary font-monospace">{{ $candidate->national_id }}</div>
                                        <div class="col-6 text-muted">رقم الهاتف:</div>
                                        <div class="col-6 fw-bold">{{ $candidate->phone ?? 'غير مسجل' }}</div>
                                        <div class="col-6 text-muted">جهة العمل الحالي:</div>
                                        <div class="col-6 fw-bold">{{ $candidate->facility->name ?? 'غير محددة' }}</div>
                                        <div class="col-6 text-muted">المركز التابع له:</div>
                                        <div class="col-6 fw-bold">{{ $candidate->facility->district->name ?? 'غير محدد' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h6 class="fw-bold text-secondary extra-small mb-2 border-bottom pb-1">
                                        <i class="bi bi-mortarboard me-1"></i>المؤهل والتخرج الأكاديمي
                                    </h6>
                                    <div class="row g-2 style-details" style="font-size: 12px;">
                                        <div class="col-6 text-muted">الجامعة والكلية:</div>
                                        <div class="col-6 fw-bold">{{ $candidate->qualification->university ?? '—' }} ({{ $candidate->qualification->faculty ?? '—' }})</div>
                                        <div class="col-6 text-muted">دفعة التخرج:</div>
                                        <div class="col-6 fw-bold text-dark font-monospace">{{ $candidate->qualification->graduation_batch ?? '—' }}</div>
                                        <div class="col-6 text-muted">التقدير العام:</div>
                                        <div class="col-6 fw-bold text-success">{{ $candidate->qualification->general_grade ?? '—' }}</div>
                                        <div class="col-6 text-muted">تقدير مادة التخصص:</div>
                                        <div class="col-6 fw-bold text-info">{{ $candidate->qualification->subject_grade ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- [6] سجل قيود الدراسات العليا وأجازات التفرغ وإيقاف القيد المفصل -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold text-primary mb-0" style="font-size: 14px;">
                            <i class="bi bi-journal-bookmark-fill me-1"></i>سجل قيد الدراسات العليا وأجازات التفرغ وإيقاف القيد
                        </h6>
                        <span class="badge bg-secondary rounded-pill">{{ $candidate->postgraduateRegistrations->count() }} طلب / درجة مسجلة</span>
                    </div>

                    @forelse($candidate->postgraduateRegistrations as $reg)
                        <div class="card border-0 shadow-sm rounded-3 mb-3 overflow-hidden">
                            <div class="card-header bg-light py-2 px-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <span class="badge bg-primary me-2">{{ $reg->required_degree }}</span>
                                    <span class="fw-bold text-dark" style="font-size: 13px;">تخصص: {{ $reg->required_specialty }}</span>
                                    <span class="text-muted extra-small ms-2 font-monospace">({{ $reg->required_university ?? $reg->university ?? 'جامعة غير محددة' }})</span>
                                </div>
                                <div>
                                    @if(in_array($reg->study_status, ['مستمر', 'قيد الدراسة', 'مفتوح']))
                                        <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-1 rounded-pill">موقفه: قيد الدراسة (مستمر)</span>
                                    @elseif($reg->study_status === 'حصل على الدرجة')
                                        <span class="badge bg-success-subtle text-success border border-success px-3 py-1 rounded-pill">تم الحصول على الدرجة</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-1 rounded-pill">موقفه: {{ $reg->study_status }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-2 mb-3 bg-light p-2 rounded-2 border" style="font-size: 12px;">
                                    <div class="col-md-2 col-6">
                                        <span class="text-muted d-block extra-small">تاريخ تقديم الطلب:</span>
                                        <strong class="font-monospace text-dark">{{ $reg->application_date ?? ($reg->created_at?->format('Y-m-d') ?? '—') }}</strong>
                                    </div>

                                    <div class="col-md-2 col-6">
                                        <span class="text-muted d-block extra-small">تاريخ القيد بالدراسة:</span>
                                        <strong class="font-monospace text-success">{{ $reg->registration_date ?? '—' }}</strong>
                                    </div>

                                    <div class="col-md-2 col-6">
                                        <span class="text-muted d-block extra-small">تاريخ تنفيذ الدراسة:</span>
                                        <strong class="font-monospace text-primary">{{ $reg->execution_date ?? '—' }}</strong>
                                    </div>

                                    <div class="col-md-2 col-6">
                                        <span class="text-muted d-block extra-small">حالة الدراسة:</span>
                                        <strong>{{ $reg->study_status ?? 'قيد الدراسة' }}</strong>
                                    </div>

                                    <!-- إظهار تاريخ الحصول على الدرجة أو تاريخ الاعتذار حسب الحالة بدقة -->
                                    @if($reg->study_status === 'حصل على الدرجة')
                                        <div class="col-md-2 col-6">
                                            <span class="text-muted d-block extra-small">تاريخ الحصول / الاعتذار:</span>
                                            <strong class="text-success font-monospace">
                                                {{ $reg->nominated_degree_date ?? '—' }}
                                                <span class="text-muted d-block" style="font-size: 10px;">(حصل على الدرجة)</span>
                                            </strong>
                                        </div>
                                    @elseif(str_contains($reg->study_status, 'اعتذار') || $reg->study_status === 'اعتذار')
                                        <div class="col-md-2 col-6">
                                            <span class="text-muted d-block extra-small">تاريخ الحصول / الاعتذار:</span>
                                            <strong class="text-danger font-monospace">
                                                {{ $reg->apology_date ?? '—' }}
                                                @if($reg->apology_reason)
                                                    <span class="text-muted d-block text-truncate" style="font-size: 10px;" title="{{ $reg->apology_reason }}">({{ $reg->apology_reason }})</span>
                                                @else
                                                    <span class="text-muted d-block" style="font-size: 10px;">(اعتذار)</span>
                                                @endif
                                            </strong>
                                        </div>
                                    @else
                                        <div class="col-md-2 col-6">
                                            <span class="text-muted d-block extra-small">تاريخ الحصول / الاعتذار:</span>
                                            <strong class="text-muted font-monospace">—</strong>
                                        </div>
                                    @endif

                                    <div class="col-md-2 col-6">
                                        <span class="text-muted d-block extra-small">سنوات الدراسة حتى الآن:</span>
                                        <strong class="text-success font-monospace">
                                            {{ $reg->years_from_registration ? $reg->years_from_registration . ' ' : '—' }}
                                        </strong>
                                    </div>
                                </div>

                                <!-- جدول أجازات التفرغ الدراسي -->
                                <h6 class="fw-bold text-secondary extra-small mb-2">
                                    <i class="bi bi-calendar-range me-1"></i>سجل أجازات التفرغ الدراسي الممنوحة لهذه الدرجة:
                                </h6>
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm table-bordered align-middle mb-0 text-nowrap" style="font-size: 11px;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="py-1 px-2 text-center" style="width: 12%;">نوع التفرغ</th>
                                                <th class="py-1 px-2 text-center" style="width: 15%;">تاريخ البداية</th>
                                                <th class="py-1 px-2 text-center" style="width: 15%;">تاريخ النهاية</th>
                                                <th class="py-1 px-2 text-center" style="width: 15%;">المدة المحسوبة</th>
                                                <th class="py-1 px-2" style="width: 25%;">رقم القرار / ملاحظات</th>
                                                <th class="py-1 px-2 text-center" style="width: 18%;">المسؤول والتاريخ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($reg->studyLeaves as $leave)
                                                @php
                                                    $s = \Carbon\Carbon::parse($leave->start_date);
                                                    $e = $leave->end_date ? \Carbon\Carbon::parse($leave->end_date) : null;
                                                    $durText = '-';
                                                    if ($e) {
                                                        $y = (int) $s->diffInYears($e);
                                                        $m = (int) $s->copy()->addYears($y)->diffInMonths($e);
                                                        $yT = $y > 0 ? ($y == 1 ? 'سنة' : ($y == 2 ? 'سنتين' : "{$y} سنوات")) : '';
                                                        $mT = $m > 0 ? ($m == 1 ? 'شهر' : ($m == 2 ? 'شهرين' : "{$m} شهور")) : '';
                                                        $durText = trim("{$yT} {$mT}") ?: 'أقل من شهر';
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="text-center font-monospace">
                                                        @if($leave->leave_type == 'بمرتب')
                                                            <span class="badge bg-success-subtle text-success border border-success px-2 py-0">بمرتب</span>
                                                        @else
                                                            <span class="badge bg-warning-subtle text-warning border border-warning px-2 py-0">بدون مرتب</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center font-monospace fw-bold">{{ $leave->start_date }}</td>
                                                    <td class="text-center font-monospace fw-bold">{{ $leave->end_date ?? 'مستمر' }}</td>
                                                    <td class="text-center fw-bold text-primary">{{ $durText }}</td>
                                                    <td>{{ $leave->decision_notes ?? '—' }}</td>
                                                    <td class="text-center font-monospace text-muted" style="font-size: 10px;">
                                                        <div>{{ $leave->user->name ?? 'مدير النظام' }}</div>
                                                        <div class="text-secondary">{{ $leave->created_at?->format('Y-m-d') }}</div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-2 text-muted fst-italic">
                                                        لم يتم تسجيل أجازات تفرغ تفصيلية لهذه الدرجة العلمية حتى الآن.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- جدول إيقاف القيد -->
                                <h6 class="fw-bold text-secondary extra-small mb-2">
                                    <i class="bi bi-pause-circle me-1"></i>سجل إيقاف القيد الممنوحة لهذه الدرجة:
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle mb-0 text-nowrap" style="font-size: 11px;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="py-1 px-2 text-center" style="width: 15%;">بداية الإيقاف</th>
                                                <th class="py-1 px-2 text-center" style="width: 15%;">نهاية الإيقاف</th>
                                                <th class="py-1 px-2 text-center" style="width: 15%;">مدة الإيقاف</th>
                                                <th class="py-1 px-2 text-center" style="width: 15%;">استلام العمل</th>
                                                <th class="py-1 px-2" style="width: 22%;">السبب / ملاحظات</th>
                                                <th class="py-1 px-2 text-center" style="width: 18%;">المسؤول والتاريخ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $allPauses = $reg->pauses ?? ($reg->registrationPauses ?? collect());
                                            @endphp
                                            @forelse($allPauses as $pause)
                                                <tr>
                                                    <td class="text-center font-monospace fw-bold">{{ $pause->pause_start_date ?? '—' }}</td>
                                                    <td class="text-center font-monospace fw-bold">{{ $pause->pause_end_date ?? 'مستمر' }}</td>
                                                    <td class="text-center font-monospace text-info fw-bold">{{ $pause->pause_duration ?? '-' }}</td>
                                                    <td class="text-center font-monospace">{{ $pause->resume_date ?? 'لم يستلم' }}</td>
                                                    <td>{{ $pause->pause_reason ?? '-' }}</td>
                                                    <td class="text-center font-monospace text-muted" style="font-size: 10px;">
                                                        <div>{{ $pause->user->name ?? 'مدير النظام' }}</div>
                                                        <div class="text-secondary">{{ $pause->created_at?->format('Y-m-d') }}</div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-2 text-muted fst-italic">
                                                        لم يتم تسجيل أي فترات إيقاف قيد لهذه الدرجة العلمية حتى الآن.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card border-0 shadow-sm rounded-3 py-4 text-center text-muted">
                            <i class="bi bi-journal-x fs-2 d-block mb-1 text-secondary"></i>
                            لا توجد أي قيود أو طلبات دراسات عليا مسجلة لهذا الطبيب سابقاً.
                        </div>
                    @endforelse

                    <!-- [7] الفوتر (التذييل) الرسمي للطباعة أسفل الصفحة (يظهر فقط عند الطباعة) -->
                    <div class="print-footer" style="display: none;">
                        <div class="d-flex justify-content-around text-muted" style="font-size: 11px;">
                            <span><i class="bi bi-geo-alt me-1"></i>العنوان: {{ $officialHeader->detailed_address ?? ($settings->address ?? 'العنوان الإداري غير مسجل') }}</span>
                            <span><i class="bi bi-envelope me-1"></i>البريد: {{ $officialHeader->official_email ?? ($settings->email ?? 'info@domain.com') }}</span>
                            <span><i class="bi bi-telephone me-1"></i>الهاتف: {{ $officialHeader->phone_fax ?? ($settings->phone ?? '—') }}</span>
                        </div>
                    </div>

                </div>
            </div>

        @else
            <div class="alert alert-warning text-center rounded-3 shadow-sm py-4">
                <i class="bi bi-exclamation-triangle-fill fs-3 d-block mb-2 text-warning"></i>
                لم يتم العثور على أي طبيب أو مرشح يطابق بيانات البحث المدخلة.
            </div>
        @endif
    @endif

</div>
