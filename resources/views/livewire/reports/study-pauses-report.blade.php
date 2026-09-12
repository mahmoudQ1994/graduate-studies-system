<div id="printable-section">
    <!-- تنسيقات الشاشة والطباعة المنسقة والأنيقة -->
    <style>
        /* تحسينات عامة لعرض الشاشة لتكون مريحة للعين */
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
                size: A4 portrait;
                margin: 5mm;
            }

            .no-print, nav, header, footer, .btn {
                display: none !important;
            }

            body {
                background-color: #fff !important;
                color: #000 !important;
                font-size: 7px !important;
                line-height: 1.2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0 !important;
                padding: 0 !important;
            }

            #printable-section {
                border: 2px solid #333 !important;
                padding: 3mm 2mm 3mm 2mm !important;
                width: 188mm !important;
                height: 285mm !important;
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
                padding: 3px 4px !important;
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
                    <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Logo" style="height: 70px; width: auto;" class="object-fit-contain">
                @endif
            </div>
        </div>
        <div class="text-center my-1 p-1 bg-light border rounded">
            <h5 class="fw-bold text-dark m-0" style="font-size: 14px;">تقرير متابعة حالات إيقاف القيد والمشكلات الدراسية</h5>
            <p class="text-muted small m-0" style="font-size: 12px; margin-top: 4px;">
                سجل إيقافات القيد والمشكلات الأكاديمية لأطباء الدراسات العليا
            </p>
        </div>
    </div>

    <div class="printable-body-content">
        <div class="container-fluid py-2">

            <!-- رأس الصفحة وأزرار التحكم -->
            <div class="d-flex align-items-center justify-content-between mb-4 px-1">
                <div>
                    <h4 class="fw-bold mb-1 text-primary">
                        <i class="bi bi-pause-circle me-2"></i>متابعة حالات إيقاف القيد والمشكلات الدراسية
                    </h4>
                    <p class="text-muted small mb-0">تتبع أسباب إيقاف القيد والعقبات الأكاديمية للتدخل المبكر بالتنسيق مع الجامعات</p>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-sm btn-outline-primary shadow-sm px-3 no-print">
                        <i class="bi bi-printer me-1"></i> طباعة التقرير (A4)
                    </button>
                </div>
            </div>

            <!-- شريط فلاتر البحث المتقدمة -->
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-light no-print">
                <div class="card-body p-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary mb-1">جهة العمل</label>
                            <input type="text" wire:model.live.debounce.300ms="searchWorkPlace" placeholder="ابحث بجهة العمل..." class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary mb-1">سبب الإيقاف / المشكلة</label>
                            <input type="text" wire:model.live.debounce.300ms="pauseReason" placeholder="ابحث بسبب الإيقاف..." class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-secondary mb-1">من تاريخ</label>
                            <input type="date" wire:model.live="fromDate" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-secondary mb-1">إلى تاريخ</label>
                            <input type="date" wire:model.live="toDate" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2 text-end">
                            <button wire:click="resetFilters" class="btn btn-sm btn-outline-secondary px-3 w-100 shadow-sm">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> إعادة تعيين
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- جدول البيانات مع توسيط المحتوى بالكامل -->
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">الاسم</th>
                                    <th class="py-3">الوظيفة</th>
                                    <th class="py-3">جهة العمل</th>
                                    <th class="py-3">نوع الدراسة</th>
                                    <th class="py-3">تالايخ القيد بالدراسة </th>
                                    <th class="py-3">تاريخ بداية الإيقاف</th>
                                    <th class="py-3">تاريخ نهاية الإيقاف</th>
                                    <th class="py-3">السبب</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pausesData as $item)
                                    @foreach($item->studyPauses as $pause)
                                        <tr>
                                            <td class="fw-bold text-dark">{{ optional($item->healthProfessional)->name ?? '---' }}</td>
                                            <td>{{ optional($item->healthProfessional)->profession ?? '---' }}</td>
                                            <td>{{ optional(optional($item->healthProfessional)->facility)->name ?? '---' }}</td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1" style="font-size: 11px;">
                                                    {{ $item->required_degree ?? '---' }}
                                                </span>
                                            </td>
                                            <td class="font-monospace">
                                                {{ $item->registration_date ? \Carbon\Carbon::parse($item->registration_date)->format('Y-m-d') : '—' }}
                                            </td>
                                            <td class="font-monospace">{{ $pause->pause_start_date ?? '-' }}</td>
                                            <td class="font-monospace">{{ $pause->pause_end_date ?? 'ساري' }}</td>
                                            <td class="text-danger fw-semibold">{{ $pause->pause_reason ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="bi bi-folder-x fs-2 d-block mb-2"></i>
                                            لا توجد حالات إيقاف قيد أو مشكلات مطابقة للبحث الحالي.
                                        </td>
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
