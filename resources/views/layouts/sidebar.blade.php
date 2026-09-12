<!-- السايدبار الأساسي للشاشات الكبيرة مع تثبيت الهيدر وتمرير الروابط -->
<div class="sidebar d-none d-lg-flex flex-column p-3 text-white shadow" id="sidebarMenu" style="width: 260px; height: 100vh; background-color: #1e293b; position: sticky; top: 0;">

    <!-- 1. الجزء الثابت بالأعلى (الهيدر: الشعار واسم المديرية) -->
    <div class="flex-shrink-0 d-flex align-items-center gap-2 pb-2 mb-2 border-bottom border-secondary border-opacity-50">
        <div class="flex-shrink-0">
            @if(isset($siteSetting) && $siteSetting->logo_path)
                <div class="rounded-circle overflow-hidden border border-2 border-primary shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <img src="{{ url('storage/' . $siteSetting->logo_path) }}" alt="الشعار" style="width: 80%; height: 80%; object-fit: cover; transform: scale(1.2);">
                </div>
            @else
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; font-size: 1rem;">
                    <i class="bi bi-building"></i>
                </div>
            @endif
        </div>

        <div class="overflow-hidden">
            <h6 class="fw-bold text-white mb-0 text-truncate" style="font-size: 0.78rem;">
                {{ $siteSetting->directorate_name ?? 'المديرية العامة للشؤون الصحية' }}
            </h6>
            <p class="text-white-50 small mb-0 text-truncate" style="font-size: 0.68rem;">
                {{ $siteSetting->administration_name ?? 'إدارة التعليم الطبي والتدريب' }}
            </p>
        </div>
    </div>

    <!-- 2. الجزء الأوسط (قنوات الملاحة قابلة للتمرير Scrollable) -->
    <div class="flex-grow-1 overflow-y-auto overflow-x-hidden pe-1 custom-scrollbar" style="font-size: 0.85rem;">
        <ul class="nav nav-pills flex-column gap-1">

            <!-- 1. الرئيسية -->
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link py-1.5 px-2 text-white d-flex align-items-center gap-2 {{ request()->routeIs('dashboard') ? 'active bg-primary' : 'opacity-75 hvr-bg' }}">
                    <i class="bi bi-speedometer2 fs-6"></i>
                    <span>الرئيسية (الداشبورد)</span>
                </a>
            </li>

            <!-- 2. الدراسات العليا -->
            <li class="nav-item">
                <a class="nav-link py-1.5 px-2 text-white d-flex align-items-center justify-content-between {{ request()->routeIs('postgraduate.*') ? 'active bg-primary' : 'opacity-75' }}"
                data-bs-toggle="collapse"
                href="#postgraduateMenu"
                role="button"
                aria-expanded="{{ request()->routeIs('postgraduate.*') ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-mortarboard-fill fs-6"></i>
                        <span>إدارة الدراسات العليا</span>
                    </div>
                    <i class="bi bi-chevron-down extra-small"></i>
                </a>

                <div class="collapse {{ request()->routeIs('postgraduate.*') ? 'show' : '' }} ms-2 mt-1" id="postgraduateMenu" data-bs-parent="#sidebarMenu">
                    <ul class="nav nav-pills flex-column gap-1 border-start border-secondary ps-2">
                        <li class="nav-item">
                            <a href="{{ route('postgraduate.search') }}" class="nav-link py-1 px-2 small {{ request()->routeIs('postgraduate.search') ? 'active bg-primary text-white' : 'text-white opacity-75' }}" wire:navigate>
                                <i class="bi bi-search me-1"></i> استعلام عن موقف مرشح
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('postgraduate.register') }}" class="nav-link py-1 px-2 small {{ request()->routeIs('postgraduate.register') ? 'active bg-primary text-white' : 'text-white opacity-75' }}" wire:navigate>
                                <i class="bi bi-person-plus-fill me-1"></i> تسجيل بيانات مرشح جديد
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('postgraduate.candidates-list') }}" class="nav-link py-1 px-2 small {{ request()->routeIs('postgraduate.candidates-list') ? 'active bg-primary text-white' : 'text-white opacity-75' }}" wire:navigate>
                                <i class="bi bi-pencil-square me-1"></i> قائمة المرشحين
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('postgraduate.study-status') }}" class="nav-link py-1 px-2 small {{ request()->routeIs('postgraduate.study-status') ? 'active bg-primary text-white' : 'text-white opacity-75' }}" wire:navigate>
                                <i class="bi bi-journal-check me-1"></i> موقف المرشح (تنفيذ/تعديل)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('postgraduate.leaves') }}" class="nav-link py-1 px-2 small {{ request()->routeIs('postgraduate.leaves') ? 'active bg-primary text-white' : 'text-white opacity-75' }}" wire:navigate>
                                <i class="bi bi-calendar2-week me-1"></i> أجازات التفرغ الدراسي
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('postgraduate.study-pauses') }}" class="nav-link py-1 px-2 small {{ request()->routeIs('postgraduate.study-pauses') ? 'active bg-primary text-white' : 'text-white opacity-75' }}" wire:navigate>
                                <i class="bi bi-pause-circle-fill me-1"></i> إدارة إيقاف القيد
                            </a>
                        </li>

                        <!-- النماذج والطباعة -->
                        <li class="nav-item">
                            <a class="nav-link py-1 px-2 small text-white opacity-75 d-flex align-items-center justify-content-between"
                            data-bs-toggle="collapse"
                            href="#printSubMenu"
                            role="button"
                            aria-expanded="false">
                                <span><i class="bi bi-printer me-1"></i> النماذج والطباعة</span>
                                <i class="bi bi-chevron-down extra-small"></i>
                            </a>
                            <div class="collapse ms-2 mt-1" id="printSubMenu">
                                <ul class="nav nav-pills flex-column gap-1 border-start border-secondary ps-2">
                                    <li>
                                        <a href="{{ route('postgraduate.official-letter-print') }}" class="nav-link py-1 px-2 small text-white opacity-75" wire:navigate>
                                            <i class="bi bi-file-earmark-text me-1"></i> كشف الترشيح للدراسة
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <!-- التقارير والتحليلات -->
                        <li class="nav-item">
                            <a class="nav-link py-1 px-2 small text-white opacity-75 d-flex align-items-center justify-content-between"
                            data-bs-toggle="collapse"
                            href="#reportsSubMenu"
                            role="button"
                            aria-expanded="false">
                                <span><i class="bi bi-graph-up-arrow me-1"></i> التقارير والتحليلات</span>
                                <i class="bi bi-chevron-down extra-small"></i>
                            </a>
                            <div class="collapse ms-2 mt-1" id="reportsSubMenu">
                                <ul class="nav nav-pills flex-column gap-1 border-start border-secondary ps-2">
                                    <li>
                                        <a href="{{ route('postgraduate.general-dashboard') }}" class="nav-link py-1 px-2 small text-white opacity-75" wire:navigate>
                                            <i class="bi bi-bar-chart-fill me-1"></i> اللوحة العامة والإحصائيات
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('postgraduate.leave-movement-report') }}" class="nav-link py-1 px-2 small text-white opacity-75" wire:navigate>
                                            <i class="bi bi-calendar-range me-1"></i>   تقرير اجازات التفرغ
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('postgraduate.medical-degrees-archive-report') }}" class="nav-link py-1 px-2 small text-white opacity-75" wire:navigate>
                                            <i class="bi bi-award-fill me-1"></i>   تقرير ارشيف الحاصلين على الدرجات العلمية
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('postgraduate.study-pauses-report') }}" class="nav-link py-1 px-2 small text-white opacity-75" wire:navigate>
                                            <i class="bi bi-pause-circle me-1"></i>   تقرير متابعة حالات إيقاف القيد
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('postgraduate.semi-annual-candidates-report') }}" class="nav-link py-1 px-2 small text-white opacity-75" wire:navigate>
                                            <i class="bi bi-file-earmark-bar-graph me-1"></i> الإحصائية النصف سنوية للمرشحين
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </li>

                        <li class="nav-item mt-1">
                            <a href="{{ route('postgraduate.import-export') }}" class="nav-link py-1 px-2 small text-white opacity-75" wire:navigate>
                                <i class="bi bi-cloud-upload-fill me-1"></i> رفع بيانات مسجلة (Excel)
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- 3. استكمال الدراسة -->
            <li class="nav-item">
                <a href="#" class="nav-link py-1.5 px-2 text-white opacity-75 d-flex align-items-center gap-2">
                    <i class="bi bi-journal-bookmark-fill fs-6"></i>
                    <span>استكمال الدراسة</span>
                </a>
            </li>

            <!-- 4. التكليف والامتياز -->
            <li class="nav-item">
                <a href="#" class="nav-link py-1.5 px-2 text-white opacity-75 d-flex align-items-center gap-2">
                    <i class="bi bi-hospital-fill fs-6"></i>
                    <span>التكليف والامتياز</span>
                </a>
            </li>

            <!-- 5. المستخدمين والصلاحيات -->
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link py-1.5 px-2 text-white opacity-75 d-flex align-items-center gap-2">
                    <i class="bi bi-shield-lock-fill fs-6"></i>
                    <span>المستخدمين والصلاحيات</span>
                </a>
            </li>

            <!-- 6. الإعدادات العامة -->
            <li class="nav-item">
                <a class="nav-link py-1.5 px-2 text-white d-flex align-items-center justify-content-between {{ request()->routeIs('settings*') || request()->routeIs('departments*') ? 'active bg-primary' : 'opacity-75' }}"
                   data-bs-toggle="collapse"
                   href="#settingsCollapse"
                   role="button"
                   aria-expanded="false">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-gear-fill fs-6"></i>
                        <span>الإعدادات العامة</span>
                    </div>
                    <i class="bi bi-chevron-down extra-small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('settings*') || request()->routeIs('departments*') ? 'show' : '' }} ms-2 mt-1" id="settingsCollapse" data-bs-parent="#sidebarMenu">
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

            <!-- 7. تكويد الهيكل والإدارات -->
            <li class="nav-item">
                <a class="nav-link py-1.5 px-2 text-white d-flex align-items-center justify-content-between {{ request()->routeIs('districts*') || request()->routeIs('sectors*') || request()->routeIs('facilities*') ? 'active bg-primary' : 'opacity-75' }}"
                   data-bs-toggle="collapse"
                   href="#codingSubmenu"
                   role="button"
                   aria-expanded="false">
                    <div class="v-flex d-flex align-items-center gap-2">
                        <i class="bi bi-gear-wide-connected fs-6"></i>
                        <span>تكويد المراكز والإدارات</span>
                    </div>
                    <i class="bi bi-chevron-down extra-small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('districts*') || request()->routeIs('sectors*') || request()->routeIs('facilities*') ? 'show' : '' }} ms-2 mt-1" id="codingSubmenu" data-bs-parent="#sidebarMenu">
                    <ul class="nav nav-pills flex-column gap-1 border-start border-secondary ps-2">
                        <li>
                            <a href="{{ route('districts.index') }}" class="nav-link py-1 px-2 small {{ request()->routeIs('districts*') ? 'active bg-primary text-white' : 'text-white opacity-75' }}">
                                <i class="bi bi-geo-alt me-1"></i> المراكز والمدن
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('sectors.index') }}" class="nav-link py-1 px-2 small {{ request()->routeIs('sectors*') ? 'active bg-primary text-white' : 'text-white opacity-75' }}">
                                <i class="bi bi-diagram-2 me-1"></i> قطاعات التبعية
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('hospitals.index') }}" class="nav-link py-1 px-2 small {{ request()->routeIs('hospitals*') ? 'active bg-primary text-white' : 'text-white opacity-75' }}">
                                <i class="bi bi-hospital me-1"></i> المستشفيات والجهات الصحية
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

        </ul>
    </div>


</div>
