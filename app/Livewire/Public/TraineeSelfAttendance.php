<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TraineeSelfAttendance extends Component
{
    public $schedule_id;
    public $national_id = '';
    public $successMessage = '';
    public $errorMessage = '';
    public $expiredMessage = ''; // رسالة انتهاء فترة البرنامج

    public function mount($schedule_id = null)
    {
        $this->schedule_id = $schedule_id;

        if (!$this->schedule_id) {
            $this->errorMessage = 'رابط الجلسة التدريبية غير صالح أو مفقود.';
        }
    }

    public function registerAttendance()
    {
        $this->reset(['successMessage', 'errorMessage']);

        if (empty($this->national_id)) {
            $this->errorMessage = 'يرجى إدخال الرقم القومي بشكل صحيح.';
            return;
        }

        if (!$this->schedule_id) {
            $this->errorMessage = 'عفواً، لا يوجد موعد تدريبي محدد.';
            return;
        }

        $schedule = DB::table('training_program_schedules as tps')
            ->join('training_programs as tp', 'tp.id', '=', 'tps.training_program_id')
            ->where('tps.id', $this->schedule_id)
            ->select('tps.*', 'tp.name_ar as program_name')
            ->first();

        if (!$schedule) {
            $this->errorMessage = 'الموعد التدريبي غير موجود بالنظام.';
            return;
        }

        $today = Carbon::today();
        $startDate = Carbon::parse($schedule->start_date)->startOfDay();
        $endDate = Carbon::parse($schedule->end_date)->endOfDay();

        // التحقق إذا انتهت فترة البرنامج
        if ($today->gt($endDate)) {
            $this->errorMessage = 'الرابط لا يعمل حالياً بسبب انتهاء فترة تنفيذ البرنامج التدريبي.';
            return;
        }

        // التحقق إذا كانت الدورة لم تبدأ بعد
        if ($today->lt($startDate)) {
            $this->errorMessage = 'عفواً، فترة تنفيذ البرنامج التدريبي لم تبدأ بعد (تبدأ في ' . $schedule->start_date . ').';
            return;
        }

        $trainee = DB::table('trainees')->where('national_id', $this->national_id)->first();

        if (!$trainee) {
            $this->errorMessage = 'الرقم القومي غير مسجل بقاعدة بيانات المتدربين.';
            return;
        }

        $enrollment = DB::table('trainee_enrollments')
            ->where('training_program_schedule_id', $this->schedule_id)
            ->where('trainee_id', $trainee->id)
            ->where('status', 'approved')
            ->first();

        if (!$enrollment) {
            $this->errorMessage = 'عفواً، أنت لست مسجلاً أو غير معتمد ضمن المقبولين في هذا البرنامج التدريبي.';
            return;
        }

        DB::table('training_attendances')->updateOrInsert(
            [
                'training_program_schedule_id' => $this->schedule_id,
                'trainee_id' => $trainee->id,
                'attendance_date' => $today->format('Y-m-d'),
            ],
            [
                'status' => 'present',
                'notes' => 'تسجيل ذاتي عبر QR Code (Public)',
                'created_by' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $this->successMessage = 'مرحباً بك يا ' . $trainee->name_ar . '! تم تسجيل حضورك بنجاح في برنامج (' . $schedule->program_name . ') ليوم ' . $today->format('Y-m-d') . '.';
        $this->national_id = '';
    }

    public function render()
    {
        $settings = DB::table('settings')->first();
        $scheduleDetails = null;
        $dayLabel = '';
        $isExpired = false;

        if ($this->schedule_id) {
            $scheduleDetails = DB::table('training_program_schedules as tps')
                ->join('training_programs as tp', 'tp.id', '=', 'tps.training_program_id')
                ->where('tps.id', $this->schedule_id)
                ->select('tps.*', 'tp.name_ar as program_name')
                ->first();

            if ($scheduleDetails) {
                $startDate = Carbon::parse($scheduleDetails->start_date);
                $endDate = Carbon::parse($scheduleDetails->end_date);
                $today = Carbon::today();

                if ($today->gt($endDate)) {
                    $isExpired = true;
                } elseif ($today->between($startDate, $endDate)) {
                    $dayNumber = $startDate->diffInDays($today) + 1;
                    $dayLabel = 'اليوم التدريبي رقم (' . $dayNumber . ')';
                } else {
                    $dayLabel = 'فترة القبول / لم تبدأ بعد';
                }
            }
        }

        return view('livewire.public.trainee-self-attendance', [
            'settings' => $settings,
            'scheduleDetails' => $scheduleDetails,
            'dayLabel' => $dayLabel,
            'isExpired' => $isExpired,
        ])->layout('layouts.guest');
    }
}
