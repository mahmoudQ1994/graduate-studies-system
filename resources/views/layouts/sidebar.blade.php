<!-- السايدبار الأساسي للشاشات الكبيرة -->
<div class="sidebar d-none d-lg-block p-3 flex-shrink-0 text-white shadow" id="sidebarMenu" style="width: 280px; min-height: 100vh; background-color: #1e293b;">

    <!-- شعار الموقع والعنوان الديناميكي -->
    <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom border-secondary border-opacity-50">
        <div class="flex-shrink-0">
            @if(isset($siteSetting) && $siteSetting->logo_path)
                <div class="rounded-circle overflow-hidden border border-2 border-primary shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <img src="{{ url('storage/' . $siteSetting->logo_path) }}" alt="الشعار" style="width: 80%; height: 80%; object-fit: cover; transform: scale(1.2);">
                </div>
            @else
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; font-size: 1.2rem;">
                    <i class="bi bi-building"></i>
                </div>
            @endif
        </div>

        <div class="overflow-hidden">
            <h6 class="fw-bold text-white mb-0 text-truncate" style="font-size: 0.88rem;">
                {{ $siteSetting->directorate_name ?? 'المديرية العامة للشؤون الصحية' }}
            </h6>
            <p class="text-white-50 small mb-0 text-truncate" style="font-size: 0.75rem;">
                {{ $siteSetting->administration_name ?? 'إدارة التعليم الطبي والتدريب' }}
            </p>
        </div>
    </div>

    <!-- قائمة روابط الملاحة -->
    <ul class="nav nav-pills flex-column mb-auto gap-1">

        <!-- 1. الرئيسية -->
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link text-white d-flex align-items-center gap-2 {{ request()->routeIs('dashboard') ? 'active bg-primary' : 'opacity-75 hvr-bg' }}">
                <i class="bi bi-speedometer2 fs-5"></i>
                <span>الرئيسية (الداشبورد)</span>
            </a>
        </li>

        <!-- 2. الدراسات العليا -->
        <li class="nav-item">
            <a class="nav-link collapsed d-flex align-items-center justify-content-between" data-bs-toggle="collapse" href="#postgraduateMenu" role="button" aria-expanded="false">
                <span>
                    <i class="bi bi-mortarboard-fill text-primary me-2"></i>
                    <span>إدارة الدراسات العليا</span>
                </span>
                <i class="bi bi-chevron-down small"></i>
            </a>

            <div class="collapse" id="postgraduateMenu">
                <ul class="nav flex-column ms-3 mt-1 border-start ps-2 small">
                    <!-- 1. استعلام عن موقف مرشح -->
                    <li class="nav-item">
                        <a class="nav-link py-1 text-secondary"
                        href="{{ route('postgraduate.search') }}" wire:navigate>
                            <i class="bi bi-search me-2"></i>استعلام عن موقف مرشح
                        </a>
                    </li>

                    <!-- 2. تسجيل بيانات مرشح -->
                    <li class="nav-item">
                        <a class="nav-link py-1 text-secondary"
                         href="{{ route('postgraduate.register') }}" wire:navigate>
                            <i class="bi bi-person-plus-fill me-2">
                                </i>تسجيل بيانات مرشح جديد
                        </a>
                    </li>

                    <!-- 3. تسجيل موقف المرشح -->
                    <li class="nav-item">
                        <a class="nav-link py-1 text-secondary"
                         href="" wire:navigate>
                            <i class="bi bi-pencil-square me-2"></i>متابعة وتحديث موقف مرشح
                        </a>
                    </li>

                    <!-- 4. إدارة أجازات التفرغ الدراسي -->
                    <li class="nav-item">
                        <a class="nav-link py-1 text-secondary"
                        href="{{ route('postgraduate.leaves') }}" wire:navigate>
                            <i class="bi bi-calendar2-week me-2"></i>إدارة أجازات التفرغ الدراسي
                        </a>
                    </li>
                    <!-- 4. التقارير -->
                    <li class="nav-item">
                        <a class="nav-link py-1 text-secondary"
                        href="" wire:navigate>
                            <i class="bi bi-file-earmark-bar-graph-fill me-2">
                                </i>التقارير والإحصائيات
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- 3. استكمال الدراسة -->
        <li class="nav-item">
            <a href="#" class="nav-link text-white opacity-75 d-flex align-items-center gap-2">
                <i class="bi bi-journal-bookmark-fill fs-5"></i>
                <span>استكمال الدراسة</span>
            </a>
        </li>

        <!-- 4. التكليف والامتياز -->
        <li class="nav-item">
            <a href="#" class="nav-link text-white opacity-75 d-flex align-items-center gap-2">
                <i class="bi bi-hospital-fill fs-5"></i>
                <span>التكليف والامتياز</span>
            </a>
        </li>

        <!-- 5. المستخدمين والصلاحيات -->
        <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link text-white opacity-75 d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock-fill fs-5"></i>
                <span>المستخدمين والصلاحيات</span>
            </a>
        </li>

        <!-- 6. الإعدادات العامة للموقع (قائمة منسدلة) -->
        <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center justify-content-between {{ request()->routeIs('settings*') || request()->routeIs('departments*') ? 'active bg-primary' : 'opacity-75' }}"
               data-bs-toggle="collapse"
               href="#settingsCollapse"
               role="button"
               aria-expanded="{{ request()->routeIs('settings*') || request()->routeIs('departments*') ? 'true' : 'false' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-gear-fill fs-5"></i>
                    <span>الإعدادات العامة</span>
                </div>
                <i class="bi bi-chevron-down small"></i>
            </a>

            <!-- القائمة الفرعية -->
            <div class="collapse {{ request()->routeIs('settings*') || request()->routeIs('departments*') ? 'show' : '' }} ms-3 mt-1" id="settingsCollapse" data-bs-parent="#sidebarMenu">
                <ul class="nav nav-pills flex-column gap-1 border-start border-secondary ps-2">
                    <li>
                        <a href="{{ route('site-settings') }}" class="nav-link py-1 px-2 small {{ request()->routeIs('site-settings') ? 'active bg-primary text-white' : 'text-white opacity-75' }}">
                            <i class="bi bi-sliders me-1"></i> البيانات العامة للموقع
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('departments') }}" class="nav-link py-1 px-2 small {{ request()->routeIs('departments') ? 'active bg-primary text-white' : 'text-white opacity-75' }}">
                            <i class="bi bi-diagram-3 me-1"></i> أقسام الإدارة
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- 7. تكويد الهيكل والإدارات (قائمة منسدلة) -->
        <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center
            justify-content-between {{ request()->routeIs('districts*') || request()->routeIs('sectors*') || request()->routeIs('facilities*') ? 'active bg-primary' : 'opacity-75' }}"
               data-bs-toggle="collapse"
               href="#codingSubmenu"
               role="button"
               aria-expanded="{{ request()->routeIs('districts*') || request()->routeIs('sectors*') || request()->routeIs('facilities*') ? 'true' : 'false' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-gear-wide-connected fs-5"></i>
                    <span>تكويد المراكز والإدارات</span>
                </div>
                <i class="bi bi-chevron-down small"></i>
            </a>

            <div class="collapse {{ request()->routeIs('districts*') || request()->routeIs('sectors*') || request()->routeIs('facilities*') ? 'show' : '' }} ms-3 mt-1" id="codingSubmenu" data-bs-parent="#sidebarMenu">
                <ul class="nav nav-pills flex-column gap-1 border-start border-secondary ps-2">
                    <!-- 1. تكويد المراكز -->
                    <li>
                        <a href="{{ route('districts.index') }}" class="nav-link py-1 px-2 small {{ request()->routeIs('districts*') ? 'active bg-primary text-white' : 'text-white opacity-75' }}">
                            <i class="bi bi-geo-alt me-1"></i> المراكز والمدن
                        </a>
                    </li>

                    <!-- 2. تكويد قطاعات التبعية -->
                    <li>
                        <a href="{{ route('sectors.index') }}" class="nav-link py-1 px-2 small {{ request()->routeIs('sectors*') ? 'active bg-primary text-white' : 'text-white opacity-75' }}">
                            <i class="bi bi-diagram-2 me-1"></i> قطاعات التبعية
                        </a>
                    </li>

                    <!-- 3. تكويد المستشفيات والجهات -->
                    <li>
                        <a href="{{ route('hospitals.index') }}" class="nav-link py-1 px-2 small {{ request()->routeIs('hospitals*') ? 'active bg-primary text-white' : 'text-white opacity-75' }}">
                            <i class="bi bi-hospital me-1"></i> المستشفيات والجهات الصحية
                        </a>
                    </li>
                </ul>
            </div>
        </li>

    </ul>

    <hr class="border-secondary my-3 border-opacity-50">

    <!-- معلومات الموظف المسجل أسفل السايدبار -->
    <div class="d-flex align-items-center gap-2 p-2 rounded bg-dark bg-opacity-50">
        <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px; font-size: 0.9rem;">
            {{ mb_substr(auth()->user()->name, 0, 1) }}
        </div>
        <div class="text-truncate" style="max-width: 160px;">
            <div class="fw-bold small text-white text-truncate">{{ auth()->user()->name }}</div>
            <div class="text-white-50 text-truncate" style="font-size: 11px;">{{ auth()->user()->email }}</div>
        </div>
    </div>

</div>
