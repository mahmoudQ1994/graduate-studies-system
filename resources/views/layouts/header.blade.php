<nav class="navbar navbar-expand-lg border-bottom shadow-sm px-3 bg-body">
    <div class="container-fluid p-0">
        <!-- عنوان الصفحة -->
        <span class="navbar-brand fw-bold text-body-secondary mb-0 h1 fs-6">
            نظام إدارة التدريب الطبي والدراسات العليا
        </span>

        <!-- قائمة البروفايل والإشعارات والوضع الليلي -->
        <div class="d-flex align-items-center gap-3 ms-auto">
            <!-- زر الإشعارات -->
            <button class="btn btn-outline-secondary border-0 position-relative rounded-circle p-2">
                <i class="bi bi-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
            </button>

            <!-- زر تبديل الوضع المظلم/المضيء -->
            <button type="button" class="btn btn-outline-secondary border-0 position-relative rounded-circle p-2 d-flex align-items-center justify-content-center" id="darkModeToggle" title="تغيير الوضع المظلم/المضيء" style="width: 40px; height: 40px;">
                <i class="bi bi-moon-stars-fill fs-5" id="themeIcon"></i>
            </button>

            <!-- القائمة المنسدلة للمستخدم -->
            <div class="dropdown">
                <button class="btn btn-link text-decoration-none text-body dropdown-toggle d-flex align-items-center gap-2 border-0 bg-transparent p-0" type="button" data-bs-toggle="dropdown">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px;">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <span class="fw-bold small d-none d-md-inline text-body">{{ auth()->user()->name }}</span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i> الملف الشخصي</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger fw-bold border-0 bg-transparent w-100 text-start px-3 py-2">
                                <i class="bi bi-box-arrow-right me-2"></i> تسجيل الخروج
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
