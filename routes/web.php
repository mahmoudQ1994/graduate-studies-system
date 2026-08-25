<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use App\Livewire\Settings\SiteSetting;
use App\Livewire\Settings\DepartmentManager;
use App\Livewire\Structure\DistrictManager;
use App\Livewire\Structure\SectorManager;
use App\Livewire\Structure\HospitalManager;
use App\Livewire\UserManagement\UserManager;
use App\Livewire\Postgraduate\CandidateSearch;
use App\Livewire\Postgraduate\CandidateRegister;

use App\Livewire\Postgraduate\ManageStudyLeaves;

// التوجيه التلقائي لصفحة تسجيل الدخول عند فتح الموقع
Route::get('/', function () {
    return redirect()->route('login');
});

// مسار تسجيل الخروج
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// المسارات المحمية (تتطلب تسجيل الدخول)
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // الإعدادات العامة للموقع
    Route::get('/settings/site', SiteSetting::class)->name('site-settings');

    Route::get('/settings/departments', DepartmentManager::class)->name('departments');

    // مسار إدارة المراكز والمدن
    Route::get('/districts', DistrictManager::class)->name('districts.index');
    Route::get('/districts/create', DistrictManager::class)->name('districts.create');
    Route::get('/districts/{district}/edit', DistrictManager::class)->name('districts.edit');
    Route::get('/districts/{district}/delete', DistrictManager::class)->name('districts.delete');

    // مسار إدارة القطاعات
    Route::get('/sectors', SectorManager::class)->name('sectors.index');
    Route::get('/sectors/create', SectorManager::class)->name('sectors.create');
    Route::get('/sectors/{sector}/edit', SectorManager::class)->name('sectors.edit');
    Route::get('/sectors/{sector}/delete', SectorManager::class)->name('sectors.delete');

    // مسار إدارة المستشفيات والجهات الصحية
    Route::get('/hospitals', HospitalManager::class)->name('hospitals.index');
    Route::get('/hospitals/create', HospitalManager::class)->name('hospitals.create');
    Route::get('/hospitals/{hospital}/edit', HospitalManager::class)->name('hospitals.edit');
    Route::get('/hospitals/{hospital}/delete', HospitalManager::class)->name('hospitals.delete');

    // مسار إدارة المستخدمين
    Route::get('/users', UserManager::class)->name('users.index');
    Route::get('/users/create', UserManager::class)->name('users.create');
    Route::get('/users/{user}/edit', UserManager::class)->name('users.edit');
    Route::get('/users/{user}/delete', UserManager::class)->name('users.delete');

    // وحدة الدراسات العليا
    Route::prefix('postgraduate')->name('postgraduate.')->group(function () {
        // مسار البحث عن المرشحين
        Route::get('/candidate-search', CandidateSearch::class)->name('search');
        // مسار تسجيل المرشح    
        Route::get('/candidate-register', CandidateRegister::class)->name('register');

        // مسار متابعة وتحديث موقف المرشح
        Route::get('/manage-study-leaves', ManageStudyLeaves::class)->name('leaves');
    });

    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
