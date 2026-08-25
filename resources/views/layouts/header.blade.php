<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-3">
    <div class="container-fluid p-0">

        <!-- عنوان الصفحة -->
        <span class="navbar-brand fw-bold text-secondary mb-0 h1 fs-6">
            نظام إدارة التدريب الطبي والدراسات العليا
        </span>

        <!-- قائمة البروفايل والإشعارات -->
        <div class="d-flex align-items-center gap-3 ms-auto">

            <!-- زر الإشعارات -->
            <button class="btn btn-light position-relative rounded-circle p-2">
                <i class="bi bi-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
            </button>

            <!-- القائمة المنسدلة للمستخدم (Dropdown) -->
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border-0 bg-transparent" type="button" data-bs-toggle="dropdown">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px;">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <span class="fw-bold small d-none d-md-inline">{{ auth()->user()->name }}</span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li><a class="dropdown-menu-item dropdown-item" href="#"><i class="bi bi-person me-2"></i> الملف الشخصي</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger
                            fw-bold border-0 bg-transparent w-100 text-start px-3 py-2">
                                <i class="bi bi-box-arrow-right me-2"></i> تسجيل الخروج
                            </button>
                        </form>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</nav>
