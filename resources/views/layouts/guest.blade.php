<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'تسجيل الدخول - نظام إدارة الدراسات العليا') }}</title>

    <!-- خط Cairo المميز -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 RTL CSS + Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #0f172a;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* حركات متدرجة للدوائر البصرية بالحلفية */
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        @keyframes pulseGlow {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.08); }
        }

        .animate-float {
            animation: floatSlow 6s ease-in-out infinite;
        }

        .animate-pulse-glow {
            animation: pulseGlow 4s ease-in-out infinite;
        }

        .glass-card {
            background: rgba(236, 182, 182, 0.07);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(112, 13, 13, 0.15);
        }
    </style>

    @livewireStyles
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 p-0 m-0">

    <div class="container-fluid p-0 min-vh-100 d-flex flex-column justify-content-center">
        {{ $slot }}
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>
</html>
