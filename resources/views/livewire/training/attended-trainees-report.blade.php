<div class="container-fluid px-4 py-4" style="background-color: #f4f6f9; min-height: 100vh;">

    <!-- رأس الصفحة -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 bg-white p-4 rounded-4 shadow-sm">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <div class="bg-primary-subtle text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-file-earmark-spreadsheet-fill fs-5"></i>
                </div>
                <span>كشوف المتدربين المقبولين وتقارير الحضور</span>
            </h4>
            <p class="text-muted small mb-0 ms-5">استعراض وتصدير كشوف المقبولين لكل برنامج وتاريخ تنفيذي بكل دقة.</p>
        </div>

        @if($selected_schedule_id && $approvedTrainees->total() > 0)
            <button type="button" wire:click="exportExcel" class="btn btn-success px-4 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2 fw-semibold transition-all">
                <i class="bi bi-file-earmark-excel fs-5"></i>
                <span>تصدير الكشف إلى Excel</span>
            </button>
        @endif
    </div>

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 mb-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- بطاقة الفلاتر (اسم البرنامج ثم تاريخ التنفيذ) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="row g-3">
                <!-- الحقل الأول: اختيار اسم البرنامج -->
            <div class="col-md-4 position-relative">
                <label class="form-label fw-bold text-secondary small">1. اسم البرنامج التدريبي (اكتب للبحث):</label>

                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text"
                        class="form-control bg-light border-start-0 rounded-end-pill px-3 py-2"
                        wire:model.live.debounce.300ms="programSearch"
                        placeholder="ابحث باسم البرنامج التدريبي...">
                </div>
                <!-- قائمة النتائج التفاعلية المنسدلة -->
                @if(strlen(trim($programSearch)) >= 1 && !empty($this->matchingPrograms) && $this->matchingPrograms->count() > 0 && !$selected_program_id)
                    <ul class="list-group position-absolute w-100 shadow-sm rounded-4 mt-1 overflow-hidden" style="z-index: 1050; max-height: 220px; overflow-y: auto;">
                        @foreach($this->matchingPrograms as $prog)
                            <li wire:click="selectProgram({{ $prog->id }}, '{{ $prog->name_ar }}')"
                                class="list-group-item list-group-item-action py-2 px-3 bg-white border-0 border-bottom cursor-pointer text-start"
                                style="cursor: pointer;">
                                <i class="bi bi-journal-code text-primary me-2"></i> {{ $prog->name_ar }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            

                <!-- الحقل الثاني: اختيار تاريخ التنفيذ المتاح لهذا البرنامج -->
                <div class="col-md-5">
                    <label class="form-label fw-bold text-secondary small">2. تاريخ التنفيذ المتاح:</label>
                    <select class="form-select rounded-pill border-light bg-light px-3 py-2" wire:model.live="selected_schedule_id" @if(!$selected_program_id) disabled @endif>
                        <option value="">-- اختر تاريخ التنفيذ --</option>
                        @foreach($availableSchedules as $sch)
                            <option value="{{ $sch->id }}">
                                من {{ $sch->start_date }} إلى {{ $sch->end_date }} (السعة: {{ $sch->max_capacity }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- حقل البحث السريع -->
                @if($selected_schedule_id)
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-semibold small">بحث بالاسم أو القومي:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-start-0 rounded-end-pill pe-3" wire:model.live.debounce.300ms="search" placeholder="ابحث...">
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- تفاصيل البرنامج المختار -->
    @if($selectedScheduleInfo)
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4 border-start border-success border-4 d-flex flex-row justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-1">{{ $selectedScheduleInfo->program_name }}</h5>
                <span class="text-muted small">فترة التنفيذ المحددة: <strong class="text-primary">{{ $selectedScheduleInfo->start_date }}</strong> إلى <strong class="text-primary">{{ $selectedScheduleInfo->end_date }}</strong></span>
            </div>
            <div>
                <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 rounded-pill border border-success-subtle">
                    إجمالي المقبولين: {{ $approvedTrainees->total() }} متدرب
                </span>
            </div>
        </div>
    @endif

    <!-- الجدول الإداري الشامل -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-body p-0">
            @if(!$selected_schedule_id)
                <div class="text-center py-5 text-muted">
                    <div class="py-5">
                        <i class="bi bi-calendar-check fs-1 d-block mb-3 text-primary opacity-50"></i>
                        <h6 class="fw-bold text-secondary">يرجى اختيار "اسم البرنامج" ثم "تاريخ التنفيذ المتاح" لعرض كشف المقبولين.</h6>
                    </div>
                </div>
            @else
                <div class="table-responsive" style="max-height: 65vh; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0 text-center" style="font-size: 0.85rem; white-space: nowrap;">
                        <thead class="table-light text-secondary sticky-top shadow-sm" style="z-index: 1;">
                            <tr style="height: 50px;">
                                <th class="py-3 px-2">م</th>
                                <th class="text-start py-3 px-3">اسم المتدرب</th>
                                <th class="py-3 px-3">الرقم القومي</th>
                                <th class="py-3 px-3">المؤهل</th>
                                <th class="py-3 px-3">الوظيفة</th>
                                <th class="py-3 px-3">جهة العمل</th>
                                <th class="py-3 px-3">الإدارة</th>
                                <th class="py-3 px-3">رقم الهاتف</th>
                                <th class="py-3 px-3">الإيميل</th>
                                <th class="py-3 px-3">موقف الاعتماد</th>
                                <th class="py-3 px-3">حالة الحضور العامة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($approvedTrainees as $index => $row)
                                <tr style="height: 55px;" class="border-bottom">
                                    <td class="text-muted px-2 fw-semibold">{{ $approvedTrainees->firstItem() + $index }}</td>
                                    <td class="fw-bold text-dark text-start px-3">{{ $row->name_ar }}</td>
                                    <td class="font-monospace text-secondary px-3">{{ $row->national_id }}</td>
                                    <td class="text-secondary px-3">{{ $row->qualification ?? '---' }}</td>
                                    <td class="text-secondary px-3">{{ $row->job_title ?? '---' }}</td>
                                    <td class="text-secondary px-3">{{ $row->workplace ?? '---' }}</td>
                                    <td class="text-secondary px-3">{{ $row->department ?? '---' }}</td>
                                    <td class="font-monospace text-muted px-3">{{ $row->phone_number ?? '---' }}</td>
                                    <td class="font-monospace text-muted px-3">{{ $row->email ?? '---' }}</td>
                                    <td class="px-3">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">مقبول رسمياً</span>
                                    </td>
                                    <td class="px-3">
                                        @if($row->attendance_status == 'attended')
                                            <span class="badge bg-success text-white px-3 py-1 rounded-pill">حاضر</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill">قيد الانتظار</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-5 text-muted">
                                        <div class="py-4">
                                            <i class="bi bi-inbox fs-2 d-block mb-2 text-black-50"></i>
                                            <p class="mb-0 fw-semibold">لا يوجد متدربين تم الموافقة على قبولهم في تاريخ التنفيذ هذا.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if($selected_schedule_id && $approvedTrainees->hasPages())
            <div class="card-footer bg-white py-3 border-top">
                {{ $approvedTrainees->links() }}
            </div>
        @endif
    </div>
</div>
