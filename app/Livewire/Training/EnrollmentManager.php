<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class EnrollmentManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filter_schedule_id = '';
    public $filter_attendance = '';
    public $filter_status = ''; // حالة الاعتماد (مقبول/مستبعد/معلق)
    public $filter_program_id = '';
    public $filter_date = '';
    public $filter_capacity_status = '';

    // متغيرات تعديل حالة التسجيل
    public $enrollmentId;
    public $status = 'pending'; // pending, approved, rejected
    public $rejection_reason = '';
    public $attendance_status = 'pending';
    public $evaluation_status = '';
    public $certificate_issued = 0;

    protected $queryString = ['search', 'filter_schedule_id', 'filter_program_id', 'filter_capacity_status', 'filter_status', 'filter_attendance'];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterScheduleId() { $this->resetPage(); }
    public function updatingFilterProgramId() { $this->resetPage(); }
    public function updatingFilterDate() { $this->resetPage(); }
    public function updatingFilterCapacityStatus() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterAttendance() { $this->resetPage(); }

    public function openEditModal($id)
    {
        $enrollment = DB::table('trainee_enrollments')->where('id', $id)->first();
        if ($enrollment) {
            $this->enrollmentId = $enrollment->id;
            $this->status = $enrollment->status ?? 'pending';
            $this->rejection_reason = $enrollment->rejection_reason ?? '';
            $this->attendance_status = $enrollment->attendance_status ?? 'pending';
            $this->evaluation_status = $enrollment->evaluation_status ?? '';
            $this->certificate_issued = $enrollment->certificate_issued ?? 0;
            $this->dispatch('show-enrollment-modal');
        }
    }

    public function updateEnrollment()
    {
        // بناءً على عمود الحضور، نقوم بتحديث موقف نهاية البرنامج تلقائياً
        $evalStatus = $this->evaluation_status;

        if ($this->attendance_status === 'passed') {
            $evalStatus = 'اجتاز البرنامج';
        } elseif ($this->attendance_status === 'failed') {
            $evalStatus = 'تجاوز نسبة الغياب';
        }

        $data = [
            'status' => $this->status,
            'rejection_reason' => $this->status === 'rejected' ? $this->rejection_reason : null,
            'attendance_status' => $this->attendance_status,
            'evaluation_status' => $evalStatus,
            'certificate_issued' => $this->certificate_issued,
            'updated_at' => now(),
        ];

        if ($this->certificate_issued == 1) {
            $current = DB::table('trainee_enrollments')->where('id', $this->enrollmentId)->first();
            if (!$current->certificate_code) {
                $data['certificate_code'] = 'CERT-' . date('Y') . '-' . rand(10000, 99999);
                $data['certificate_issued_at'] = now();
            }
        }

        DB::table('trainee_enrollments')
            ->where('id', $this->enrollmentId)
            ->update($data);

        session()->flash('message', 'تم تحديث موقف التسجيل والحضور وموقف نهاية البرنامج بنجاح.');
        $this->dispatch('close-enrollment-modal');
    }

    public function deleteEnrollment($id)
    {
        DB::table('trainee_enrollments')->where('id', $id)->delete();
        session()->flash('message', 'تم إزالة تسجيل المتدرب من البرنامج بنجاح.');
    }

    public function render()
    {
        // 1. حساب الإحصائيات الحية
        $totalEnrollmentsCount = DB::table('trainee_enrollments')->count();

        $attendedCount = DB::table('trainee_enrollments')
            ->where('attendance_status', 'passed')
            ->count();

        $failedAttendanceCount = DB::table('trainee_enrollments')
            ->where('attendance_status', 'failed')
            ->count();

        $availableSchedulesCount = DB::table('training_program_schedules as tps')
            ->whereRaw('(SELECT COUNT(*) FROM trainee_enrollments WHERE training_program_schedule_id = tps.id) < tps.max_capacity')
            ->count();

        // 2. الاستعلام الرئيسي
        $query = DB::table('trainee_enrollments as te')
            ->join('trainees as t', 't.id', '=', 'te.trainee_id')
            ->join('training_program_schedules as tps', 'tps.id', '=', 'te.training_program_schedule_id')
            ->join('training_programs as tp', 'tp.id', '=', 'tps.training_program_id')
            ->select(
                'te.*',
                't.name_ar as trainee_name',
                't.national_id',
                't.phone_number',
                't.job_title',
                't.workplace',
                'tp.name_ar as program_name',
                'tps.start_date',
                'tps.end_date',
                'tps.time_slot',
                'tps.max_capacity',
                DB::raw('(SELECT COUNT(*) FROM trainee_enrollments WHERE training_program_schedule_id = tps.id) as current_enrolled')
            )
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('t.name_ar', 'like', '%' . $this->search . '%')
                        ->orWhere('t.national_id', 'like', '%' . $this->search . '%')
                        ->orWhere('t.phone_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filter_schedule_id, function ($q) {
                $q->where('te.training_program_schedule_id', $this->filter_schedule_id);
            })
            ->when($this->filter_program_id, function ($q) {
                $q->where('tp.id', $this->filter_program_id);
            })
            ->when($this->filter_date, function ($q) {
                $q->where('tps.start_date', $this->filter_date);
            })
            ->when($this->filter_status, function ($q) {
                $q->where('te.status', $this->filter_status);
            })
            ->when($this->filter_attendance, function ($q) {
                $q->where('te.attendance_status', $this->filter_attendance);
            })
            ->when($this->filter_capacity_status, function ($q) {
                if ($this->filter_capacity_status == 'full') {
                    $q->whereRaw('(SELECT COUNT(*) FROM trainee_enrollments WHERE training_program_schedule_id = tps.id) >= tps.max_capacity');
                } elseif ($this->filter_capacity_status == 'available') {
                    $q->whereRaw('(SELECT COUNT(*) FROM trainee_enrollments WHERE training_program_schedule_id = tps.id) < tps.max_capacity');
                }
            });

        $enrollments = $query->latest('te.enrolled_at')->paginate(10);

        // تحديث "موقف المتدرب نهاية البرنامج" تلقائياً في العرض بناءً على عمود الحضور مباشرة
        foreach ($enrollments as $row) {
            if ($row->attendance_status === 'passed' && $row->evaluation_status !== 'اجتاز البرنامج') {
                DB::table('trainee_enrollments')->where('id', $row->id)->update(['evaluation_status' => 'اجتاز البرنامج']);
                $row->evaluation_status = 'اجتاز البرنامج';
            } elseif ($row->attendance_status === 'failed' && $row->evaluation_status !== 'تجاوز نسبة الغياب') {
                DB::table('trainee_enrollments')->where('id', $row->id)->update(['evaluation_status' => 'تجاوز نسبة الغياب']);
                $row->evaluation_status = 'تجاوز نسبة الغياب';
            }
        }

        $schedules = DB::table('training_program_schedules as tps')
            ->join('training_programs as tp', 'tp.id', '=', 'tps.training_program_id')
            ->select('tps.id', 'tp.name_ar', 'tps.start_date')
            ->get();

        $programs = DB::table('training_programs')->select('id', 'name_ar')->get();

        return view('livewire.training.enrollment-manager', [
            'enrollments' => $enrollments,
            'schedules' => $schedules,
            'programs' => $programs,
            'totalEnrollmentsCount' => $totalEnrollmentsCount,
            'attendedCount' => $attendedCount,
            'failedAttendanceCount' => $failedAttendanceCount,
            'availableSchedulesCount' => $availableSchedulesCount,
        ])->layout('layouts.app');
    }
}
