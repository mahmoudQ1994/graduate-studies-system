<div class="container-fluid py-3">
    <!-- عنوان الصفحة -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="fw-bold mb-1 text-primary">
                <i class="bi bi-search me-2"></i>الاستعلام الشامل عن موقف مرشح
            </h5>
            <p class="text-muted small mb-0">البحث بالرقم القومي أو الاسم لعرض الملف الكامل وسجل الدراسات العليا والأجازات</p>
        </div>
    </div>

    <!-- كارت شريط البحث المنفصل -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <!-- 1. البحث بالرقم القومي -->
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

                <!-- فاصل أور -->
                <div class="col-md-2 text-center pt-3 pt-md-0">
                    <span class="badge bg-light text-muted border px-3 py-1 rounded-pill extra-small">أو</span>
                </div>

                <!-- 2. البحث بالاسم -->
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

    <!-- نتائج البحث -->
    @if($searched)
        @if($candidate)
            <!-- كارت ملف البيانات الشخصية والأكاديمية -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-primary text-white py-2 px-3 d-flex align-items-center justify-content-between">
                    <span class="fw-bold" style="font-size: 14px;">
                        <i class="bi bi-person-badge-fill me-2"></i>الملف الشخصي والوظيفي للمرشح
                    </span>
                    <span class="badge bg-white text-primary fw-bold">{{ $candidate->profession }}</span>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3">
                        <!-- البيانات الأساسية -->
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

                        <!-- المؤهل والأكاديميا -->
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

            <!-- سجل طلبات وقيد الدراسات العليا والأجازات -->
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="fw-bold text-primary mb-0" style="font-size: 14px;">
                    <i class="bi bi-journal-bookmark-fill me-1"></i>سجل قيد الدراسات العليا وأجازات التفرغ
                </h6>
                <span class="badge bg-secondary rounded-pill">{{ $candidate->postgraduateRegistrations->count() }} طلب / درجة مسجلة</span>
            </div>

            @forelse($candidate->postgraduateRegistrations as $reg)
                <div class="card border-0 shadow-sm rounded-3 mb-3 overflow-hidden">
                    <div class="card-header bg-light py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                        <div>
                            <span class="badge bg-primary me-2">{{ $reg->required_degree }}</span>
                            <span class="fw-bold text-dark" style="font-size: 13px;">تخصص: {{ $reg->required_specialty }}</span>
                            <span class="text-muted extra-small ms-2">({{ $reg->required_university }})</span>
                        </div>
                        <div>
                            @if(in_array($reg->study_status, ['مستمر', 'قيد الدراسة', 'مفتوح']))
                                <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-1 rounded-pill">موقفه: قيد الدراسة (مستمر)</span>
                            @elseif($reg->study_status === 'تم الحصول عليها')
                                <span class="badge bg-success-subtle text-success border border-success px-3 py-1 rounded-pill">تم الحصول على الدرجة</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-1 rounded-pill">موقفه: {{ $reg->study_status }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <!-- تفاصيل قيد الدرجة -->
                        <div class="row g-2 mb-3 bg-light p-2 rounded-2 border" style="font-size: 12px;">
                            <div class="col-md-3">
                                <span class="text-muted d-block extra-small">تاريخ تقديم الطلب:</span>
                                <strong class="font-monospace text-dark">{{ $reg->application_date ?? '—' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <span class="text-muted d-block extra-small">تاريخ القيد النهائي:</span>
                                <strong class="font-monospace text-primary">{{ $reg->registration_date ?? '—' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <span class="text-muted d-block extra-small">حالة الدرجة والمرشح:</span>
                                <strong>{{ $reg->nominated_degree_status ?? 'قيد الدراسة' }}</strong>
                                @if($reg->nominated_degree_date)
                                    <span class="font-monospace text-muted">({{ $reg->nominated_degree_date }})</span>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <span class="text-muted d-block extra-small">سنوات الدراسة حتى الآن:</span>
                                <strong class="text-success font-monospace fs-6">
                                    {{ $reg->years_from_registration ? $reg->years_from_registration . ' سنة' : '—' }}
                                </strong>
                            </div>
                            @if($reg->cancellation_reason)
                                <div class="col-md-12 border-top pt-1 mt-1 text-danger extra-small">
                                    <strong>سبب الاعتذار / الإلغاء:</strong> {{ $reg->cancellation_reason }}
                                </div>
                            @endif
                        </div>

                        <!-- جدول أجازات التفرغ المربوطة بهذه الدرجة -->
                        <h6 class="fw-bold text-secondary extra-small mb-2">
                            <i class="bi bi-calendar-range me-1"></i>سجل أجازات التفرغ الدراسي الممنوحة لهذه الدرجة:
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 11px;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-1 px-2 text-center" style="width: 15%;">نوع التفرغ</th>
                                        <th class="py-1 px-2 text-center" style="width: 18%;">تاريخ بداية التفرغ</th>
                                        <th class="py-1 px-2 text-center" style="width: 18%;">تاريخ نهاية التفرغ</th>
                                        <th class="py-1 px-2 text-center" style="width: 18%;">المدة المحسوبة</th>
                                        <th class="py-1 px-2">رقم القرار / تفاصيل الأجازة</th>
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
                                            <td class="text-center font-monospace fw-bold">{{ $leave->end_date ?? 'مستمر حتى الآن' }}</td>
                                            <td class="text-center fw-bold text-primary">{{ $durText }}</td>
                                            <td>{{ $leave->decision_notes ?? 'لا يوجد ملاحظات مدونة' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-2 text-muted fst-italic">
                                                لم يتم تسجيل أجازات تفرغ تفصيلية لهذه الدرجة العلمية حتى الآن.
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

        @else
            <div class="alert alert-warning text-center rounded-3 shadow-sm py-4">
                <i class="bi bi-exclamation-triangle-fill fs-3 d-block mb-2 text-warning"></i>
                لم يتم العثور على أي طبيب أو مرشح يطابق بيانات البحث المدخلة.
            </div>
        @endif
    @endif
</div>
