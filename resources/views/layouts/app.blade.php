<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'نظام إدارة الدراسات العليا') }}</title>

    <!-- خط Cairo المميز من Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 RTL CSS + Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
        }

        /* تنسيق الـ Sidebar بأسلوب Bootstrap احترافي */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background-color: #1e293b;
            transition: all 0.3s;
        }

        .sidebar .nav-link {
            color: #94a3b8;
            font-weight: 600;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.08);
        }

        .sidebar .nav-link.active {
            color: #ffffff;
            background-color: #0d6efd;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 100vh;
            position: relative;
            padding-bottom: 50px; /* مسافة لكي لا يغطي الفوتر الثابت المحتوى */
        }

        /* تخصيص خلفية الفوتر لتطابق لون السايد بار */
        .custom-footer {
            background-color: #1e293b !important;
            color: #cbd5e1 !important;
            border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
    </style>

    @livewireStyles
</head>
<body class="bg-body text-body">

    <div class="d-flex">
        <!-- السايد بار (Bootstrap Sidebar) -->
        @include('layouts.sidebar')

        <!-- الهيدر والمحتوى الرئيسي -->
        <div class="main-wrapper">
            @include('layouts.header')

            <main class="p-4 flex-grow-1">
                {{ $slot }}
            </main>

            <!-- الفوتر الثابت في أسفل الشاشة بلون السايد بار -->
            <footer class="position-fixed bottom-0 start-0 w-100 py-2 text-center shadow-sm custom-footer" style="z-index: 1030;">
                <div class="container-fluid px-3">
                    <p class="mb-0 small text-truncate">
                        جميع الحقوق محفوظة &copy; {{ date('Y') }} — صُمم وطور النظام بواسطة المهندس/ محمود قطب (<span dir="ltr">01020682595</span>)
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @livewireScripts

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const htmlElement = document.documentElement;

        // تطبيق الوضع المحفوظ مسبقاً في المتصفح فور فتح الصفحة
        const savedTheme = localStorage.getItem('theme') || 'light';
        htmlElement.setAttribute('data-bs-theme', savedTheme);
        updateIcon(savedTheme);

        // استخدام Event Delegation للتعامل مع زر التبديل حتى لو تم إعادة تحميل أجزاء عبر Livewire
        document.addEventListener('click', function (event) {
            if (event.target.closest('#darkModeToggle')) {
                let currentTheme = htmlElement.getAttribute('data-bs-theme');
                let newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                htmlElement.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateIcon(newTheme);
            }
        });

        function updateIcon(theme) {
            const themeIcon = document.getElementById('themeIcon');
            if (!themeIcon) return;

            if (theme === 'dark') {
                themeIcon.classList.remove('bi-moon-stars-fill');
                themeIcon.classList.add('bi-sun-fill');
                themeIcon.style.color = '#ffc107'; // لون أصفر للشمس
            } else {
                themeIcon.classList.remove('bi-sun-fill');
                themeIcon.classList.add('bi-moon-stars-fill');
                themeIcon.style.color = '#212529'; // لون داكن للقمر
            }
        }
    });
    </script>
</body>
</html>
