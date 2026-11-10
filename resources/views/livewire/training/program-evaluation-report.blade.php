<div class="container-fluid px-4 py-4" style="background-color: #f4f7fa; min-height: 100vh;" dir="rtl">

    <!-- رأس الصفحة الرسمي -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white" dir="rtl">
        <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="text-start">
                <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-clipboard2-check-fill text-primary"></i>
                    <span>متابعة تقييمات البرامج والاختبارات (القبلي / البعدي)</span>
                </h4>
                <p class="text-muted small mb-0">لوحة التحكم الإدارية لمتابعة استمارات التقييم الشاملة، البريد الإلكتروني لاعتماد الشهادات، درجات اختبارات قياس المعرفة، ومواقف الاجتياز للمتدربين.</p>
            </div>

        </div>
    </div>

    <!-- كارت الفلترة المتقدمة -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white" dir="rtl">
        <div class="card-body p-4">
            <div class="row align-items-center g-3 text-start">

                <!-- 1. اسم البرنامج التدريبي -->
                <div class="col-lg-5 col-md-12">
                    <label class="form-label fw-bold text-secondary small d-block mb-1 text-start">
                        <i class="bi bi-journal-bookmark-fill text-primary me-1"></i> 1. اسم البرنامج التدريبي:
                    </label>
                    <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-light border-0 ps-3 text-primary">
                            <i class="bi bi-search"></i>
                        </span>
                        <select class="form-select bg-light border-0 py-2.5 px-3 text-start fw-semibold shadow-none" wire:model.live="selected_program_id" style="font-size: 0.9rem; direction: rtl; text-align: right; cursor: pointer;">
                            <option value="">-- ابحث أو اختر اسم البرنامج التدريبي --</option>
                            @foreach($programs as $prog)
                                <option value="{{ $prog->id }}">{{ $prog->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 2. تاريخ التنفيذ (الموعد) -->
                <div class="col-lg-4 col-md-6">
                    <label class="form-label fw-bold text-secondary small d-block mb-1 text-start">
                        <i class="bi bi-calendar2-week-fill text-success me-1"></i> 2. تاريخ التنفيذ (الموعد):
                    </label>
                    <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-light border-0 ps-3 text-success">
                            <i class="bi bi-calendar-range"></i>
                        </span>
                        <select class="form-select bg-light border-0 py-2.5 px-3 text-start fw-semibold shadow-none" wire:model.live="selected_schedule_id" @if(!$selected_program_id) disabled @endif style="font-size: 0.9rem; direction: rtl; text-align: right; cursor: pointer;">
                            <option value="">-- اختر موعد البرنامج التدريبي --</option>
                            @foreach($availableSchedules as $sch)
                                <option value="{{ $sch->id }}">
                                    📅 من {{ $sch->start_date }} إلى {{ $sch->end_date }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 3. بحث سريع عن متدرب -->
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-bold text-secondary small d-block mb-1 text-start">
                        <i class="bi bi-person-badge-fill text-info me-1"></i> 3. بحث سريع (اسم / رقم قومي):
                    </label>
                    <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-light border-0 ps-3 text-muted">
                            <i class="bi bi-person-search"></i>
                        </span>
                        <input type="text" class="form-control bg-light border-0 py-2.5 px-3 text-start shadow-none" wire:model.live.debounce.300ms="search" @if(!$selected_schedule_id) disabled @endif placeholder="ابحث باسم المتدرب أو رقمه..." style="font-size: 0.9rem; direction: rtl; text-align: right;">
                    </div>
                </div>

            </div>

            <!-- شريط عرض ونسخ رابط استمارة التقييم -->
            @if($evaluationLink)
                <div class="mt-4 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 rounded-4 shadow-sm text-start" style="background: linear-gradient(135deg, #e8f5e9, #f1f8e9); border: 1px solid #c8e6c9;" dir="rtl">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success text-white rounded-circle p-2.5 d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;">
                            <i class="bi bi-whatsapp fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1 small text-start">رابط استمارة التقييم الرسمية المخصص لهذه الدورة:</h6>
                            <span class="text-success font-monospace fw-semibold text-start d-block" style="font-size: 0.78rem;" dir="ltr">{{ $evaluationLink }}</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ $evaluationLink }}" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3 py-2 fw-semibold shadow-sm bg-white">
                            <i class="bi bi-box-arrow-up-right me-1"></i> فتح الاستمارة
                        </a>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $evaluationLink }}'); alert('تم بنجاح نسخ رابط استمارة التقييم لمشاركته عبر الواتساب!');" class="btn btn-success btn-sm rounded-pill px-3 py-2 fw-semibold text-white shadow-sm" style="background-color: #25d366; border: none;">
                            <i class="bi bi-whatsapp me-1"></i> نسخ للواتس آب
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($selected_schedule_id)
        <!-- كروت الإحصائيات المباشرة -->
        <div class="row g-3 mb-4 text-start" dir="rtl">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 text-start">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-start">
                            <span class="text-muted small d-block mb-1">إجمالي المستهدفين المسجلين</span>
                            <h3 class="fw-bold text-dark mb-0">{{ $stats['total_enrolled'] }}</h3>
                        </div>
                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                            <i class="bi bi-people-fill fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 text-start">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-start">
                            <span class="text-muted small d-block mb-1">التقييمات التي تم تسليمها</span>
                            <h3 class="fw-bold text-success mb-0">{{ $stats['submitted_evaluations'] }}</h3>
                        </div>
                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 text-start">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-start">
                            <span class="text-muted small d-block mb-1">لم يتم التسليم بعد</span>
                            <h3 class="fw-bold text-warning mb-0">{{ $stats['pending_evaluations'] }}</h3>
                        </div>
                        <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                            <i class="bi bi-hourglass-split fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 text-start">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-start">
                            <span class="text-muted small d-block mb-1">نسبة تقييم ورضا البرنامج ككل</span>
                            <h3 class="fw-bold text-info mb-0">{{ $stats['satisfaction_rate'] }}%</h3>
                        </div>
                        <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                            <i class="bi bi-graph-up-arrow fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول البيانات والتقارير الاحترافي -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white text-start" dir="rtl">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 65vh;">
                    <table class="table table-hover align-middle mb-0 text-center" style="font-size: 0.88rem; white-space: nowrap;" dir="rtl">
                        <thead class="table-light text-secondary sticky-top shadow-sm border-bottom" style="z-index: 1;">
                            <tr style="height: 55px;">
                                <th class="py-3 px-3">م</th>
                                <th class="text-start py-3 px-3">اسم المتدرب</th>
                                <th class="py-3 px-3">الرقم القومي</th>
                                <th class="py-3 px-3">جهة العمل / الوظيفة</th>
                                <th class="py-3 px-3">حالة الحضور</th>
                                <th class="py-3 px-3">استمارة التقييم الشامل</th>
                                <th class="py-3 px-3">البريد الإلكتروني (للشهادة)</th>
                                <th class="py-3 px-3">الاختبار القبلي (Pre)</th>
                                <th class="py-3 px-3">الاختبار البعدي (Post)</th>
                                <th class="py-3 px-3">موقف الاجتياز النهائي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enrollments as $index => $row)
                                <tr style="height: 65px;" class="border-bottom">
                                    <td class="text-muted px-3 fw-semibold">{{ $enrollments->firstItem() + $index }}</td>
                                    <td class="fw-bold text-dark text-start px-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                {{ mb_substr($row->trainee_name, 0, 1) }}
                                            </div>
                                            <span>{{ $row->trainee_name }}</span>
                                        </div>
                                    </td>
                                    <td class="font-monospace text-secondary px-3">{{ $row->national_id }}</td>
                                    <td class="text-secondary px-3 text-start">{{ $row->workplace ?? '---' }}</td>

                                    <!-- حالة الحضور -->
                                    <td class="px-3">
                                        @if($row->attendance_status == 'passed')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-bold">اجتاز الحضور</span>
                                        @elseif($row->attendance_status == 'failed')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fw-bold">تجاوز الغياب</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary px-3 py-1.5 rounded-pill">قيد المتابعة</span>
                                        @endif
                                    </td>

                                    <!-- حالة التقييم ومعاينة الإجابات -->
                                    <td class="px-3">
                                        @if($row->evaluation_id)
                                            <button type="button" wire:click="viewTraineeEvaluation({{ $row->trainee_id }})" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm">
                                                <i class="bi bi-eye-fill me-1"></i> عرض التقييم
                                            </button>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1.5 rounded-pill">لم يتم التقييم</span>
                                        @endif
                                    </td>

                                    <!-- البريد الإلكتروني -->
                                    <td class="font-monospace text-muted px-3">
                                        {{ $row->official_email ?: 'غير مسجل' }}
                                    </td>

                                    <!-- الاختبار القبلي -->
                                    <td class="px-3 font-monospace">
                                        <span class="badge bg-light text-dark border px-2.5 py-1">--- / 10</span>
                                    </td>

                                    <!-- الاختبار البعدي -->
                                    <td class="px-3 font-monospace">
                                        <span class="badge bg-light text-dark border px-2.5 py-1">--- / 10</span>
                                    </td>

                                    <!-- موقف الاجتياز -->
                                    <td class="px-3">
                                        @if($row->attendance_status == 'passed')
                                            <span class="badge bg-success text-white px-3 py-1.5 rounded-pill fw-bold shadow-sm">اجتاز البرنامج</span>
                                        @else
                                            <span class="badge bg-danger text-white px-3 py-1.5 rounded-pill fw-bold shadow-sm">لم يجتاز</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <div class="py-4">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 text-black-50"></i>
                                            <p class="mb-0 fw-semibold">لا توجد تسجيلات مطابقة لهذه الفترة الزمنية.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($enrollments->hasPages())
                <div class="card-footer bg-white py-3 border-top text-start">
                    {{ $enrollments->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white" dir="rtl">
            <div class="py-5">
                <i class="bi bi-journal-check fs-1 text-primary mb-3 d-block"></i>
                <h5 class="fw-bold text-dark">يرجى اختيار اسم البرنامج التدريبي ثم تحديد تاريخ التنفيذ (الموعد) أعلاه</h5>
                <p class="text-muted small mb-0">لعرض تقارير الحضور، استمارات التقييم، وإحصائيات تسليم التقييمات الخاصة بالدورة.</p>
            </div>
        </div>
    @endif

    <!-- نافذة منبثقة (Modal) لعرض إجابات تقييم المتدرب التفصيلية باللغة العربية -->
    <div class="modal fade" id="evaluationModal" tabindex="-1" aria-labelledby="evaluationModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered" dir="rtl">
            <div class="modal-content border-0 shadow-lg rounded-4 text-start">
                <div class="modal-header bg-primary text-white px-4 py-3 rounded-top-4">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2 mb-0" id="evaluationModalLabel">
                        <i class="bi bi-journal-text"></i> تفاصيل استمارة تقييم المتدرب: <span class="text-warning">{{ $selectedTraineeName }}</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    @if($selectedEvaluation)
                        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white mb-3">
                            <h6 class="fw-bold text-primary mb-2 border-bottom pb-2">بيانات التواصل والإعتماد:</h6>
                            <p class="mb-1"><strong>البريد الإلكتروني للشهادة:</strong> <span class="font-monospace text-muted">{{ $selectedEvaluation->official_email ?? 'غير مسجل' }}</span></p>
                            <p class="mb-0"><strong>تاريخ تسليم الاستمارة:</strong> <span class="text-muted">{{ $selectedEvaluation->created_at ?? '---' }}</span></p>
                        </div>

                        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                            <h6 class="fw-bold text-success mb-3 border-bottom pb-2">إجابات محاور الاستمارة التدريبية:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0" style="font-size: 0.88rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="py-2 px-3">محور السؤال / البند في الاستمارة</th>
                                            <th class="py-2 px-3 text-center" style="width: 200px;">تقييم المتدرب (الإجابة)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $evaluationLabels = [
                                                'phone number' => 'رقم الهاتف',
                                                'content clarity' => 'وضوح المحتوى العلمي',
                                                'content relevance' => 'ارتباط المحتوى بأهداف البرنامج',
                                                'content depth' => 'عمق وتغطية الموضوعات',
                                                'content time balance' => 'توازن الوقت المخصص للمحتوى',
                                                'trainer knowledge' => 'تمكن المحاضر من المادة العلمية',
                                                'trainer delivery' => 'أسلوب وطريقة العرض والشرح',
                                                'trainer interaction' => 'تفاعل المدرب وإجابة الاستفسارات',
                                                'training hall' => 'تجهيزات ومناسبة قاعة التدريب',
                                                'organization' => 'التنظيم والإدارة العامة',
                                                'overall rating' => 'التقييم العام للبرنامج',
                                                'notes' => 'الملاحظات والتوصيات العامة',
                                                'recommendations' => 'مقترحات وتوصيات المتدرب',
                                            ];
                                        @endphp

                                        @foreach((array)$selectedEvaluation as $column => $value)
                                            @if(!in_array(strtolower(trim($column)), ['id', 'training_program_schedule_id', 'trainee_id', 'official_email', 'created_at', 'updated_at']))
                                                @php
                                                    $cleanKey = strtolower(trim(str_replace('_', ' ', $column)));
                                                @endphp
                                                <tr>
                                                    <td class="fw-semibold text-secondary px-3 py-2">
                                                        {{ $evaluationLabels[$cleanKey] ?? ucwords($cleanKey) }}
                                                    </td>
                                                    <td class="text-center px-3 py-2 fw-bold text-dark">
                                                        {{ $value !== null && $value !== '' ? $value : '---' }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <p class="mb-0">جاري تحميل بيانات التقييم...</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-white border-0 px-4 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('show-evaluation-modal', () => {
            var myModal = new bootstrap.Modal(document.getElementById('evaluationModal'));
            myModal.show();
        });
    </script>
    @endscript

</div>
