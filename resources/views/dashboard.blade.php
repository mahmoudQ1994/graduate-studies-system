<x-app-layout>
    <!-- عنوان الترحيب -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">أهلاً بك، {{ auth()->user()->name }} 👋</h3>
            <p class="text-muted small">إليك نظرة عامة على طلبات الترشيح والدراسات العليا اليوم.</p>
        </div>
        <button class="btn btn-primary fw-bold shadow-sm rounded-3">
            <i class="bi bi-plus-lg me-1"></i> إضافة طلب جديد
        </button>
    </div>

    <!-- شبكة الكروت الإحصائية بـ Bootstrap Grid -->
    <div class="row g-3 mb-4">
        <!-- كارت 1 -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold">إجمالي المتقدمين</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">1,248</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                        <i class="bi bi-folder-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- كارت 2 -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold">قيد المراجعة</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">84</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- كارت 3 -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold">الطلبات المقبولة</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">952</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-3">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- كارت 4 -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold">الأقسام الفعالة</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">18</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-3">
                        <i class="bi bi-hospital-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- كارت جدول البيانات -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold m-0 text-dark">أحدث طلبات الترشيح</h6>
            <a href="#" class="btn btn-sm btn-link text-decoration-none fw-bold">عرض الكل ←</a>
        </div>

    </div>
</x-app-layout>
