<?php

namespace App\Livewire\Postgraduate;

use Livewire\Component;
use App\Models\PostgraduateRegistration;
use App\Models\Facility;
use Carbon\Carbon;

class CandidateStatusUpdate extends Component
{
    public $registrationId;
    public $registration;

    // 1. بيانات جهة العمل وحركة النيابة القابلة للتعديل
    public $facility_id;
    public $secondment_facility;
    public $movement_specialty;
    public $movement_date;

    // 2. بيانات تنفيذ الدراسة
    public $execution_date;
    public $study_enrollment_date;

    // 3. بيانات إيقاف وتنشيط / إعادة قيد الدراسة
    public $is_suspended = false;
    public $suspension_reason = '';
    public $suspension_start_date = '';
    public $suspension_end_date = '';
    public $calculated_suspension_period = '';

    public function mount($registrationId)
    {
        $this->registrationId = $registrationId;
        $this->loadRegistrationData();
    }

    public function loadRegistrationData()
    {
        $this->registration = PostgraduateRegistration::with(['healthProfessional.facility', 'healthProfessional.movement'])->findOrFail($this->registrationId);

        $professional = $this->registration->healthProfessional;
        $this->facility_id = $professional->facility_id ?? null;
        $this->secondment_facility = $professional->secondment_facility ?? '';

        if ($professional && $professional->movement) {
            $this->movement_specialty = $professional->movement->specialty ?? '';
            $this->movement_date = $professional->movement->movement_date ?? '';
        }

        // بيانات تنفيذ الدراسة الحالية
        $this->execution_date = $this->registration->execution_date ?? '';
        $this->study_enrollment_date = $this->registration->study_enrollment_date ?? '';

        // بيانات الإيقاف الحالية
        $this->is_suspended = $this->registration->is_suspended ?? false;
        $this->suspension_reason = $this->registration->suspension_reason ?? '';
        $this->suspension_start_date = $this->registration->suspension_start_date ?? '';
        $this->suspension_end_date = $this->registration->suspension_end_date ?? '';

        $this->calculateSuspensionPeriod();
    }

    // حساب مدة الإيقاف تلقائياً بصيغة سنة وشهر
    public function updatedSuspensionStartDate() { $this->calculateSuspensionPeriod(); }
    public function updatedSuspensionEndDate() { $this->calculateSuspensionPeriod(); }

    public function calculateSuspensionPeriod()
    {
        if ($this->suspension_start_date && $this->suspension_end_date) {
            $start = Carbon::parse($this->suspension_start_date);
            $end = Carbon::parse($this->suspension_end_date);

            if ($end->greaterThanOrEqualTo($start)) {
                $diff = $start->diff($end);
                $years = $diff->y;
                $months = $diff->m;

                $periodStr = [];
                if ($years > 0) {
                    $periodStr[] = $years . ' ' . ($years == 1 ? 'سنة' : 'سنوات');
                }
                if ($months > 0) {
                    $periodStr[] = $months . ' ' . ($months == 1 ? 'شهر' : 'شهور');
                }

                $this->calculated_suspension_period = empty($periodStr) ? 'أقل من شهر' : implode(' و ', $periodStr);
            } else {
                $this->calculated_suspension_period = 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية';
            }
        } else {
            $this->calculated_suspension_period = '';
        }
    }

    // تفعيل أو إلغاء الإيقاف (إعادة القيد) بضغطة زر
    public function toggleSuspension($status)
    {
        $this->is_suspended = $status;
        if (!$status) {
            // تنشيط / إعادة قيد
            $this->suspension_reason = null;
            $this->suspension_start_date = null;
            $this->suspension_end_date = null;
            $this->calculated_suspension_period = '';
        }
    }

    public function updateRecord()
    {
        $this->validate([
            'facility_id' => 'required',
            'suspension_start_date' => 'nullable|date',
            'suspension_end_date' => 'nullable|date|after_or_equal:suspension_start_date',
        ]);

        // 1. تحديث بيانات جهة العمل والنيابة للمرشح
        $professional = $this->registration->healthProfessional;
        if ($professional) {
            $professional->update([
                'facility_id' => $this->facility_id,
                'secondment_facility' => $this->secondment_facility,
            ]);

            if ($professional->movement) {
                $professional->movement->update([
                    'specialty' => $this->movement_specialty,
                    'movement_date' => $this->movement_date,
                ]);
            }
        }

        // 2. تحديث بيانات التنفيذ وقيد الدراسة وحالة الإيقاف/التنشيط
        $this->registration->update([
            'execution_date' => $this->execution_date,
            'study_enrollment_date' => $this->study_enrollment_date,
            'is_suspended' => $this->is_suspended,
            'suspension_reason' => $this->is_suspended ? $this->suspension_reason : null,
            'suspension_start_date' => $this->is_suspended ? $this->suspension_start_date : null,
            'suspension_end_date' => $this->is_suspended ? $this->suspension_end_date : null,
            'study_status' => $this->is_suspended ? 'موقوف القيد' : 'ساري / قيد الدراسة',
        ]);

        session()->flash('success', 'تم تحديث موقف المرشح، وتاريخ التنفيذ، وبيانات النيابة بنجاح.');
    }

    public function render()
    {
        return view('livewire.postgraduate.candidate-status-update', [
            'facilities' => Facility::where('is_active', true)->get(),
        ])->layout('layouts.app');
    }
}
