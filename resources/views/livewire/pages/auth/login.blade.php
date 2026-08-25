<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="row g-0 min-vh-100 align-items-center">

    <!-- النصف الأول (الأيمن): نموذج تسجيل الدخول -->
    <div class="col-12 col-lg-6 bg-white min-vh-100 d-flex align-items-center justify-content-center p-4 p-md-5">
        <div class="w-100" style="max-width: 440px;">

            <!-- الشعار الرئيسي -->
            <div class="text-center mb-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-4 d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 70px; height: 70px;">
                    <i class="bi bi-heart-pulse-fill fs-1"></i>
                </div>
                <h3 class="fw-bold text-dark">أهلاً بك مجدداً 👋</h3>
                <p class="text-muted small">قم بتسجيل الدخول للوصول لمنصة التعليم الطبي والدراسات العليا</p>
            </div>

            <!-- حالة الجلسة (أخطاء الدخول) -->
            <x-auth-session-status class="mb-4 text-danger small" :status="session('status')" />

            <form wire:submit="login">
                <!-- البريد الإلكتروني -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold text-secondary small">البريد الإلكتروني الوظيفي</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username"
                               class="form-control bg-light border-start-0 ps-0 py-2.5 @error('form.email') is-invalid @enderror"
                               placeholder="user@medical-edu.gov.eg">
                    </div>
                    @error('form.email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- كلمة المرور -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label fw-semibold text-secondary small mb-0">كلمة المرور</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-primary text-decoration-none small fw-bold" wire:navigate>
                                نسيت كلمة المرور؟
                            </a>
                        @endif
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input wire:model="form.password" id="password" type="password" name="password" required autocomplete="current-password"
                               class="form-control bg-light border-start-0 ps-0 py-2.5 @error('form.password') is-invalid @enderror"
                               placeholder="••••••••">
                    </div>
                    @error('form.password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- خيار تذكرني -->
                <div class="form-check mb-4">
                    <input wire:model="form.remember" id="remember_me" type="checkbox" class="form-check-input" name="remember">
                    <label for="remember_me" class="form-check-label text-secondary small">
                        تذكر تسجيل دخولي في هذا الجهاز
                    </label>
                </div>

                <!-- زر التسجيل -->
                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm fs-6">
                    <span wire:loading.remove>تسجيل الدخول <i class="bi bi-arrow-left ms-1"></i></span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        جاري التحقق من البيانات...
                    </span>
                </button>
            </form>

            <div class="mt-5 text-center text-muted small border-top pt-3">
                جميع الحقوق محفوظة © {{ date('Y') }} - إدارة التعليم الطبي والتدريب
            </div>
        </div>
    </div>

    <!-- النصف الثاني (الأيسر): الواجهة التفاعلية والمتحركة الهادفة للقطاع الطبي -->
    <div class="col-lg-6 d-none d-lg-flex min-vh-100 position-relative overflow-hidden align-items-center justify-content-center p-5"
         style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);">

        <!-- الأشكال والدوائر المتحركة في الخلفية -->
        <div class="position-absolute animate-pulse-glow" style="width: 350px; height: 350px; background: rgba(99, 102, 241, 0.25); filter: blur(90px); border-radius: 50%; top: 10%; left: 10%;"></div>
        <div class="position-absolute animate-pulse-glow" style="width: 300px; height: 300px; background: rgba(14, 165, 233, 0.2); filter: blur(80px); border-radius: 50%; bottom: 10%; right: 10%;"></div>

        <div class="position-relative z-1 text-white max-w-lg text-center text-lg-start">

            <!-- أيكونة متحركة تعبر عن الطب والدراسات العليا -->
            <div class="d-inline-flex p-3 rounded-4 glass-card mb-4 animate-float shadow-lg">
                <i class="bi bi-mortarboard fs-1 text-info me-2"></i>
                <i class="bi bi-hospital fs-1 text-primary"></i>
            </div>

            <h2 class="display-6 fw-extrabold text-white mb-3 lh-base">
                المنصة الرقمية الموحدة <br>
                <span class="text-transparent bg-clip-text text-info fw-bold">للتعليم الطبي والدراسات العليا</span>
            </h2>

            <p class="text-light opacity-75 fs-6 mb-5 lh-lg">
                نظام إلكتروني متكامل لإدارة بيانات المرشحين والدرجات العلمية والأقسام الطبية بكل سهولة وأمان، يدعم التحول الرقمي وإتاحة الاستعلامات الفورية.
            </p>

            <!-- كروت إحصائية سريعة عائمة زجاجية (Glassmorphism) -->
            <div class="row g-3">
                <div class="col-6">
                    <div class="glass-card p-3 rounded-4 text-start">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-shield-check fs-4 text-success"></i>
                            <span class="fw-bold fs-5 text-white">100%</span>
                        </div>
                        <p class="small text-light opacity-75 m-0">نظام آمن ومحمي بالكامل</p>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>
