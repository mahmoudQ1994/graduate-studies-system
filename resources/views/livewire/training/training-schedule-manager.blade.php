<div>
    <div class="container-fluid py-3">
        <!-- العنوان الرئيسي -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-primary mb-0">
                <i class="bi bi-calendar-check me-1"></i> جدول وتنشيط البرامج التدريبية المعتمدة
            </h5>
            <div>
                <!-- زر تصدير Excel -->
                <button type="button" wire:click="exportExcel" class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel me-1"></i> تصدير البيانات  Excel
                </button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- كارد فلاتر البحث المتقدم -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
            <div class="card-header bg-light fw-bold text-secondary py-2 border-bottom">
                <i class="bi bi-funnel me-1 text-primary"></i> فلاتر البحث المتقدم
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- البحث بالاسم أو الكود -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">بحث بالاسم أو الكود:</label>
                        <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="اكتب اسم أو كود البرنامج...">
                    </div>

                    <!-- من تاريخ -->
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">من تاريخ:</label>
                        <input type="date" class="form-control form-control-sm" wire:model.live="filter_from_date">
                    </div>

                    <!-- إلى تاريخ -->
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">إلى تاريخ:</label>
                        <input type="date" class="form-control form-control-sm" wire:model.live="filter_to_date">
                    </div>

                    <!-- نوع البرنامج -->
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">نوع البرنامج:</label>
                        <select class="form-select form-select-sm" wire:model.live="filter_program_type">
                            <option value="">-- الكل --</option>
                            @foreach($programTypes ?? [] as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- حالة البرنامج -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">حالة البرنامج:</label>
                        <select class="form-select form-select-sm" wire:model.live="filter_status">
                            <option value="">-- كل الحالات --</option>
                            <option value="draft">مسودة جدولة</option>
                            <option value="active">نشط (جاهز)</option>
                            <option value="in_progress">جاري التنفيذ</option>
                            <option value="completed">تم التنفيذ</option>
                            <option value="cancelled">ملغي</option>
                        </select>
                    </div>

                    <!-- اسم المحاضر -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">اسم المحاضر:</label>
                        <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="filter_trainer" placeholder="بحث باسم المحاضر...">
                    </div>

                    <!-- قاعة التدريب -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">قاعة التدريب:</label>
                        <select class="form-select form-select-sm" wire:model.live="filter_hall">
                            <option value="">-- كل القاعات --</option>
                            @foreach($halls as $hall)
                                <option value="{{ $hall->id }}">{{ $hall->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- زر إعادة التعيين -->
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> إعادة تعيين
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول العرض الرئيسي مع دعم التمرير الأفقي والراحة البصرية -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <span class="fw-bold text-secondary">
                    <i class="bi bi-table me-1 text-primary"></i> البرامج الجاهزة للجدولة والتنفيذ
                </span>
                <span class="badge bg-primary px-3 py-2" style="font-size: 0.85rem;">
                    إجمالي النتائج: {{ $programs->total() }}
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-hover table-striped mb-0 text-center align-middle" style="white-space: nowrap; font-size: 0.88rem;">
                        <thead class="table-light text-secondary sticky-top" style="z-index: 10;">
                            <tr style="height: 45px;">
                                <th class="py-3">#</th>
                                <th class="py-3">الكود</th>
                                <th class="py-3 text-start">اسم البرنامج</th>
                                <th class="py-3">نوع البرنامج</th>
                                <th class="py-3">حالة الجدولة</th>
                                <th class="py-3">تاريخ التنفيذ (البداية)</th>
                                <th class="py-3">تاريخ نهاية التنفيذ</th>
                                <th class="py-3">عدد أيام التدريب</th>
                                <th class="py-3">مكان التنفيذ</th>
                                <th class="py-3 text-start">المحاضرون</th>
                                <th class="py-3">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programs as $index => $item)
                                <tr wire:key="program-schedule-{{ $item->id }}" style="height: 50px;">
                                    <td class="text-muted fw-semibold">{{ $programs->firstItem() + $index }}</td>

                                    <!-- الكود -->
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1 font-monospace">
                                            {{ $item->code }}
                                        </span>
                                    </td>

                                    <!-- اسم البرنامج -->
                                    <td class="fw-bold text-start text-dark" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis;" title="{{ $item->name_ar }}">
                                        {{ $item->name_ar }}
                                    </td>

                                    <!-- نوع البرنامج -->
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1">
                                            {{ $item->programType->name ?? '---' }}
                                        </span>
                                    </td>

                                    <!-- حالة الجدولة -->
                                    <td>
                                        @if($item->schedule)
                                            @php
                                                $statusClass = match($item->schedule->schedule_status) {
                                                    'active' => 'bg-success text-white',
                                                    'in_progress' => 'bg-primary text-white',
                                                    'completed' => 'bg-secondary text-white',
                                                    'cancelled' => 'bg-danger text-white',
                                                    default => 'bg-warning text-dark',
                                                };
                                                $statusText = match($item->schedule->schedule_status) {
                                                    'active' => 'نشط وجاهز',
                                                    'in_progress' => 'جاري التنفيذ',
                                                    'completed' => 'تم التنفيذ',
                                                    'cancelled' => 'ملغي',
                                                    default => 'مسودة جدولة',
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }} px-2 py-1">
                                                {{ $statusText }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border px-2 py-1">
                                                غير مجدول
                                            </span>
                                        @endif
                                    </td>

                                    <!-- تاريخ التنفيذ (البداية) -->
                                    <td class="text-secondary font-monospace">
                                        {{ $item->schedule->start_date ?? '---' }}
                                    </td>

                                    <!-- تاريخ نهاية التنفيذ -->
                                    <td class="text-secondary font-monospace">
                                        {{ $item->schedule->end_date ?? '---' }}
                                    </td>

                                    <!-- عدد أيام التدريب -->
                                    <td>
                                        <span class="fw-bold text-dark">{{ $item->duration_days }}</span> أيام
                                    </td>

                                    <!-- مكان التنفيذ (القاعة) -->
                                    <td>
                                        <span class="text-dark">
                                            {{ $item->schedule->hall->name ?? ($item->hall->name ?? '---') }}
                                        </span>
                                    </td>

                                    <!-- المحاضرون -->
                                    <td class="text-start" style="max-width: 220px; overflow: hidden; text-overflow: ellipsis;" title="
                                        @if($item->schedule && $item->schedule->trainers->isNotEmpty())
                                            {{ $item->schedule->trainers->pluck('trainer_name')->implode(', ') }}
                                        @else
                                            {{ $item->consultant_name ?? '---' }}
                                        @endif
                                    ">
                                        <span class="text-muted small">
                                            @if($item->schedule && $item->schedule->trainers->isNotEmpty())
                                                {{ $item->schedule->trainers->pluck('trainer_name')->implode('، ') }}
                                            @else
                                                {{ $item->consultant_name ?? '---' }}
                                            @endif
                                        </span>
                                    </td>

                                    <!-- الإجراءات -->
                                    <td>
                                        <button type="button"
                                                wire:click="openScheduleModal({{ $item->id }})"
                                                class="btn btn-outline-primary btn-sm px-2 py-1 d-inline-flex align-items-center gap-1 shadow-sm">
                                            <i class="bi bi-calendar-check"></i>
                                            <span>{{ $item->schedule ? 'تعديل الجدولة' : 'جدولة وتنشيط' }}</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-5 text-muted">
                                        <div class="py-4">
                                            <i class="bi bi-folder2-open display-4 text-secondary opacity-50 mb-2 d-block"></i>
                                            <span>لا توجد برامج مطابقة لخيارات البحث والحالة الحالية.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($programs->hasPages())
                <div class="card-footer bg-white py-3 border-top">
                    {{ $programs->links() }}
                </div>
            @endif
        </div>

        <!-- Modal نافذة الجدولة بنظام التبويبات (Tabs) -->
        <div wire:ignore.self class="modal fade" id="scheduleModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white py-2">
                        <h6 class="modal-title fw-bold">
                            <i class="bi bi-calendar-event me-1"></i> جدولة وتنشيط البرنامج التدريبي: {{ $selectedProgram->name_ar ?? '' }}
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body bg-light">
                        @if($selectedProgram)
                            <!-- شريط التبويبات العلوي -->
                            <ul class="nav nav-pills nav-fill mb-3 bg-white p-2 rounded shadow-sm">
                                <li class="nav-item">
                                    <button type="button" wire:click="setTab(1)" class="nav-link fw-bold {{ $activeTab == 1 ? 'active bg-primary text-white' : 'text-dark' }}">
                                        1. <i class="bi bi-calendar-check me-1"></i>تواريخ وقاعات التنفيذ
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" wire:click="setTab(2)" class="nav-link fw-bold {{ $activeTab == 2 ? 'active bg-primary text-white' : 'text-dark' }}">
                                        <i class="bi bi-people me-1"></i> 2. توزيع المحاضرين والجلسات
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" wire:click="setTab(3)" class="nav-link fw-bold {{ $activeTab == 3 ? 'active bg-primary text-white' : 'text-dark' }}">
                                        <i class="bi bi-journal-text me-1"></i> 3. التعليمات والملاحظات
                                    </button>
                                </li>
                            </ul>

                            <!-- التبويب الأول -->
                            <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white {{ $activeTab == 1 ? '' : 'd-none' }}">
                                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                                    <i class="bi bi-calendar3 me-1"></i> تحديد تواريخ ومكان تنفيذ البرنامج (المدة: {{ $selectedProgram->duration_days }} أيام - استبعاد الجمعة)
                                </h6>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">تاريخ البداية: *</label>
                                        <input type="date" class="form-control form-control-sm" wire:model.live="start_date">
                                        @error('start_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">تاريخ النهاية (محسوب تلقائياً): *</label>
                                        <input type="date" class="form-control form-control-sm bg-light fw-bold text-dark" wire:model="end_date" readonly>
                                        @error('end_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">فترة التوقيت:</label>
                                        <select class="form-select form-select-sm" wire:model="time_slot">
                                            <option value="صباحي">صباحي</option>
                                            <option value="مسائي">مسائي</option>
                                            <option value="تدريبي كامل اليوم">تدريبي كامل اليوم</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">القاعة التدريبية: *</label>
                                        <select class="form-select form-select-sm" wire:model="hall_id">
                                            <option value="">-- اختر القاعة --</option>
                                            @foreach($halls as $hall)
                                                <option value="{{ $hall->id }}">{{ $hall->name }} (السعة: {{ $hall->chairs_count ?? 25 }})</option>
                                            @endforeach
                                        </select>
                                        @error('hall_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">الحد الأقصى للمقاعد:</label>
                                        <input type="number" class="form-control form-control-sm" wire:model="max_capacity">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">حالة التنشيط والجدولة: *</label>
                                        <select class="form-select form-select-sm fw-bold text-success" wire:model="schedule_status">
                                            <option value="draft">مسودة جدولة</option>
                                            <option value="active">نشط (جاهز للتسجيل)</option>
                                            <option value="in_progress">جاري التنفيذ</option>
                                            <option value="completed">تم التنفيذ</option>
                                            <option value="cancelled">ملغي</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-4 text-end">
                                    <button type="button" wire:click="setTab(2)" class="btn btn-primary btn-sm px-4">
                                        التالي <i class="bi bi-arrow-left ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- التبويب الثاني -->
                            <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white {{ $activeTab == 2 ? '' : 'd-none' }}">
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                    <h6 class="fw-bold text-primary mb-0">
                                        <i class="bi bi-people me-1"></i> توزيع المحاضرين وتخصيص الأيام التدريبية
                                    </h6>
                                    <button type="button" wire:click="addTrainerRow" class="btn btn-outline-success btn-sm py-0 px-2" style="font-size: 11px;">
                                        <i class="bi bi-plus-lg"></i> إضافة محاضر آخر
                                    </button>
                                </div>

                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered table-sm align-middle mb-0">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th style="width: 30%;">اختر المحاضر / الاستشاري *</th>
                                                <th style="width: 40%;">التواريخ والأيام المخصصة له</th>
                                                <th style="width: 25%;">المحاور أو الجلسات التدريبية</th>
                                                <th style="width: 5%;">حذف</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($trainers as $index => $trainer)
                                                <tr>
                                                    <td>
                                                        <div class="position-relative">
                                                            <input type="text"
                                                                   class="form-control form-control-sm"
                                                                   wire:model.live.debounce.300ms="trainerSearch.{{ $index }}"
                                                                   placeholder="اكتب للبحث عن اسم المحاضر..."
                                                                   autocomplete="off">

                                                            <input type="hidden" wire:model="trainers.{{ $index }}.trainer_name">

                                                            @php
                                                                $matchingOptions = $this->getMatchingTrainersForIndex($index);
                                                            @endphp

                                                            @if(!empty($trainerSearch[$index]) && ($trainerSearch[$index] !== ($trainers[$index]['trainer_name'] ?? '')) && $matchingOptions->isNotEmpty())
                                                                <div class="card position-absolute w-100 shadow-sm border mt-1" style="z-index: 1050; max-height: 150px; overflow-y: auto;">
                                                                    <ul class="list-group list-group-flush small mb-0">
                                                                        @foreach($matchingOptions as $trainerOpt)
                                                                            <li>
                                                                                <button type="button"
                                                                                        wire:click.prevent="selectTrainerForRow({{ $index }}, '{{ $trainerOpt->name_ar }}')"
                                                                                        class="list-group-item list-group-item-action py-1 px-2 text-end">
                                                                                    <span class="fw-bold text-dark">{{ $trainerOpt->name_ar }}</span>
                                                                                    <small class="text-muted d-block" style="font-size: 0.7rem;">
                                                                                        الرقم القومي: {{ $trainerOpt->national_id }} | التخصص: {{ $trainerOpt->current_job ?? 'محاضر' }}
                                                                                    </small>
                                                                                </button>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="p-2 border rounded bg-light" style="max-height: 120px; overflow-y: auto;">
                                                            @forelse($availableDates as $dateInfo)
                                                                <div class="form-check form-check-inline me-2 mb-1" style="font-size: 11px;">
                                                                    <input class="form-check-input" type="checkbox"
                                                                           id="tab_date_{{ $index }}_{{ $loop->index }}"
                                                                           value="{{ $dateInfo['date'] }}"
                                                                           wire:model="trainers.{{ $index }}.assigned_days">
                                                                    <label class="form-check-label text-dark fw-semibold" for="tab_date_{{ $index }}_{{ $loop->index }}">
                                                                        {{ $dateInfo['formatted'] }}
                                                                    </label>
                                                                </div>
                                                            @empty
                                                                <span class="text-muted small">يرجى تحديد تاريخ البداية في التبويب الأول أولاً.</span>
                                                            @endforelse
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm" wire:model="trainers.{{ $index }}.session_topics" placeholder="المحاور أو الجلسات...">
                                                    </td>
                                                    <td class="text-center">
                                                        @if(count($trainers) > 1)
                                                            <button type="button" wire:click="removeTrainerRow({{ $index }})" class="btn btn-outline-danger btn-sm py-0 px-1" title="حذف السطر">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <button type="button" wire:click="setTab(1)" class="btn btn-secondary btn-sm px-4">
                                        <i class="bi bi-arrow-right me-1"></i> السابق
                                    </button>
                                    <button type="button" wire:click="setTab(3)" class="btn btn-primary btn-sm px-4">
                                        التالي <i class="bi bi-arrow-left ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- التبويب الثالث -->
                            <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white {{ $activeTab == 3 ? '' : 'd-none' }}">
                                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                                    <i class="bi bi-journal-text me-1"></i> التعليمات والملاحظات التنفيذية
                                </h6>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">ملاحظات عامة للقاعة أو الإدارة:</label>
                                    <textarea class="form-control" wire:model="notes" rows="5" placeholder="أي توجيهات خاصة بتجهيز القاعة، الأجهزة، أو متطلبات الضيافة..."></textarea>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <button type="button" wire:click="setTab(2)" class="btn btn-secondary btn-sm px-4">
                                        <i class="bi bi-arrow-right me-1"></i> السابق
                                    </button>
                                    <button type="button" wire:click="saveSchedule" class="btn btn-success btn-sm px-5 fw-bold">
                                        <i class="bi bi-check-circle me-1"></i> حفظ وتنشيط الجدولة النهائية
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer py-2 bg-white justify-content-between">
                        <span class="text-muted small">التبويب الحالي: {{ $activeTab }} من 3</span>
                        <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('show-schedule-modal', () => {
            var myModal = new bootstrap.Modal(document.getElementById('scheduleModal'));
            myModal.show();
        });

        $wire.on('close-schedule-modal', () => {
            var myModalEl = document.getElementById('scheduleModal');
            var modal = bootstrap.Modal.getInstance(myModalEl);
            if (modal) {
                modal.hide();
            }
        });
    </script>
    @endscript
</div>
