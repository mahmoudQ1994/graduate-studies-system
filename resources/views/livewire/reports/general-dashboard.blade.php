<div id="printable-section">

    <!-- تنسيقات وأكواد CSS الخاصة بالطباعة بحجم A4 والهيدر والفوتر الثابتين -->
<style>
        @media print {
            /* تقليص الهامش العلوي إلى الحد الأدنى لرفع المحتوى والفواتير لأعلى الصفحة */
            @page {
                size: A4 portrait;
                margin: 5mm 10mm 15mm 10mm;
            }

            .no-print, nav, header, footer, .btn {
                display: none !important;
            }

            body {
                background-color: #fff !important;
                color: #000 !important;
                font-size: 10px !important;
                line-height: 1.25 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
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

            /* الهيدر يبدأ من أعلى نقطة في الصفحة تماماً */
            .print-header {
                display: block !important;
                width: 100% !important;
                background: white !important;
                border-bottom: 2px solid #333;
                padding-bottom: 4px;
                margin-bottom: 8px;
            }

            /* الفوتر مثبت دائماً في أسفل الورقة نهائياً */
            .print-footer {
                display: block !important;
                position: fixed !important;
                bottom: 2% !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                background: white !important;
                border-top: 1px solid #ccc;
                padding-top: 4px;
                text-align: center;
                z-index: 9999;
            }

            .card {
                border: 1px solid #ced4da !important;
                box-shadow: none !important;
                margin-bottom: 6px !important;
                break-inside: avoid;
            }

            .card-header {
                background-color: #f8f9fa !important;
                color: #000 !important;
                border-bottom: 1px solid #ced4da !important;
                print-color-adjust: exact;
                padding: 4px 8px !important;
            }

            .print-text-dark {
                color: #000 !important;
                font-weight: 700 !important;
            }

            table {
                width: 100% !important;
                break-inside: auto;
            }

            tr {
                break-inside: avoid;
                break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            .table-responsive {
                overflow: visible !important;
            }
        }
    </style>

    <!-- الهيدر الخاص بالطباعة فقط -->
    <div class="print-header" style="display: none;">
        <div class="row align-items-center mb-2 pb-2 border-bottom">
            <div class="col-8 text-start">
                <h6 class="fw-bold m-0 text-dark" style="font-size: 12px;">{{ $settings->governorate_name ?? 'محافظة سوهاج' }}</h6>
                <h6 class="fw-bold m-0 text-primary" style="font-size: 11px;">{{ $settings->directorate_name ?? 'مديرية الصحة بسوهاج' }}</h6>
                <h6 class="fw-bold m-0 text-secondary" style="font-size: 10px;">{{ $settings->administration_name ?? 'ادارة التدريب والمدارس' }}</h6>
            </div>

        </div>

        <!-- عنوان بمنتصف الصفحة -->
        <div class="text-center my-2 p-1 bg-light border rounded">
            <h5 class="fw-bold text-dark m-0" style="font-size: 13px;">
                استعلام عن موقف الدراسات العليا بادارة التدريب والمدارس
            </h5>
            <!--  خلال الفترة من الى تاريخ -->
            <p class="text-muted small m-0" style="font-size: 10px; margin-top: 2px;">
                خلال الفترة من <strong class="text-primary">{{ $fromDate ?? 'بداية العام' }}</strong> إلى <strong class="text-primary">{{ $toDate ?? 'نهاية العام' }}</strong>
            </p>

        </div>
    </div>

    <div class="container-fluid py-3">

        <!-- رأس الصفحة وأزرار التحكم -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-primary">
                    <i class="bi bi-speedometer2 me-2"></i>لوحة المؤشرات العامة والتقارير الإحصائية
                </h4>
                <p class="text-muted small mb-0">نظرة شاملة ومحدثة على الكادر الطبي، تسجيلات الدراسات العليا، وأجازات التفرغ بمديرية الصحة بسوهاج</p>
            </div>
            <div>
                <button onclick="window.print()" class="btn btn-sm btn-outline-primary shadow-sm px-3 no-print">
                    <i class="bi bi-printer me-1"></i> طباعة اللوحة (A4)
                </button>
            </div>
        </div>

        <!-- [0] شريط فلاتر البحث والتواريخ -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-light no-print">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted mb-1">من تاريخ</label>
                        <input type="date" wire:model.live="fromDate" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted mb-1">إلى تاريخ</label>
                        <input type="date" wire:model.live="toDate" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button wire:click="resetFilters" class="btn btn-sm btn-outline-secondary w-100">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> إعادة تعيين
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- [1] البطاقات الإحصائية الرئيسية (KPI Widgets) -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-3 border-start border-primary border-4">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted d-block small mb-1">إجمالي الكادر الطبي</span>
                                <h3 class="fw-bold text-dark mb-0 font-monospace">{{ $totalProfessionals }}</h3>
                            </div>
                            <div class="bg-primary-subtle text-primary p-3 rounded-3 fs-4">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-3 border-start border-success border-4">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted d-block small mb-1">إجمالي طلبات الدراسات العليا</span>
                                <h3 class="fw-bold text-dark mb-0 font-monospace">{{ $totalRegistrations }}</h3>
                            </div>
                            <div class="bg-success-subtle text-success p-3 rounded-3 fs-4">
                                <i class="bi bi-journal-bookmark-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-3 border-start border-warning border-4">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted d-block small mb-1">سجلات أجازات التفرغ</span>
                                <h3 class="fw-bold text-dark mb-0 font-monospace">{{ $totalLeaves }}</h3>
                            </div>
                            <div class="bg-warning-subtle text-warning p-3 rounded-3 fs-4">
                                <i class="bi bi-calendar-range-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-3 border-start border-danger border-4">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted d-block small mb-1">حالات إيقاف القيد (سارية)</span>
                                <h3 class="fw-bold text-dark mb-0 font-monospace">{{ $totalPauses }}</h3>
                            </div>
                            <div class="bg-danger-subtle text-danger p-3 rounded-3 fs-4">
                                <i class="bi bi-pause-circle-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- [2] الجداول الإحصائية التفصيلية (التسجيلات والحالات) -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark m-0">
                            <i class="bi bi-pie-chart-fill text-primary me-2"></i>توزيع التسجيلات حسب الدرجة العلمية
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-2">الدرجة العلمية</th>
                                        <th class="py-2 text-center">عدد المسجلين</th>
                                        <th class="py-2 text-end">النسبة / التفاصيل</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($degreesBreakdown as $degree => $count)
                                        <tr>
                                            <td class="fw-bold text-dark">{{ $degree ?? 'غير محدد' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1 font-monospace">{{ $count }}</span>
                                            </td>
                                            <td class="text-end text-muted small">
                                                {{ $totalRegistrations > 0 ? round(($count / $totalRegistrations) * 100, 1) : 0 }}% من الإجمالي
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-3 text-muted">لا توجد بيانات مسجلة حالياً.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark m-0">
                            <i class="bi bi-bar-chart-fill text-success me-2"></i>حالة الدراسة الحالية للمرشحين
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-2">حالة الدراسة</th>
                                        <th class="py-2 text-center">العدد</th>
                                        <th class="py-2 text-end">مؤشر الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($statusBreakdown as $status => $count)
                                        <tr>
                                            <td class="fw-bold text-dark">{{ $status ?? 'قيد الدراسة' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-success-subtle text-success border border-success px-2 py-1 font-monospace">{{ $count }}</span>
                                            </td>
                                            <td class="text-end">
                                                @if($status === 'حصل على الدرجة')
                                                    <span class="badge bg-success" style="font-size: 9px;">مكتمل</span>
                                                @else
                                                    <span class="badge bg-warning text-dark" style="font-size: 9px;">جاري المتابعة</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-3 text-muted">لا توجد حالات مسجلة.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- [3] الجداول الإحصائية المتقدمة (الوظائف وجهات العمل) -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark m-0">
                            <i class="bi bi-person-badge-fill text-info me-2"></i>توزيع الكادر الطبي حسب الوظيفة
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-2">الوظيفة الأساسية</th>
                                        <th class="py-2 text-center">العدد</th>
                                        <th class="py-2 text-end">النسبة المئوية</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($professionBreakdown as $profession => $count)
                                        <tr>
                                            <td class="fw-bold text-dark">{{ $profession ?? 'غير محدد' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info-subtle text-info border border-info px-2 py-1 font-monospace">{{ $count }}</span>
                                            </td>
                                            <td class="text-end text-muted small">
                                                {{ $totalProfessionals > 0 ? round(($count / $totalProfessionals) * 100, 1) : 0 }}%
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-3 text-muted">لا توجد وظائف مسجلة.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark m-0">
                            <i class="bi bi-hospital-fill text-danger me-2"></i>أبرز جهات العمل والانتداب (أعلى 5 جهات)
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-2">جهة العمل / الانتداب</th>
                                        <th class="py-2 text-center">عدد الكوادر</th>
                                        <th class="py-2 text-end">المؤشر</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($facilityBreakdown as $facility => $count)
                                        <tr>
                                            <td class="fw-bold text-dark">{{ $facility ?? 'غير محدد' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1 font-monospace">{{ $count }}</span>
                                            </td>
                                            <td class="text-end text-muted small">
                                                {{ $totalProfessionals > 0 ? round(($count / $totalProfessionals) * 100, 1) : 0 }}%
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-3 text-muted">لا توجد بيانات لجهات العمل.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- [4] قسم الرسوم البيانية المرئية (Charts Section) -->
        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark m-0">
                            <i class="bi bi-pie-chart text-primary me-2"></i>التوزيع النسبي للدرجات العلمية
                        </h6>
                    </div>
                    <div class="card-body p-3 d-flex justify-content-center align-items-center" style="height: 300px;">
                        <canvas id="degreesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark m-0">
                            <i class="bi bi-bar-chart-steps text-info me-2"></i>توزيع الكادر الطبي حسب الوظائف
                        </h6>
                    </div>
                    <div class="card-body p-3 d-flex justify-content-center align-items-center" style="height: 300px;">
                        <canvas id="professionsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- أكواد تشغيل وتحديث الرسوم البيانية تفاعلياً -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let degreesChartInstance = null;
                let professionsChartInstance = null;

                function initCharts(degreesLabels, degreesData, profLabels, profData) {
                    const ctxDegrees = document.getElementById('degreesChart');
                    if (ctxDegrees) {
                        if (degreesChartInstance) {
                            degreesChartInstance.destroy();
                        }
                        degreesChartInstance = new Chart(ctxDegrees.getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: degreesLabels,
                                datasets: [{
                                    data: degreesData,
                                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3546', '#0dcaf0']
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { position: 'bottom' } }
                            }
                        });
                    }

                    const ctxProf = document.getElementById('professionsChart');
                    if (ctxProf) {
                        if (professionsChartInstance) {
                            professionsChartInstance.destroy();
                        }
                        professionsChartInstance = new Chart(ctxProf.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: profLabels,
                                datasets: [{
                                    label: 'عدد الكوادر',
                                    data: profData,
                                    backgroundColor: '#0d6efd',
                                    borderRadius: 5
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: { y: { beginAtZero: true } },
                                plugins: { legend: { display: false } }
                            }
                        });
                    }
                }

                initCharts(
                    @json($chartDegreesLabels),
                    @json($chartDegreesData),
                    @json($chartProfLabels),
                    @json($chartProfData)
                );

                Livewire.on('chartDataUpdated', (event) => {
                    const data = Array.isArray(event) ? event[0] : event;
                    initCharts(
                        data.degreesLabels,
                        data.degreesData,
                        data.profLabels,
                        data.profData
                    );
                });
            });
        </script>
    </div>

    <!-- الفوتر الخاص بالطباعة فقط -->
    <div class="print-footer" style="display: none;">
        <div class="d-flex justify-content-around text-muted" style="font-size: 9px;">
            <span><i class="bi bi-geo-alt me-1"></i>العنوان: {{ $officialHeader->detailed_address ?? 'ميدان الثقافة - بجوار مركز رعاية طفل شرق' }}</span>
            <span><i class="bi bi-envelope me-1"></i>البريد: {{ $officialHeader->official_email ?? 'mahmoudqotb06@gmail.com' }}</span>
            <span><i class="bi bi-telephone me-1"></i>الهاتف: {{ $officialHeader->phone_fax ?? '01020682595' }}</span>
        </div>
    </div>

</div>
