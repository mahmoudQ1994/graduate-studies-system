<div>
    <style>
        .interactive-card {
            transition: all 0.3s ease-in-out;
            text-decoration: none !important;
        }
        .interactive-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>

    <!-- عنوان الترحيب والإجراءات السريعة -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-body mb-1 fs-4">أهلاً بك، {{ auth()->user()->name }} 👋</h3>
            <p class="text-body-secondary small mb-0">لوحة المؤشرات العامة لمتابعة طلبات الدراسات العليا والتدريب.</p>
        </div>
    </div>

    <!-- قسم أزرار الوصول السريع لأبرز صفحات النظام -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-3 rounded-4">
                <h6 class="fw-bold text-body mb-3">
                    <i class="bi bi-lightning-charge-fill text-warning me-2"></i> الوصول السريع لأبرز صفحات النظام
                </h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('postgraduate.register') }}" class="btn btn-outline-primary btn-sm px-3 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2">
                        <i class="bi bi-person-plus-fill"></i> تسجيل مرشح جديد
                    </a>
                    <a href="{{ route('postgraduate.search') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2">
                        <i class="bi bi-search"></i> البحث عن مرشح
                    </a>
                    <a href="{{ route('postgraduate.leaves') }}" class="btn btn-outline-success btn-sm px-3 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-check-fill"></i> إجازات التفرغ
                    </a>
                    <a href="{{ route('postgraduate.training-secondment') }}" class="btn btn-outline-info btn-sm px-3 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2">
                        <i class="bi bi-award-fill"></i> إفاد للتدريب
                    </a>
                    <a href="{{ route('postgraduate.study-pauses') }}" class="btn btn-outline-danger btn-sm px-3 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2">
                        <i class="bi bi-pause-circle-fill"></i> إيقافات القيد
                    </a>
                    <a href="{{ route('specialties') }}" class="btn btn-outline-primary btn-sm px-3 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2">
                        <i class="bi bi-journal-medical"></i> إدارة التخصصات
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- شبكة الكروت الإحصائية التفاعلية -->
    <div class="row g-3 mb-4">

        <!-- كارت إجمالي المرشحين -->
        <div class="col-12 col-sm-6 col-lg-3">
            <a href="{{ route('postgraduate.candidates-list') }}" class="card border-0 shadow-sm p-3 rounded-4 h-100 interactive-card d-block">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-body-secondary small fw-semibold d-block mb-1">إجمالي المرشحين</span>
                        <h3 class="fw-bold text-body mb-0">{{ $stats['total_candidates'] }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- كارت الإفادات النشطة -->
        <div class="col-12 col-sm-6 col-lg-3">
            <a href="{{ route('postgraduate.training-secondment-index') }}" class="card border-0 shadow-sm p-3 rounded-4 h-100 interactive-card d-block">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-body-secondary small fw-semibold d-block mb-1">الإفادات النشطة حالياً</span>
                        <h3 class="fw-bold text-success mb-0">{{ $stats['active_trainings'] }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-award-fill fs-4"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- كارت الحاصلون على الدرجة -->
        <div class="col-12 col-sm-6 col-lg-3">
            <a href="{{ route('postgraduate.candidates-list') }}" class="card border-0 shadow-sm p-3 rounded-4 h-100 interactive-card d-block">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-body-secondary small fw-semibold d-block mb-1">الحاصلون على الدرجة</span>
                        <h3 class="fw-bold text-info mb-0">{{ $stats['completed_degree'] }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-mortarboard-fill fs-4"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- كارت إيقافات القيد -->
        <div class="col-12 col-sm-6 col-lg-3">
            <a href="{{ route('postgraduate.study-pauses') }}" class="card border-0 shadow-sm p-3 rounded-4 h-100 interactive-card d-block">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-body-secondary small fw-semibold d-block mb-1">إجمالي إيقافات القيد</span>
                        <h3 class="fw-bold text-danger mb-0">{{ $stats['paused_registrations'] }}</h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-pause-circle-fill fs-4"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- كارت توزيع الحالات الدراسية (5 حالات) -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm p-3 rounded-4 h-100">
                <h6 class="fw-bold text-body mb-3"><i class="bi bi-pie-chart-fill text-primary me-2"></i> توزيع الحالات الدراسية للمرشحين</h6>
                <div class="row g-2 text-center">
                    <div class="col">
                        <a href="{{ route('postgraduate.candidates-list') }}" class="p-2 bg-body-tertiary rounded-3 d-block text-decoration-none interactive-card">
                            <span class="text-body-secondary small d-block mb-1" style="font-size: 10px;">جاري الفحص</span>
                            <h6 class="fw-bold text-body-secondary mb-0">{{ $stats['status_pending'] }}</h6>
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ route('postgraduate.candidates-list') }}" class="p-2 bg-body-tertiary rounded-3 d-block text-decoration-none interactive-card">
                            <span class="text-body-secondary small d-block mb-1" style="font-size: 10px;">مستمر بالدراسة</span>
                            <h6 class="fw-bold text-primary mb-0">{{ $stats['status_ongoing'] }}</h6>
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ route('postgraduate.candidates-list') }}" class="p-2 bg-body-tertiary rounded-3 d-block text-decoration-none interactive-card">
                            <span class="text-body-secondary small d-block mb-1" style="font-size: 10px;">حصل على الدرجة</span>
                            <h6 class="fw-bold text-success mb-0">{{ $stats['status_graduated'] }}</h6>
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ route('postgraduate.candidates-list') }}" class="p-2 bg-body-tertiary rounded-3 d-block text-decoration-none interactive-card">
                            <span class="text-body-secondary small d-block mb-1" style="font-size: 10px;">اعتذار</span>
                            <h6 class="fw-bold text-warning mb-0">{{ $stats['status_apology'] }}</h6>
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ route('postgraduate.candidates-list') }}" class="p-2 bg-body-tertiary rounded-3 d-block text-decoration-none interactive-card">
                            <span class="text-body-secondary small d-block mb-1" style="font-size: 10px;">عدم القبول</span>
                            <h6 class="fw-bold text-danger mb-0">{{ $stats['status_rejected'] }}</h6>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- كارت التنبيهات -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm p-3 rounded-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-body m-0 fs-6"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> التنبيهات</h6>
                </div>
                <a href="{{ route('postgraduate.leaves') }}" class="p-2.5 bg-body-tertiary rounded-3 d-flex justify-content-between align-items-center px-3 mt-1 text-decoration-none interactive-card">
                    <span class="text-body-secondary small fw-semibold" style="font-size: 11px;">إجازات تنتهي قريباً (30 يوم)</span>
                    <h5 class="fw-bold text-warning mb-0">{{ $stats['expiring_leaves_count'] }}</h5>
                </a>
            </div>
        </div>
    </div>

    <!-- جدول أحدث طلبات الترشيح -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-body py-3 px-4 d-flex justify-content-between align-items-center border-0">
            <h6 class="fw-bold m-0 text-body d-flex align-items-center">
                <i class="bi bi-clock-history text-primary me-2"></i> أحدث طلبات الترشيح المسجلة
            </h6>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small text-center">
                <thead class="table-light text-body-secondary">
                    <tr>
                        <th class="py-3 px-4 text-start">اسم المرشح</th>
                        <th class="py-3">الرقم القومي</th>
                        <th class="py-3">الوظيفة</th>
                        <th class="py-3">نوع الدراسة المطلوبة</th>
                        <th class="py-3">التخصص المطلوب</th>
                        <th class="py-3">حالة الدراسة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestCandidates as $candidate)
                        <tr>
                            <td class="text-start px-4 fw-bold text-body">
                                {{ $candidate->healthProfessional->name ?? '---' }}
                            </td>
                            <td>
                                <span class="font-monospace text-body-secondary">{{ $candidate->healthProfessional->national_id ?? '---' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info border-0 px-2 py-1">
                                    {{ $candidate->healthProfessional->profession ?? 'طبيب بشري' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-2 py-1">
                                    {{ $candidate->required_degree ?? '---' }}
                                </span>
                            </td>
                            <td class="text-body-secondary fw-semibold">
                                {{ $candidate->required_specialty ?? '---' }}
                            </td>
                            <td>
                                @php
                                    $status = $candidate->study_status ?? 'جاري فحص الطلب';
                                    $badgeStyle = match($status) {
                                        'حصل على الدرجة' => 'background-color: rgba(25, 135, 84, 0.2); color: #198754;',
                                        'مستمر بالدراسة' => 'background-color: rgba(13, 110, 253, 0.2); color: #0d6efd;',
                                        'اعتذار عن الدراسة' => 'background-color: rgba(255, 193, 7, 0.2); color: #ffc107;',
                                        'عدم القبول بالدراسة' => 'background-color: rgba(220, 53, 69, 0.2); color: #dc3545;',
                                        default => 'background-color: rgba(108, 117, 125, 0.2); color: #6c757d;'
                                    };
                                @endphp
                                <span class="badge px-3 py-2 fw-bold rounded-pill" style="{{ $badgeStyle }}">
                                    {{ $status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-body-secondary">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                لا توجد طلبات ترشيح مسجلة حديثاً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
