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
use App\Livewire\Postgraduate\PostgraduateCandidatesList;
use App\Livewire\Postgraduate\PostgraduateEdit;
use App\Livewire\Postgraduate\ManageStudyLeaves;
use App\Livewire\Postgraduate\ManageStudyStatus;
use App\Livewire\Postgraduate\ManageStudyPauses;
use App\Livewire\Postgraduate\OfficialLetterPrint;
use App\Livewire\Reports\GeneralDashboard;
use App\Livewire\Postgraduate\ImportExport;
use App\Livewire\Reports\LeaveMovementReport;
use App\Livewire\Reports\MedicalDegreesArchiveReport;
use App\Livewire\Reports\StudyPausesReport;
use App\Livewire\Reports\SemiAnnualCandidatesReport;
use App\Livewire\Postgraduate\CreateTrainingSecondment;
use App\Livewire\Postgraduate\TrainingSecondmentIndex;
use App\Livewire\Postgraduate\PrintTrainingMemo;
use App\Livewire\Reports\TrainingReport;
use App\Livewire\Structure\UniversityManager;
use App\Livewire\Structure\CollegeManager;
use App\Livewire\Settings\SpecialtyManager;
use App\Livewire\Dashboard;
use App\Livewire\UserProfile;
use App\Livewire\Training\ProgramTypesManager;
use App\Livewire\Training\HallsManager;
use App\Livewire\Training\MethodsManager;
use App\Livewire\Training\KnowledgeManager;
use App\Livewire\Training\ImpactManager;
use App\Livewire\Training\RejectionReasonsManager;
use App\Livewire\Training\HourlyRatesManager;
use App\Livewire\Training\JobStatusesManager;
use App\Livewire\Training\SpecializationsManager;
use App\Livewire\Training\JobGradesManager;
use App\Livewire\Training\TrainingProgramManager;
use App\Livewire\Training\TrainingApprovalManager;
use App\Livewire\Training\TrainingScheduleManager;
use App\Livewire\Training\TrainerManager;
use App\Livewire\Training\TrainerApprovalManager;
use App\Livewire\Training\TrainersList;
use App\Livewire\Training\TraineeManager;
use App\Livewire\Training\JobTitleManager;
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

    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/profile', UserProfile::class)->name('profile');

    // الإعدادات العامة للموقع
    Route::get('/settings/site', SiteSetting::class)->name('site-settings');
    Route::get('/settings/departments', DepartmentManager::class)->name('departments');
    // مسار إدارة المواهلات الوظيفية
    Route::get('/settings/specialties', SpecialtyManager::class)->name('specialties');

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

    // مسار إدارة الجامعات
    Route::get('/universities', UniversityManager::class)->name('universities.index');
    Route::get('/universities/create', UniversityManager::class)->name('universities.create');
    Route::get('/universities/{university}/edit', UniversityManager::class)->name('universities.edit');
    Route::get('/universities/{university}/delete', UniversityManager::class)->name('universities.delete');
    Route::get('/universities/export', UniversityManager::class)->name('universities.export');

    // مسار إدارة الكليات
    Route::get('/colleges', CollegeManager::class)->name('colleges.index');
    route::get('/colleges/create', CollegeManager::class)->name('colleges.create');
    route::get('/colleges/{college}/edit', CollegeManager::class)->name('colleges.edit');
    route::get('/colleges/{college}/delete', CollegeManager::class)->name('colleges.delete');
    route::get('/colleges/export', CollegeManager::class)->name('colleges.export');

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

        // مسار عرض قائمة المرشحين
        Route::get('/candidates-list', PostgraduateCandidatesList::class)->name('candidates-list');

        // مسار تعديل بيانات المرشح
        Route::get('/candidate-edit/{id}', PostgraduateEdit::class)->name('edit');

        // مسار حفظ تعديل بيانات المرشح
        Route::get('/candidates', PostgraduateCandidatesList::class)->name('candidates');

        // مسار تعديل الدراسة للمرشح
        Route::get('/manage-study-status', ManageStudyStatus::class)->name('study-status');

        // مسار تعديل واضافة اجازة تفرغ دراسى
        Route::get('/manage-study-leaves', ManageStudyLeaves::class)->name('leaves');
        //مسار ادارة الافاد للتدريب
        Route::get('/training-secondment', CreateTrainingSecondment::class)->name('training-secondment');
        // مسار عرض قائمة الافاد للتدريب
        Route::get('/training-secondment-index', TrainingSecondmentIndex::class)->name('training-secondment-index');

        // مسار إدارة إيقاف القيد
        Route::get('/study-pauses', ManageStudyPauses::class)->name('study-pauses');

        // مسار طباعة الخطاب الرسمي
        Route::get('/official-letter-print', OfficialLetterPrint::class)->name('official-letter-print');

        // مسار طباعة الافادة للتدريب
        Route::get('/print-training-memo', PrintTrainingMemo::class)->name('print-training-memo');

        Route::get('/general-dashboard', GeneralDashboard::class)->name('general-dashboard');

        // مسار عرض تقرير اجازات التفرغ
        Route::get('/leave-movement-report', LeaveMovementReport::class)->name('leave-movement-report');

        // مسار عرض تقرير ارشيف الحاصلين على الدرجات العلمية
        Route::get('/medical-degrees-archive-report', MedicalDegreesArchiveReport::class)->name('medical-degrees-archive-report');

        // مسار عرض تقرير إيقاف القيد
        Route::get('/study-pauses-report', StudyPausesReport::class)->name('study-pauses-report');

        //مسار عرض تقرير الافاد للتدريب
        Route::get('/reports/training', TrainingReport::class)->name('reports.training');
        // مسار عرض تقرير المرشحين للنصف السنوي
        Route::get('/semi-annual-candidates-report', SemiAnnualCandidatesReport::class)->name('semi-annual-candidates-report');

        // مسار استيراد وتصدير بيانات الدراسات العليا
        Route::get('/import-export', ImportExport::class)->name('import-export');

        Route::view('profile', 'profile')->name('profile');
    });

    //مسارات قسم التدريب
    Route::prefix('training')->name('training.')->group(function () {
        // مسار إدارة أنواع البرامج التدريبية
        Route::get('/program-types', ProgramTypesManager::class)->name('program-types');
        // مسار إدارة قاعات التدريب
        Route::get('/halls', HallsManager::class)->name('halls');
        // مسار إدارة أساليب التدريب
        Route::get('/methods', MethodsManager::class)->name('methods');
        // مسار إدارة المسميات الوظيفية
        Route::get('/job-titles', JobTitleManager::class)->name('job-titles');
        // مسار إدارة قياسات المعرفة
        Route::get('/knowledge-measurements', KnowledgeManager::class)->name('knowledge-measurements');
        // مسار إدارة قياسات الأثر
        Route::get('/impact-measurements', ImpactManager::class)->name('impact-measurements');
        // مسار إدارة أسباب الرفض
        Route::get('/rejection-reasons', RejectionReasonsManager::class)->name('rejection-reasons');
        // مسار إدارة معدلات الساعات التدريبية
        Route::get('/hourly-rates', HourlyRatesManager::class)->name('hourly-rates');
        // مسار إدارة الحالات الوظيفية
        Route::get('/job-statuses', JobStatusesManager::class)->name('job-statuses');
        // مسار إدارة التخصصات الفنية
        Route::get('/specializations', SpecializationsManager::class)->name('specializations');
        // مسار إدارة الدرجات الوظيفية
        Route::get('/job-grades', JobGradesManager::class)->name('job-grades');
        // مسار إدارة البرامج التدريبية
        Route::get('/programs', TrainingProgramManager::class)->name('programs');
        // مسار إدارة الاعتمادات والموافقات للبرامج التدريبية
        Route::get('/programs/approvals', TrainingApprovalManager::class)->name('programs.approvals');
        //مسار ادارة تنشيط البرامج
        Route::get('/programs/schedules', TrainingScheduleManager::class)->name('programs.schedules');
        // مسار انشاء المدربين
        Route::get('/trainers', TrainerManager::class)->name('trainers');
        // مسار إدارة الاعتمادات ومتابعة السجل التدقيقي للمدربين
        Route::get('/trainers/approvals', TrainerApprovalManager::class)->name('trainers.approvals');
        // مسار عرض قائمة المدربين
        Route::get('/trainers/list', TrainersList::class)->name('trainers.list');
        //مسار تعديل بيانات المدرب
        Route::get('/trainers/{trainer}/edit', TrainerManager::class)->name('trainers.edit');
        //مسار حذف بيانات المدرب
        Route::get('/trainers/{trainer}/delete', TrainerManager::class)->name('trainers.delete');
        // مسار انشاء المتدربين
        Route::get('/trainees', TraineeManager::class)->name('trainees');
    });

});

require __DIR__.'/auth.php';
