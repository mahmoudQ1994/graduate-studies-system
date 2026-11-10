<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DailyAttendanceManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $programSearch = '';
    public $selected_program_id = '';
    public $selected_schedule_id = '';
    public $attendance_date;
    public $search = '';

    public $attendanceStatuses = [];
    public $attendanceNotes = [];

    // متغيرات QR Code الذاتي
    public $showQrModal = false;
    public $generatedQrUrl = '';

    // متغيرات QR Code التقييم
    public $showEvalQrModal = false;
    public $generatedEvalUrl = '';

    public function mount()
    {
        $this->attendance_date = date('Y-m-d');
    }

    public function updatedProgramSearch($value)
    {
        if (empty($value)) {
            $this->selected_program_id = '';
            $this->selected_schedule_id = '';
        }
    }

    public function updatedSelectedProgramId()
    {
        $this->selected_schedule_id = '';
        $this->resetPage();
    }

    public function updatedSelectedScheduleId()
    {
        $this->loadScheduleDefaultDate();
        $this->loadExistingAttendances();
        $this->resetPage();
    }

    // ضبط تاريخ اليوم تلقائياً ليكون ضمن فترة البرنامج عند اختيار الموعد
    public function loadScheduleDefaultDate()
    {
        if ($this->selected_schedule_id) {
            $schedule = DB::table('training_program_schedules')
                ->where('id', $this->selected_schedule_id)
                ->select('start_date', 'end_date')
                ->first();

            if ($schedule) {
                $today = Carbon::today();
                $start = Carbon::parse($schedule->start_date);
                $end = Carbon::parse($schedule->end_date);

                if ($today->between($start, $end)) {
                    $this->attendance_date = $today->format('Y-m-d');
                } else {
                    $this->attendance_date = $schedule->start_date;
                }
            }
        }
    }

    // دالة توليد رابط الحضور الذاتي للجدول المختار
    public function generateQrCodeLink()
    {
        if (!$this->selected_schedule_id) {
            session()->flash('error', 'يرجى اختيار البرنامج وموعد التنفيذ أولاً لتوليد الرابط.');
            return;
        }

        // توليد الرابط الخاص بهذا الجدول (ليعمل مع مسار الـ Public الذي أنشأناه)
        $this->generatedQrUrl = route('training.self-attendance', ['schedule_id' => $this->selected_schedule_id]);
        $this->showQrModal = true;
    }

    public function generateEvalQrCodeLink()
    {
        if (!$this->selected_schedule_id) {
            session()->flash('error', 'يرجى اختيار البرنامج وموعد التنفيذ أولاً لتوليد رابط التقييم.');
            return;
        }

        // توليد رابط التقييم الخاص بهذا السجل
        $this->generatedEvalUrl = route('training.evaluation', ['schedule_id' => $this->selected_schedule_id]);
        $this->showEvalQrModal = true;
    }

    public function closeEvalQrModal()
    {
        $this->showEvalQrModal = false;
    }

    public function closeQrModal()
    {
        $this->showQrModal = false;
    }

    // دالة احتساب واعتماد حالات الحضور بناءً على نسبة 25% كحد أدنى
    public function calculateAttendanceStatuses()
    {
        if (!$this->selected_schedule_id) {
            session()->flash('error', 'يرجى اختيار الموعد التدريبي أولاً.');
            return;
        }

        $schedule = DB::table('training_program_schedules')->where('id', $this->selected_schedule_id)->first();
        if (!$schedule) {
            session()->flash('error', 'الموعد التدريبي غير موجود.');
            return;
        }

        $startDate = Carbon::parse($schedule->start_date);
        $endDate = Carbon::parse($schedule->end_date);
        $totalTrainingDays = $startDate->diffInDays($endDate) + 1;

        if ($totalTrainingDays <= 0) {
            session()->flash('error', 'خطأ في أيام البرنامج التدريبي.');
            return;
        }

        $enrollments = DB::table('trainee_enrollments')
            ->where('training_program_schedule_id', $this->selected_schedule_id)
            ->where('status', 'approved')
            ->get();

        $updatedCount = 0;

        foreach ($enrollments as $enrollment) {
            $attendedDaysCount = DB::table('training_attendances')
                ->where('training_program_schedule_id', $this->selected_schedule_id)
                ->where('trainee_id', $enrollment->trainee_id)
                ->where('status', 'present')
                ->count();

            $attendancePercentage = ($attendedDaysCount / $totalTrainingDays) * 100;

            // إذا حقق 25% أو أكثر يعتبر اجتاز (passed)، وإلا (failed)
            $newStatus = ($attendancePercentage >= 75) ? 'passed' : 'failed';

            DB::table('trainee_enrollments')
                ->where('id', $enrollment->id)
                ->update([
                    'attendance_status' => $newStatus,
                    'updated_at' => now(),
                ]);

            $updatedCount++;
        }

        session()->flash('message', 'تم احتساب واعتماد حالات الحضور بنجاح لـ (' . $updatedCount . ') متدرباً بناءً على نسبة 25% كحد أدنى.');
    }

    public function updatedAttendanceDate()
    {
        $this->loadExistingAttendances();
    }

    public function getMatchingProgramsProperty()
    {
        if (strlen(trim($this->programSearch)) < 1) {
            return collect();
        }
        return DB::table('training_programs')
            ->where('name_ar', 'like', '%' . $this->programSearch . '%')
            ->limit(6)
            ->get();
    }

    public function selectProgram($id, $name)
    {
        $this->selected_program_id = $id;
        $this->programSearch = $name;
        $this->selected_schedule_id = '';
        $this->resetPage();
    }

    // تحميل الحضور المسجل مسبقاً وتعبئة مصفوفة الحالات بدقة للتعديل
    public function loadExistingAttendances()
    {
        if (!$this->selected_schedule_id || !$this->attendance_date) {
            return;
        }

        $records = DB::table('training_attendances')
            ->where('training_program_schedule_id', $this->selected_schedule_id)
            ->where('attendance_date', $this->attendance_date)
            ->get();

        $this->attendanceStatuses = [];
        $this->attendanceNotes = [];

        foreach ($records as $rec) {
            $this->attendanceStatuses[$rec->trainee_id] = $rec->status;
            $this->attendanceNotes[$rec->trainee_id] = $rec->notes;
        }
    }

    public function saveAttendance()
    {
        if (!$this->selected_schedule_id) {
            session()->flash('error', 'يرجى اختيار البرنامج وتاريخ التنفيذ أولاً.');
            return;
        }

        if (!$this->attendance_date) {
            session()->flash('error', 'يرجى تحديد تاريخ اليوم التدريبي.');
            return;
        }

        // التحقق من أن التاريخ يقع ضمن فترة البرنامج حصراً
        $schedule = DB::table('training_program_schedules')
            ->where('id', $this->selected_schedule_id)
            ->select('start_date', 'end_date')
            ->first();

        if ($schedule) {
            $startDate = Carbon::parse($schedule->start_date)->startOfDay();
            $endDate = Carbon::parse($schedule->end_date)->endOfDay();
            $targetDate = Carbon::parse($this->attendance_date)->startOfDay();

            if (!$targetDate->between($startDate, $endDate)) {
                session()->flash('error', 'عفواً، لا يمكن تسجيل الحضور! التاريخ المحدد خارج فترة البرنامج (من ' . $schedule->start_date . ' إلى ' . $schedule->end_date . ').');
                return;
            }
        }

        $userId = Auth::id();
        $now = now();

        $approvedTrainees = DB::table('trainee_enrollments')
            ->where('training_program_schedule_id', $this->selected_schedule_id)
            ->where('status', 'approved')
            ->pluck('trainee_id');

        foreach ($approvedTrainees as $traineeId) {
            $status = $this->attendanceStatuses[$traineeId] ?? 'present';
            $notes = $this->attendanceNotes[$traineeId] ?? null;

            DB::table('training_attendances')->updateOrInsert(
                [
                    'training_program_schedule_id' => $this->selected_schedule_id,
                    'trainee_id' => $traineeId,
                    'attendance_date' => $this->attendance_date,
                ],
                [
                    'status' => $status,
                    'notes' => $notes,
                    'created_by' => $userId,
                    'updated_at' => $now,
                ]
            );
        }

        $this->loadExistingAttendances();

        session()->flash('message', 'تم حفظ وتحديث كشف الحضور اليومي وتوثيق بيانات المسؤول بنجاح!');
    }

    public function render()
    {
        $availableSchedules = collect();
        if ($this->selected_program_id) {
            $availableSchedules = DB::table('training_program_schedules')
                ->where('training_program_id', $this->selected_program_id)
                ->select('id', 'start_date', 'end_date', 'max_capacity')
                ->orderBy('start_date', 'desc')
                ->get();
        }

        $approvedTrainees = collect();
        $selectedScheduleInfo = null;
        $stats = ['present' => 0, 'absent' => 0, 'excused' => 0, 'total' => 0];
        $dayInfo = null;

        if ($this->selected_schedule_id) {
            $selectedScheduleInfo = DB::table('training_program_schedules as tps')
                ->join('training_programs as tp', 'tp.id', '=', 'tps.training_program_id')
                ->where('tps.id', $this->selected_schedule_id)
                ->select('tp.name_ar as program_name', 'tps.start_date', 'tps.end_date')
                ->first();

            if ($selectedScheduleInfo) {
                $startDate = Carbon::parse($selectedScheduleInfo->start_date);
                $endDate = Carbon::parse($selectedScheduleInfo->end_date);
                $currentDate = Carbon::parse($this->attendance_date);

                $totalDays = $startDate->diffInDays($endDate) + 1;
                $currentDayNumber = $startDate->diffInDays($currentDate) + 1;
                $isWithinRange = $currentDate->between($startDate, $endDate);

                $dayInfo = [
                    'total_days' => $totalDays,
                    'current_day' => $isWithinRange ? $currentDayNumber : null,
                    'is_valid' => $isWithinRange,
                ];
            }

            $stats['total'] = DB::table('trainee_enrollments')
                ->where('training_program_schedule_id', $this->selected_schedule_id)
                ->where('status', 'approved')
                ->count();

            $dayAttendances = DB::table('training_attendances')
                ->where('training_program_schedule_id', $this->selected_schedule_id)
                ->where('attendance_date', $this->attendance_date)
                ->get();

            foreach ($dayAttendances as $att) {
                if (isset($stats[$att->status])) {
                    $stats[$att->status]++;
                }
            }

            $approvedTrainees = DB::table('trainee_enrollments as te')
                ->join('trainees as t', 't.id', '=', 'te.trainee_id')
                ->where('te.training_program_schedule_id', $this->selected_schedule_id)
                ->where('te.status', 'approved')
                ->when($this->search, function ($q) {
                    $q->where(function ($query) {
                        $query->where('t.name_ar', 'like', '%' . $this->search . '%')
                            ->orWhere('t.national_id', 'like', '%' . $this->search . '%');
                    });
                })
                ->select('te.*', 't.name_ar', 't.national_id', 't.qualification', 't.phone_number')
                ->paginate(20);

            foreach ($approvedTrainees as $trainee) {
                $attendanceRecord = DB::table('training_attendances as ta')
                    ->leftJoin('users as u', 'u.id', '=', 'ta.created_by')
                    ->where('ta.training_program_schedule_id', $this->selected_schedule_id)
                    ->where('ta.trainee_id', $trainee->trainee_id)
                    ->where('ta.attendance_date', $this->attendance_date)
                    ->select('ta.status', 'ta.notes', 'u.name as creator_name', 'ta.updated_at')
                    ->first();

                if ($attendanceRecord) {
                    if (!isset($this->attendanceStatuses[$trainee->trainee_id])) {
                        $this->attendanceStatuses[$trainee->trainee_id] = $attendanceRecord->status;
                    }
                    $this->attendanceNotes[$trainee->trainee_id] = $attendanceRecord->notes;
                    $trainee->creator_name = $attendanceRecord->creator_name ?? '---';
                    $trainee->recorded_at = $attendanceRecord->updated_at ? Carbon::parse($attendanceRecord->updated_at)->format('Y-m-d H:i') : '---';
                } else {
                    if (!isset($this->attendanceStatuses[$trainee->trainee_id])) {
                        $this->attendanceStatuses[$trainee->trainee_id] = 'present';
                    }
                    $trainee->creator_name = 'لم يُحفظ بعد';
                    $trainee->recorded_at = '---';
                }
            }
        }

        return view('livewire.training.daily-attendance-manager', [
            'availableSchedules' => $availableSchedules,
            'approvedTrainees' => $approvedTrainees,
            'selectedScheduleInfo' => $selectedScheduleInfo,
            'stats' => $stats,
            'dayInfo' => $dayInfo,
        ])->layout('layouts.app');
    }
}
