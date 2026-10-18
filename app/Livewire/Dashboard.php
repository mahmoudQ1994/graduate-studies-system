<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PostgraduateRegistration;
use App\Models\TrainingSecondment;
use App\Models\RegistrationPause;
use App\Models\Specialty;
use App\Models\Facility;
use App\Models\User;
use App\Models\Department;
use Carbon\Carbon;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'total_candidates' => PostgraduateRegistration::count(),
            'total_users' => User::count(),
            'total_specialties' => Specialty::count(),
            'total_facilities' => Facility::count(),
            'total_departments' => Department::count(),
            'active_trainings' => TrainingSecondment::where('end_date', '>=', Carbon::today()->format('Y-m-d'))->count(),
            'completed_degree' => PostgraduateRegistration::where('study_status', 'حصل على الدرجة')->count(),
            'paused_registrations' => RegistrationPause::count(),

            // توزيع الحالات الدراسية بدقة حسب المسميات المعتمدة
            'status_pending' => PostgraduateRegistration::where('study_status', 'جاري فحص الطلب')->count(),
            'status_ongoing' => PostgraduateRegistration::where('study_status', 'مستمر بالدراسة')->count(),
            'status_graduated' => PostgraduateRegistration::where('study_status', 'حصل على الدرجة')->count(),
            'status_apology' => PostgraduateRegistration::where('study_status', 'اعتذار عن الدراسة')->count(),
            'status_rejected' => PostgraduateRegistration::where('study_status', 'عدم القبول بالدراسة')->count(),

            // التنبيهات (إجازات التفرغ التي تنتهي خلال 30 يوماً)
            'expiring_leaves_count' => PostgraduateRegistration::whereHas('studyLeaves', function ($query) {
                $query->whereNull('actual_return_date')
                      ->whereNotNull('end_date')
                      ->where('end_date', '<=', Carbon::now()->addDays(30));
            })->count(),
        ];

        $latestCandidates = PostgraduateRegistration::with(['healthProfessional.facility'])
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.dashboard', [
            'stats' => $stats,
            'latestCandidates' => $latestCandidates,
        ])->layout('layouts.app');
    }
}
