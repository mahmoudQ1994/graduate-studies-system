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

<div class="card border-0 shadow-lg rounded-4 overflow-hidden">
    <!-- هيدر الكارت العلوي -->
    <div class="card-header bg-primary text-white text-center py-4 border-0">
        <div class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 60px; height: 60px;">
            <i class="bi bi-mortarboard-fill fs-2"></i>
        </div>
        <h4 class="fw-bold mb-1">تسجيل الدخول</h4>
        <p class="small text-white-50 mb-0">نظام إدارة التدريب الطبي والدراسات العليا</p>
    </div>

    <div class="card-body p-4 p-sm-5 bg-white">
        <!-- عرض أخطاء الجلسة (مثل كلمة المرور خاطئة) -->
        <x-auth-session-status class="mb-4 text-danger small" :status="session('status')" />

        <form wire:submit="login">
            <!-- 1. البريد الإلكتروني -->
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-secondary">البريد الإلكتروني</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username"
                           class="form-control bg-light border-start-0 ps-0 @error('form.email') is-invalid @enderror"
                           placeholder="name@example.com">
                </div>
                @error('form.email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- 2. كلمة المرور -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label fw-semibold text-secondary mb-0">كلمة المرور</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-primary text-decoration-none small fw-semibold" wire:navigate>
                            نسيت كلمة المرور؟
                        </a>
                    @endif
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input wire:model="form.password" id="password" type="password" name="password" required autocomplete="current-password"
                           class="form-control bg-light border-start-0 ps-0 @error('form.password') is-invalid @enderror"
                           placeholder="••••••••">
                </div>
                @error('form.password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- 3. تذكرني -->
            <div class="form-check mb-4">
                <input wire:model="form.remember" id="remember_me" type="checkbox" class="form-check-input" name="remember">
                <label for="remember_me" class="form-check-label text-secondary small">
                    تذكر تسجيل دخولي
                </label>
            </div>

            <!-- 4. زر الدخول -->
            <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold shadow-sm rounded-3">
                <span wire:loading.remove>دخول للنظام</span>
                <span wire:loading>
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    جاري التحقق...
                </span>
            </button>
        </form>
    </div>
</div>
