<div id="printable-section">
    <!-- تنسيقات الطباعة بحجم A4 -->
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 5mm;
            }

            .no-print, nav, header, footer, .btn {
                display: none !important;
            }

            body {
                background-color: #fff !important;
                color: #000 !important;
                font-size: 10px !important;
                line-height: 1.2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* إطار يحيط بالصفحة بالكامل باستخدام Flexbox لتوزيع المحتوى بدقة دون صفحات زائدة */
            #printable-section {
                border: 2px solid #333 !important;
                padding: 3mm 2mm 3mm 2mm !important;
                width: 188mm !important;
                height: 285mm !important; /* الارتفاع الفعلي لصفحة A4 داخل الهوامش */
                max-height: 275mm !important;
                background: #fff !important;
                box-sizing: border-box !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                position: relative !important;
                overflow: hidden !important;
            }

            .print-header {
                display: block !important;
                width: 100% !important;
                background: white !important;
                border-bottom: 3px solid #333;
                padding-bottom: 6px;
                margin-bottom: 6px;
                flex-shrink: 0;
            }

            .printable-body-content {
                flex-grow: 1;
                display: flex;
                flex-direction: column;
            }

            /* الفوتر يثبت في أسفل الإطار تماماً دون التسبب في صفحة جديدة */
            .print-footer {
                display: block !important;
                width: 100% !important;
                background: white !important;
                border-top: 2px solid #ccc;
                padding-top: 3px;
                text-align: center;
                flex-shrink: 0;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
                margin-bottom: 0 !important;
                background: transparent !important;
            }

            .card-body {
                padding: 0 !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            th, td {
                padding: 4px 5px !important;
                border: 1px solid #dee2e6 !important;
            }

            tr {
                break-inside: avoid;
            }

            thead {
                display: table-header-group;
            }

            .table-responsive {
                overflow: visible !important;
            }
        }
    </style>

    <!-- الهيدر الخاص بالطباعة -->
    <div class="print-header" style="display: none;">
        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
            <div class="text-start">
                <h6 class="fw-bold m-0 text-dark" style="font-size: 14px;">{{ optional($settings)->governorate_name ?? 'محافظة سوهاج' }}</h6>
                <h6  class="fw-bold m-0 text-dark" style="font-size: 14px;">{{ optional($settings)->directorate_name ?? 'مديرية الصحة بسوهاج' }}</h6>
                <h6  class="fw-bold m-0 text-dark" style="font-size: 14px;">{{ optional($settings)->administration_name ?? 'ادارة التدريب والمدارس' }}</h6>
            </div>
            <div class="text-start">
                @if(optional($settings)->logo_path)
                    <img src="{{ asset('storage/' . $settings->logo_path) }}"
                    alt="Logo" style="height: 70px; width: auto;"
                    class="object-fit-contain">
                @endif
            </div>
        </div>
        <div class="text-center my-1 p-1 bg-light border rounded">
            <h5 class="fw-bold text-dark m-0" style="font-size: 15px;">تقرير حركة أجازات التفرغ والمنح الدراسية</h5>
            <p class="text-muted small m-0" style="font-size: 13px; margin-top: 4px;">
                خلال الفترة من <strong class="text-primary">{{ $fromDate ?? 'بداية العام' }}
                    </strong> إلى <strong class="text-primary">{{ $toDate ?? 'نهاية العام' }}</strong>
            </p>
        </div>
    </div>

    <div class="printable-body-content">
        <div class="container-fluid py-2">

            <!-- رأس الصفحة وأزرار التحكم -->
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h4 class="fw-bold mb-1 text-primary">
                        <i class="bi bi-calendar-range me-2"></i>تقرير حركة أجازات التفرغ
                    </h4>
                    <p class="text-muted small mb-2 ">متابعة تفصيلية لسجلات أجازات التفرغ للدراسات العليا بمديرية الصحة بسوهاج</p>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-sm btn-outline-primary shadow-sm px-3 no-print">
                        <i class="bi bi-printer me-1"></i> طباعة التقرير (A4)
                    </button>
                </div>
            </div>

            <!-- شريط فلاتر البحث المتقدمة -->
            <div class="card border-0 shadow-sm rounded-3 mb-3 bg-light no-print">
                <div class="card-body p-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted mb-1">من تاريخ</label>
                            <input type="date" wire:model.live="fromDate" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted mb-1">إلى تاريخ</label>
                            <input type="date" wire:model.live="toDate" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted mb-1">البحث بجهة العمل</label>
                            <input type="text" wire:model.live.debounce.300ms="searchWorkPlace" placeholder="اكتب اسم جهة العمل..." class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted mb-1">البحث بالوظيفة</label>
                            <input type="text" wire:model.live.debounce.300ms="searchProfession" placeholder="طبيب بشري، صيدلي..." class="form-control form-control-sm">
                        </div>
                        <div class="col-md-12 text-end mt-2">
                            <button wire:click="resetFilters" class="btn btn-sm btn-outline-secondary px-3">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> إعادة تعيين الفلاتر
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- جدول بيانات أجازات التفرغ -->
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-2">الاسم</th>
                                    <th class="py-2">الوظيفة</th>
                                    <th class="py-2">جهة العمل</th>
                                    <th class="py-2">رقم الموبيل</th>
                                    <th class="py-2 text-center">تاريخ بداية التفرغ</th>
                                    <th class="py-2 text-center">تاريخ نهاية التفرغ</th>
                                    <th class="py-2 text-center">حالة موقف التفرغ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leavesData as $leave)
                                    <tr>
                                        <td class="fw-bold text-dark">
                                            {{ $leave->registration->healthProfessional->name ?? ($leave->registration->user->name ?? 'غير محدد') }}
                                        </td>
                                        <td>{{ $leave->registration->healthProfessional->profession ?? '-' }}</td>
                                        <td>{{ $leave->registration->healthProfessional->facility->name ?? 'لا يوجد' }}</td>
                                        <td class="font-monospace">{{ $leave->registration->healthProfessional->phone ?? '-' }}</td>
                                        <td class="text-center font-monospace">{{ $leave->start_date }}</td>
                                        <td class="text-center font-monospace">{{ $leave->end_date }}</td>
                                        <td class="text-center">
                                            @if($leave->end_date && \Carbon\Carbon::parse($leave->end_date)->isFuture())
                                                <span class="badge bg-success-subtle text-success border border-success px-2 py-1" style="font-size: 9px;">ساري</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1" style="font-size: 9px;">منتهي</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">لا توجد سجلات أجازات تفرغ مطابقة لمعايير البحث.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- فوتر الطباعة -->
    <div class="print-footer" style="display: none;">
        <div class="d-flex justify-content-around text-muted" style="font-size: 13px; padding: 2px 0;">
            <span><i class="bi bi-geo-alt me-1"></i>العنوان: {{ optional($officialHeader)->detailed_address ?? 'ميدان الثقافة - سوهاج' }}</span>
            <span><i class="bi bi-envelope me-1"></i>البريد: {{ optional($officialHeader)->official_email ?? 'mahmoudqotb06@gmail.com' }}</span>
            <span><i class="bi bi-telephone me-1"></i>الهاتف: {{ optional($officialHeader)->phone_fax ?? '01020682595' }}</span>
        </div>
    </div>
</div>
