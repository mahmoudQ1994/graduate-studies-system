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
        <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
            <div class="text-start">
                <h6 class="fw-bold m-0 text-dark" style="font-size: 14px;">{{ optional($settings)->governorate_name ?? 'محافظة سوهاج' }}</h6>
                <h6 class="fw-bold m-0 text-dark" style="font-size: 14px;">{{ optional($settings)->directorate_name ?? 'مديرية الصحة بسوهاج' }}</h6>
                <h6 class="fw-bold m-0 text-dark" style="font-size: 14px;">{{ optional($settings)->administration_name ?? 'ادارة التدريب والمدارس' }}</h6>
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
            <h5 class="fw-bold text-dark m-0" style="font-size: 14px;">الأرشيف التاريخي للإنجازات (الحاصلون على الدرجات العلمية)</h5>
            <p class="text-muted small m-0" style="font-size: 13px; margin-top: 12px; line-height: 20px;">
                سجل الكوادر الطبية الحاصلة على (دبلوم ،ماجستير، دكتوراه)
            </p>
        </div>
    </div>

    <div class="printable-body-content">
        <div class="container-fluid py-2">

            <!-- رأس الصفحة وأزرار التحكم -->
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h4 class="fw-bold mb-1 text-primary">
                        <i class="bi bi-award me-2"></i>الأرشيف التاريخي للإنجازات
                    </h4>
                    <p class="text-muted small mb-1">قاعدة بيانات الكوادر الحاصلة على درجات تخصصية لتوظيفها في القيادات الفنية</p>
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
                            <label class="form-label small fw-bold text-muted mb-1">نوع الدرجة العلمية</label>
                            <select wire:model.live="degreeType" class="form-select form-select-sm">
                                <option value="">كل الدرجات</option>
                                <option value="دبلوم">دبلوم</option>
                                <option value="ماجستير">ماجستير</option>
                                <option value="زمالة مصرية">زمالة مصرية</option>
                                <option value="دكتوراه">دكتوراه</option>

                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-1">من تاريخ</label>
                            <input type="date" wire:model.live="fromDate" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-1">إلى تاريخ</label>
                            <input type="date" wire:model.live="toDate" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-1">جهة العمل</label>
                            <input type="text" wire:model.live.debounce.300ms="searchWorkPlace" placeholder="اسم جهة العمل..." class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3 text-end">
                            <button wire:click="resetFilters" class="btn btn-sm btn-outline-secondary px-3">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> إعادة تعيين الفلاتر
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- جدول الأرشيف التاريخي -->
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-2">الاسم</th>
                                    <th class="py-2">الوظيفة</th>
                                    <th class="py-2">جهة العمل</th>
                                    <th class="py-2">الدرجة العلمية</th>
                                    <th class="py-2">التخصص</th>
                                    <th class="py-2 text-center">تاريخ الحصول على الدرجة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($archivesData as $archive)
                                    <tr>
                                        <td class="fw-bold text-dark">
                                            {{ optional($archive->healthProfessional)->name ?? 'غير محدد' }}
                                        </td>
                                        <td>{{ optional($archive->healthProfessional)->profession ?? '-' }}</td>
                                        <td>{{ optional(optional($archive->healthProfessional)->facility)->name ?? 'لا يوجد' }}</td>
                                        <td><span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1" style="font-size: 9px;">{{ $archive->required_degree ?? '-' }}</span></td>
                                        <td>{{ $archive->required_specialty ?? '-' }}</td>
                                        <td class="text-center font-monospace">{{ $archive->nominated_degree_date ? $archive->nominated_degree_date->format('Y-m-d') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">لا توجد سجلات أرشيفية مطابقة لمعايير البحث.</td>
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
        <div class="d-flex justify-content-around text-muted" style="font-size: 10px; padding: 2px 0;">
            <span><i class="bi bi-geo-alt me-1"></i>العنوان: {{ optional($officialHeader)->detailed_address ?? 'ميدان الثقافة - سوهاج' }}</span>
            <span><i class="bi bi-envelope me-1"></i>البريد: {{ optional($officialHeader)->official_email ?? 'mahmoudqotb06@gmail.com' }}</span>
            <span><i class="bi bi-telephone me-1"></i>الهاتف: {{ optional($officialHeader)->phone_fax ?? '01020682595' }}</span>
        </div>
    </div>
</div>
