<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'نظام إدارة الدراسات العليا') }}</title>

    <!-- 1. خط Cairo المميز من Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- 2. Bootstrap 5 RTL CSS + Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
        }

        /* تنسيق الـ Sidebar بأسلوب Bootstrap احترافي */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background-color: #1e293b; /* لون كحلي داكن مريح للعين */
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
            background-color: #0d6efd; /* لون أزرق للزر النشط */
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
    </style>

    @livewireStyles
</head>
<body>

    <div class="d-flex">
        <!-- 1. السايد بار (Bootstrap Sidebar) -->
        @include('layouts.sidebar')

        <!-- 2. الهيدر والمحتوى الرئيسي -->
        <div class="main-wrapper">
            @include('layouts.header')

            <main class="p-4">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle (مرة واحدة فقط) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>
</html>
