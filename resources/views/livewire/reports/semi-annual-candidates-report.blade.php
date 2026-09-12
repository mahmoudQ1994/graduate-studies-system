<div id="printable-section">
    @php
        $pageSettings = \App\Models\Setting::first();
        $headerInfo = \App\Models\OfficialHeader::first();
    @endphp

    <!-- تنسيقات الشاشة والطباعة المنسقة والأنيقة -->
    <style>
        .card {
            border-radius: 0.75rem !important;
        }
        .form-control, .form-select {
            font-size: 0.875rem !important;
            padding: 0.5rem 0.75rem !important;
            border-color: #dee2e6 !important;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important;
        }
        table.table th {
            background-color: #f8f9fa !important;
            color: #495057 !important;
            font-weight: 600 !important;
            font-size: 0.9rem !important;
            vertical-align: middle !important;
        }
        table.table td {
            font-size: 0.875rem !important;
            vertical-align: middle !important;
            color: #212529 !important;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 12mm 5mm 12mm 5mm; /* ترك مساحة كافية للهيدر والفوتر في أعلى وأسفل الصفحة */
            }

            .no-print, nav, header, footer, .btn, .pagination-container, .card-footer {
                display: none !important;
            }

            body {
                background-color: #fff !important;
                color: #000 !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            #printable-section {
                width: 100% !important;
                background: #fff !important;
            }

            /* جعل الهيدر يظهر في أعلى كل صفحة مطبوعة تلقائياً */
            .print-header {
                display: block !important;
                position: running(headerContainer);
            }

            /* جعل الفوتر يظهر في أسفل كل صفحة مطبوعة تلقائياً */
            .print-footer {
                display: block !important;
                position: running(footerContainer);
            }

            @page {
                @top-center {
                    content: element(printHeader);
                }
                @bottom-center {
                    content: element(printFooter);
                }
            }

            .print-header-element {
                position: running(printHeader);
                width: 100%;
            }

            .print-footer-element {
                position: running(printFooter);
                width: 100%;
            }

            /* تكرار ترويسة الجدول في كل صفحة جديدة */
            thead {
                display: table-header-group !important;
            }

            tr {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            th, td {
                padding: 2px 3px !important;
                border: 1px solid #999 !important;
                font-size: 6px !important;
                white-space: normal !important;
                word-wrap: break-word !important;
            }
        }
    </style>

    <!-- الهيدر الخاص بالطباعة المتكرر أعلى كل صفحة -->
    <div class="print-header-element">
        <div class="print-header" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                <div class="text-start">
                    <h6 class="fw-bold m-0 text-dark" style="font-size: 11px;">{{ optional($pageSettings)->governorate_name ?? 'محافظة سوهاج' }}</h6>
                    <h6 class="fw-bold m-0 text-dark" style="font-size: 11px;">{{ optional($pageSettings)->directorate_name ?? 'مديرية الصحة بسوهاج' }}</h6>
                    <h6 class="fw-bold m-0 text-dark" style="font-size: 11px;">{{ optional($pageSettings)->administration_name ?? 'ادارة التدريب والمدارس' }}</h6>
                </div>
                <div class="text-start">
                    @if(optional($pageSettings)->logo_path)
                        <img src="{{ asset('storage/' . $pageSettings->logo_path) }}" alt="Logo" style="height: 35px; width: auto;" class="object-fit-contain">
                    @endif
                </div>
            </div>
            <div class="text-center my-1 p-1 bg-light border rounded">
                <h5 class="fw-bold text-dark m-0" style="font-size: 11px;">الإحصائية النصف سنوية للمرشحين</h5>
                <p class="text-muted small m-0" style="font-size: 8px; margin-top: 1px;">
                    عرض وتحليل بيانات المرشحين للدراسات العليا المتقدمين عبر الفترات المختلفة
                </p>
            </div>
        </div>
    </div>

    <!-- المحتوى المرئي على الشاشة -->
    <div class="printable-body-content">
        <div class="container-fluid px-4 py-4">
            <!-- عنوان التقرير والأزرار -->
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-3 border-bottom gap-3">
                <div>
                    <h4 class="fw-bold mb-1 text-primary">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>الإحصائية النصف سنوية للمرشحين
                    </h4>
                    <p class="text-muted small mb-0">عرض وتحليل بيانات المرشحين للدراسات العليا المتقدمين عبر الفترات المختلفة</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold border border-primary border-opacity-25 shadow-sm">
                        <i class="bi bi-people-fill me-1"></i> إجمالي النتائج: {{ $totalCount }} مرشح
                    </span>

                    <!-- زر تحميل إكسيل -->
                    <button wire:click="exportExcel" class="btn btn-sm btn-success shadow-sm px-3 no-print">
                        <i class="bi bi-file-earmark-excel me-1"></i> تحميل إكسيل (Excel)
                    </button>

                    <!-- زر طباعة جميع النتائج -->
                    <button wire:click="printAll" class="btn btn-sm btn-outline-primary shadow-sm px-3 no-print">
                        <i class="bi bi-printer me-1"></i> طباعة كل النتائج (A4 عرضي)
                    </button>
                </div>
            </div>

            <!-- بطاقة الفلاتر والبحث المتقدم -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white border-top border-primary border-3 no-print">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <label class="form-label small fw-bold text-secondary mb-1">بحث بالاسم / الرقم القومي</label>
                            <div class="input-group input-group-sm shadow-sm">
                                <span class="input-group-text bg-light border-end-0 rounded-start-pill px-3 text-muted">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm bg-light border-start-0 rounded-end-pill px-3" placeholder="اكتب للبحث...">
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-6">
                            <label class="form-label small fw-bold text-secondary mb-1">من تاريخ</label>
                            <input type="date" wire:model.live="fromDate" class="form-control form-control-sm bg-light rounded-pill px-3 shadow-sm">
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-6">
                            <label class="form-label small fw-bold text-secondary mb-1">إلى تاريخ</label>
                            <input type="date" wire:model.live="toDate" class="form-control form-control-sm bg-light rounded-pill px-3 shadow-sm">
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-6">
                            <label class="form-label small fw-bold text-secondary mb-1">نوع الترشيح</label>
                            <select wire:model.live="sponsorshipType" class="form-select form-select-sm bg-light rounded-pill px-3 shadow-sm">
                                <option value="">كل أنواع الترشيح</option>
                                <option value="الاساسية">الاساسية</option>
                                <option value="الاستثنائية">الاستثنائية</option>
                                <option value="التكميلية">التكميلية</option>
                                <option value="على النفقة الخاصة">على النفقة الخاصة</option>
                            </select>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <label class="form-label small fw-bold text-secondary mb-1">نوع الدراسة</label>
                            <select wire:model.live="requiredDegree" class="form-select form-select-sm bg-light rounded-pill px-3 shadow-sm">
                                <option value="">كل الدرجات العلمية</option>
                                <option value="دبلوم">دبلوم</option>
                                <option value="ماجستير">ماجستير</option>
                                <option value="دكتوراه">دكتوراه</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- جدول البيانات بتصميم أنيق ومريح -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center text-nowrap">
                            <thead class="bg-primary text-white small">
                                <tr>
                                    <th style="width: 3%;" class="py-3 px-1">#</th>
                                    <th style="width: 11%;" class="text-start py-3 px-1">الاسم</th>
                                    <th style="width: 8%;" class="py-3 px-1">الرقم القومي</th>
                                    <th style="width: 7%;" class="py-3 px-1">المؤهل</th>
                                    <th style="width: 7%;" class="py-3 px-1">الوظيفة</th>
                                    <th style="width: 9%;" class="py-3 px-1">جهة العمل</th>
                                    <th style="width: 6%;" class="py-3 px-1">سنة الترشيح</th>
                                    <th style="width: 7%;" class="py-3 px-1">نوع الترشيح</th>
                                    <th style="width: 8%;" class="py-3 px-1">تخصص النيابة</th>
                                    <th style="width: 7%;" class="py-3 px-1">تاريخ النيابة</th>
                                    <th style="width: 8%;" class="py-3 px-1">جامعة الترشيح</th>
                                    <th style="width: 7%;" class="py-3 px-1">نوع الدراسة</th>
                                    <th style="width: 12%;" class="py-3 px-1">موقف التفرغ الدراسي</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                @forelse($candidates as $index => $item)
                                    @php
                                        $prof = $item->healthProfessional;
                                        $qual = $prof?->qualification;
                                        $mov = $prof?->medicalMovements?->first();

                                        $activeLeave = $item->studyLeaves()
                                            ->where(function($q) {
                                                $q->whereNull('actual_return_date')
                                                  ->orWhere('actual_return_date', '>', now());
                                            })->first();
                                    @endphp
                                    <tr>
                                        <td class="text-muted fw-semibold px-1">{{ $isPrinting ? ($index + 1) : ($candidates->firstItem() + $index) }}</td>
                                        <td class="fw-bold text-start text-primary px-1">{{ $prof->name ?? '-' }}</td>
                                        <td class="px-1"><span class="font-monospace text-secondary">{{ $prof->national_id ?? '-' }}</span></td>
                                        <td class="px-1">{{ $qual->qualification ?? '-' }}</td>
                                        <td class="px-1">{{ $prof->profession ?? '-' }}</td>
                                        <td class="px-1">
                                            <span>{{ $prof->facility->name ?? '-' }}</span>
                                        </td>
                                        <td class="px-1">{{ $item->application_date ? date('Y', strtotime($item->application_date)) : '-' }}</td>
                                        <td class="px-1">
                                            <span>{{ $item->sponsorship_type ?? '-' }}</span>
                                        </td>
                                        <td class="px-1">{{ $mov->specialty ?? '-' }}</td>
                                        <td class="px-1">{{ $mov->movement_date ?? '-' }}</td>
                                        <td class="px-1">{{ $item->required_university ?? '-' }}</td>
                                        <td class="px-1">
                                            <span>{{ $item->required_degree ?? '-' }}</span>
                                        </td>
                                        <td class="px-1">
                                            @if($activeLeave)
                                                <span>في إجازة: {{ $activeLeave->leave_type }}</span>
                                            @else
                                                <span>لا يوجد</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center py-5 text-muted">
                                            <div class="py-4">
                                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                                <h6 class="fw-bold text-secondary">لا توجد بيانات متاحة مطابقة لخيارات البحث</h6>
                                                <p class="small text-muted mb-0">جرب تغيير خيارات الفلاتر أعلاه لعرض النتائج</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- تذييل الجدول وترتيب أزرار الـ Pagination (تختفي عند الطباعة) -->
                <div class="card-footer bg-white py-3 px-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 no-print">
                    <div class="small text-muted fw-semibold">
                        عرض الصفوف من <span class="text-dark fw-bold">{{ $isPrinting ? 1 : ($candidates->firstItem() ?? 0) }}</span> إلى <span class="text-dark fw-bold">{{ $isPrinting ? count($candidates) : ($candidates->lastItem() ?? 0) }}</span> من إجمالي <span class="text-primary fw-bold">{{ $totalCount }}</span> مرشح
                    </div>
                    <div class="pagination-container mb-0" dir="ltr">
                        @if(!$isPrinting)
                            {{ $candidates->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- فوتر الطباعة المتكرر أسفل كل صفحة -->
    <div class="print-footer-element">
        <div class="print-footer" style="display: none;">
            <div class="d-flex justify-content-around text-muted border-top pt-1" style="font-size: 8px;">
                <span><i class="bi bi-geo-alt me-1"></i>العنوان: {{ optional($headerInfo)->detailed_address ?? 'ميدان الثقافة - سوهاج' }}</span>
                <span><i class="bi bi-envelope me-1"></i>البريد: {{ optional($headerInfo)->official_email ?? 'mahmoudqotb06@gmail.com' }}</span>
                <span><i class="bi bi-telephone me-1"></i>الهاتف: {{ optional($headerInfo)->phone_fax ?? '01020682595' }}</span>
            </div>
        </div>
    </div>

    <style>
        .pagination-container nav svg {
            width: 18px;
            height: 18px;
        }
        .pagination-container .pagination {
            margin-bottom: 0;
            gap: 4px;
            align-items: center;
        }
        .pagination-container .page-item .page-link {
            border-radius: 50px;
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
            color: #0d6efd;
            border-color: #dee2e6;
            transition: all 0.2s ease-in-out;
        }
        .pagination-container .page-item .page-link:hover {
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        .pagination-container .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
            box-shadow: 0 2px 4px rgba(13, 110, 253, 0.25);
        }
        .pagination-container .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
    </style>
</div>

@script
<script>
    $wire.on('trigger-print', () => {
        setTimeout(() => {
            window.print();
            $wire.call('resetPrintState');
        }, 600);
    });
</script>
@endscript
