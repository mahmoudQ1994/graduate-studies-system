<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class TraineeEvaluation extends Component
{
    public $schedule_id;
    public $national_id = '';
    public $official_email = '';
    public $phone_number = '';

    // 1. المحتوى
    public $content_clarity = 5;
    public $content_relevance = 5;
    public $content_depth = 5;
    public $content_time_balance = 5;

    // 2. المدرب
    public $trainer_knowledge = 5;
    public $trainer_delivery = 5;
    public $trainer_interaction = 5;
    public $trainer_time_management = 5;

    // 3. البيئة
    public $hall_comfort = 5;
    public $equipment_quality = 5;
    public $catering_services = 5;

    // 4. الإدارة
    public $admin_response = 5;
    public $registration_process = 5;

    // 5. الأثر والرضا
    public $practical_application = 5;
    public $overall_satisfaction = 5;
    public $recommend_to_colleague = true;

    // ملاحظات
    public $strengths_notes = '';
    public $improvement_notes = '';

    public $successMessage = '';
    public $errorMessage = '';

    public function mount($schedule_id = null)
    {
        $this->schedule_id = $schedule_id;
    }

    public function submitEvaluation()
    {
        $this->reset(['successMessage', 'errorMessage']);

        if (empty($this->national_id) || empty($this->official_email)) {
            $this->errorMessage = 'يرجى إدخال الرقم القومي والبريد الإلكتروني الرسمي لاستلام الشهادة.';
            return;
        }

        if (!filter_var($this->official_email, FILTER_VALIDATE_EMAIL)) {
            $this->errorMessage = 'صيغة البريد الإلكتروني غير صحيحة.';
            return;
        }

        // 1. التحقق من المتدرب
        $trainee = DB::table('trainees')->where('national_id', $this->national_id)->first();
        if (!$trainee) {
            $this->errorMessage = 'الرقم القومي غير مسجل بقاعدة بيانات المتدربين.';
            return;
        }

        // 2. التحقق من التسجيل والقبول
        $enrollment = DB::table('trainee_enrollments')
            ->where('training_program_schedule_id', $this->schedule_id)
            ->where('trainee_id', $trainee->id)
            ->where('status', 'approved')
            ->first();

        if (!$enrollment) {
            $this->errorMessage = 'عفواً، أنت لست مسجلاً أو غير معتمد ضمن المقبولين في هذا البرنامج التدريبي.';
            return;
        }

        // 3. منع التقييم المكرر
        $alreadyEvaluated = DB::table('program_evaluations')
            ->where('training_program_schedule_id', $this->schedule_id)
            ->where('trainee_id', $trainee->id)
            ->exists();

        if ($alreadyEvaluated) {
            $this->errorMessage = 'لقد قمت بإرسال استمارة التقييم الخاصة بهذا البرنامج مسبقاً.';
            return;
        }

        // 4. أدراج التقييم الشامل في قاعدة البيانات
        DB::table('program_evaluations')->insert([
            'training_program_schedule_id' => $this->schedule_id,
            'trainee_id'                   => $trainee->id,
            'official_email'               => $this->official_email,
            'phone_number'                 => $this->phone_number,
            'content_clarity'              => $this->content_clarity,
            'content_relevance'            => $this->content_relevance,
            'content_depth'                => $this->content_depth,
            'content_time_balance'         => $this->content_time_balance,
            'trainer_knowledge'            => $this->trainer_knowledge,
            'trainer_delivery'             => $this->trainer_delivery,
            'trainer_interaction'          => $this->trainer_interaction,
            'trainer_time_management'      => $this->trainer_time_management,
            'hall_comfort'                 => $this->hall_comfort,
            'equipment_quality'            => $this->equipment_quality,
            'catering_services'            => $this->catering_services,
            'admin_response'               => $this->admin_response,
            'registration_process'         => $this->registration_process,
            'practical_application'        => $this->practical_application,
            'overall_satisfaction'         => $this->overall_satisfaction,
            'recommend_to_colleague'       => $this->recommend_to_colleague ? 1 : 0,
            'strengths_notes'              => $this->strengths_notes,
            'improvement_notes'            => $this->improvement_notes,
            'created_at'                   => now(),
            'updated_at'                   => now(),
        ]);

        // 5. تحديث البريد الإلكتروني وموقف التقييم في جدول trainee_enrollments
        DB::table('trainee_enrollments')
            ->where('id', $enrollment->id)
            ->update([
                'official_email'    => $this->official_email,
                'evaluation_status' => 'تم التقييم',
                'updated_at'        => now(),
            ]);

        $this->successMessage = 'شكراً لك يا ' . $trainee->name_ar . '! تم استلام استمارة التقييم وحفظ بريدك الإلكتروني (' . $this->official_email . ') بنجاح لاستلام الشهادة فور صدورها.';
        $this->national_id = '';
        $this->official_email = '';
    }

    public function render()
    {
        $settings = DB::table('settings')->first();
        $scheduleDetails = DB::table('training_program_schedules as tps')
            ->join('training_programs as tp', 'tp.id', '=', 'tps.training_program_id')
            ->where('tps.id', $this->schedule_id)
            ->select('tps.*', 'tp.name_ar as program_name')
            ->first();

        return view('livewire.public.trainee-evaluation', [
            'settings'        => $settings,
            'scheduleDetails' => $scheduleDetails,
        ])->layout('layouts.guest');
    }
}
