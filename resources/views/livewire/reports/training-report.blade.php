<div id="printable-section">
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

        /* إجبار محتوى خلايا الجدول على صف واحد للشاشة مع تفعيل السكرول الأفقي */
        .table-responsive {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }

        table.table th,
        table.table td {
            white-space: nowrap !important;
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
                margin: 2mm;
            }

            /* فرض الاتجاه العرضي على مستوى عنصر الجسم والصفحة لمنع التداخل */
            html, body {
                width: 297mm !important;
                height: 206mm !important;
                writing-mode: horizontal-tb !important;
            }

            .no-print, nav, header, footer, .btn {
                display: none !important;
            }

            body {
                background-color: #fff !important;
                color: #000 !important;
                font-size: 9px !important;
                line-height: 1.3 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0 !important;
                padding: 0 !important;
            }

            #printable-section {
                border: 2px solid #333 !important;
                padding: 2mm 2mm 2mm 2mm !important;
                width: 277mm !important;
                height: 192mm !important;
                max-height: 198mm !important;
                background: #fff !important;
                box-sizing: border-box !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                position: relative !important;
                overflow: hidden !important;
                margin: 0 auto !important;
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
                border-top: 3px solid #ccc;
                padding-top: 4px;
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
                padding: 5px 6px !important;
                border: 1px solid #dee2e6 !important;
                white-space: nowrap !important;
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
        <div class="d-flex justify-content-between align-items-center
        mb-1 ">
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

    </div>

    <div class="printable-body-content">
        <div class="container-fluid py-2">

            <!-- رأس الصفحة وأزرار التحكم -->
            <div class="d-flex align-items-center justify-content-between mb-4 px-1">
                <div>
                    <h4 class="fw-bold mb-1 text-primary">
                        <i class="bi bi-file-earmark-bar-graph-fill me-2"></i>تقرير الإفاد ومد الإفاد للتدريب
                    </h4>
                    <p class="text-muted small mb-0">استعراض ومتابعة كافة قرارات الإفاد والتدريب للكوادر الصحية</p>
                </div>
                <div class="d-flex gap-2">
                    <button wire:click="exportExcel" class="btn btn-sm btn-outline-success shadow-sm px-3 no-print">
                        <i class="bi bi-file-earmark-excel-fill me-1"></i> تصدير Excel
                    </button>
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
                            <label class="form-label small fw-bold text-secondary mb-1">بحث بالاسم أو الرقم القومي</label>
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="اكتب للبحث..." class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary mb-1">جهة العمل الأصلية</label>
                            <select wire:model.live="facility_id" class="form-select form-select-sm">
                                <option value="">-- كل جهات العمل --</option>
                                @foreach($facilities as $facility)
                                    <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary mb-1">جهة الإفاد</label>
                            <input type="text" wire:model.live.debounce.300ms="training_entity" placeholder="اسم جهة التدريب..." class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary mb-1">نوع الإجراء</label>
                            <select wire:model.live="action_type" class="form-select form-select-sm">
                                <option value="">-- كل الإجراءات --</option>
                                <option value="إيفاد">إيفاد</option>
                                <option value="مد إيفاد">مد إيفاد</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary mb-1">من تاريخ </label>
                            <input type="date" wire:model.live="date_from" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary mb-1">إلى تاريخ </label>
                            <input type="date" wire:model.live="date_to" class="form-control form-control-sm">
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
                                    <th class="py-3">#</th>
                                    <th class="py-3">الاسم</th>
                                    <th class="py-3">الرقم القومي</th>
                                    <th class="py-3">الوظيفة</th>
                                    <th class="py-3">جهة العمل</th>
                                    <th class="py-3">جهة الإفاد</th>
                                    <th class="py-3">نوع الإجراء</th>
                                    <th class="py-3">تاريخ البداية</th>
                                    <th class="py-3">تاريخ النهاية</th>
                                    <th class="py-3">مدة الإفاد</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trainings as $index => $training)
                                    <tr>
                                        <td>{{ $trainings->firstItem() + $index }}</td>
                                        <td class="fw-bold text-dark">{{ $training->healthProfessional->name ?? '-' }}</td>
                                        <td>{{ $training->healthProfessional->national_id ?? '-' }}</td>
                                        <td>{{ $training->healthProfessional->profession ?? '-' }}</td>
                                        <td>{{ $training->healthProfessional->facility->name ?? '-' }}</td>
                                        <td>{{ $training->training_entity }}</td>
                                        <td>
                                            <span class="badge {{ $training->action_type == 'إيفاد' ? 'bg-success-subtle text-success border border-success' : 'bg-warning-subtle text-warning border border-warning' }} px-2 py-1" style="font-size: 11px;">
                                                {{ $training->action_type }}
                                            </span>
                                        </td>
                                        <td class="font-monospace">{{ $training->start_date }}</td>
                                        <td class="font-monospace">{{ $training->end_date }}</td>
                                        <td><span class="fw-bold">{{ $training->duration_months }}</span> شهر</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5 text-muted">
                                            <i class="bi bi-folder-x fs-2 d-block mb-2"></i>
                                            لا توجد بيانات مطابقة للبحث أو الفلاتر المحددة.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 no-print border-0">
                    {{ $trainings->links() }}
                </div>
            </div>

        </div>
    </div>

    <!-- فوتر الطباعة -->
    <div class="print-footer" style="display: none;">
        <div class="d-flex justify-content-around text-muted" style="font-size: 10px; padding: 2px 0;">
            <span><i class="bi bi-geo-alt me-1"></i>العنوان: {{ optional($headerInfo)->detailed_address ?? 'ميدان الثقافة - سوهاج' }}</span>
            <span><i class="bi bi-envelope me-1"></i>البريد: {{ optional($headerInfo)->official_email ?? 'mahmoudqotb06@gmail.com' }}</span>
            <span><i class="bi bi-telephone me-1"></i>الهاتف: {{ optional($headerInfo)->phone_fax ?? '01020682595' }}</span>
        </div>
    </div>
</div>
