<?php

namespace App\Livewire\Postgraduate;

use Livewire\Component;
use App\Models\HealthProfessional;
use App\Models\ProfessionalQualification;
use App\Models\MedicalMovement;
use App\Models\TrainingSecondment;
use App\Models\Facility;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CreateTrainingSecondment extends Component
{
    // يتحكم في الخطوة الحالية (الافتراضي الخطوة الأولى)
    public $currentStep = 1;

    // الانتقال للخطوة التالية مع التحقق البسيط حسب الحاجة
    public function nextStep()
    {
        $this->currentStep++;
    }

    // العودة للخطوة السابقة
    public function previousStep()
    {
        $this->currentStep--;
    }

    // متغيرات  للتحقق من حالة الإفاد النشط
    public $activeTrainingMessage = null;
    public $hasActiveTraining = false;
    // بيانات البحث والموظف الأساسية
    public $national_id;
    public $name;
    public $phone;
    public $profession;
    public $facility_id;
    public $secondment_facility;

    // بيانات المؤهل
    public $university;
    public $qualification;
    public $graduation_batch;
    public $general_grade;
    public $subject_grade;
    public $total_marks;

    // حركة النيابة
    public $specialty;
    public $movement_date;

    // بيانات الإفاد للتدريب وخصائص المد
    public $action_type = 'إفاد';
    public $training_duration_text;
    public $training_entity;
    public $training_department;
    public $duration_months;
    public $training_days = [];
    public $start_date;
    public $end_date;

    // متغيرات خاصة بالإفادات السابقة لخاصية "مد إفاد"
    public $previousTrainings = [];
    public $selectedPreviousTrainingId;

    /**
     * دالة لتحديد أو إلغاء تحديد جميع أيام الأسبوع (انتداب كلي)
     */
    public function toggleAllDays()
    {
        $days = ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];

        if (is_array($this->training_days) && count($this->training_days) == count($days)) {
            $this->training_days = [];
        } else {
            $this->training_days = $days;
        }
    }

    /**
     * البحث التلقائي عن بيانات الموظف والمؤهل وحركات النيابة والإفادات السابقة عند إدخال الرقم القومي
     */
    public function updatedNationalId($value)
    {
        $professional = HealthProfessional::with(['qualification', 'medicalMovements'])->where('national_id', $value)->first();

        if ($professional) {
            $this->name = $professional->name;
            $this->phone = $professional->phone;
            $this->profession = $professional->profession;
            $this->facility_id = $professional->facility_id;
            $this->secondment_facility = $professional->secondment_facility;

            // جلب أحدث مؤهل
            $qual = $professional->qualification()->latest()->first();
            if ($qual) {
                $this->university = $qual->university;
                $this->qualification = $qual->qualification;
                $this->graduation_batch = $qual->graduation_batch;
                $this->general_grade = $qual->general_grade;
                $this->subject_grade = $qual->subject_grade;
                $this->total_marks = $qual->total_marks;
            }

            // جلب أحدث حركة نيابة
            $mov = $professional->medicalMovements()->latest()->first();
            if ($mov) {
                $this->specialty = $mov->specialty;
                if (preg_match('/^\d{4}-\d{2}$/', $mov->movement_date)) {
                    $this->movement_date = $mov->movement_date;
                } elseif (!empty($mov->movement_date)) {
                    try {
                        $this->movement_date = Carbon::parse($mov->movement_date)->format('Y-m');
                    } catch (\Exception $e) {
                        $this->movement_date = null;
                    }
                } else {
                    $this->movement_date = null;
                }
            }

            // جلب الإفادات السابقة
            $this->previousTrainings = TrainingSecondment::where('health_professional_id', $professional->id)->get();

            // التحقق مما إذا كان الموظف لديه إفاد نشط حالياً
            $activeTraining = TrainingSecondment::where('health_professional_id', $professional->id)
                ->where('end_date', '>=', Carbon::today()->format('Y-m-d'))
                ->latest()
                ->first();

            if ($activeTraining) {
                $this->hasActiveTraining = true;
                $daysList = is_array($activeTraining->training_days) ? implode('، ', $activeTraining->training_days) : $activeTraining->training_days;

                $this->activeTrainingMessage = "الموظف موفد بالفعل إلى ({$activeTraining->training_entity}) " .
                    "من تاريخ {$activeTraining->start_date} إلى تاريخ {$activeTraining->end_date} " .
                    "بأيام تدريب: ({$daysList}). لا يمكن تسجيل إفاد جديد حتى ينتهي الإفاد الحالي، المتاح فقط هو 'مد إفاد'.";
            } else {
                $this->hasActiveTraining = false;
                $this->activeTrainingMessage = null;
            }

        } else {
            $this->reset([
                'name', 'phone', 'profession', 'facility_id', 'secondment_facility',
                'university', 'qualification', 'graduation_batch', 'general_grade',
                'subject_grade', 'total_marks', 'specialty', 'movement_date',
                'previousTrainings', 'selectedPreviousTrainingId', 'activeTrainingMessage', 'hasActiveTraining'
            ]);
        }
    }

    /**
     * جلب بيانات الإفاد السابق وتعبئتها تلقائياً عند اختيار إفاد لمدّه
     */
    public function updatedSelectedPreviousTrainingId($value)
    {
        if ($value) {
            $training = TrainingSecondment::find($value);
            if ($training) {
                $this->training_entity = $training->training_entity;
                $this->training_department = $training->training_department;
                $this->training_duration_text = $training->training_duration_text;
                $this->training_days = $training->training_days ?? [];
            }
        }
    }

    /**
     * إعادة تعيين أو ضبط بعض الحقول عند تغيير نوع الإجراء (إفاد / مد إفاد)
     */
    public function updatedActionType($value)
    {
        if ($value === 'إفاد') {
            $this->selectedPreviousTrainingId = null;
        }
    }

    /**
     * حساب تاريخ النهاية تلقائياً بناءً على تاريخ البداية وعدد الشهور
     */
    public function updatedStartDate() { $this->calculateEndDate(); }
    public function updatedDurationMonths() { $this->calculateEndDate(); }

    /**
     * دالة حساب تاريخ انتهاء التدريب
     */
    public function calculateEndDate()
    {
        if (!empty($this->start_date) && is_numeric($this->duration_months)) {
            $this->end_date = Carbon::parse($this->start_date)
                ->addMonths((int)$this->duration_months)
                ->format('Y-m-d');
        } else {
            $this->end_date = null;
        }
    }

    /**
     * حفظ البيانات الجديدة
     */
    public function save()
    {
        $this->validate([
            'national_id' => 'required|digits:14',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'profession' => 'required|string',
            'action_type' => 'required|string',
            'training_entity' => 'required|string',
            'duration_months' => 'required|integer|min:1',
            'start_date' => 'required|date',
        ]);

        if ($this->hasActiveTraining && $this->action_type === 'إفاد') {
            session()->flash('error', 'عذراً، موقف الإفاد القديم غير مقفول (الموظف موفد حالياً). لا يمكن عمل إفاد جديد، يمكنك اختيار "مد إفاد" فقط.');
            return;
        }

        // 1. حفظ أو تحديث الموظف الأساسي
        $professional = HealthProfessional::updateOrCreate(
            ['national_id' => $this->national_id],
            [
                'name' => $this->name,
                'phone' => $this->phone,
                'profession' => $this->profession,
                'facility_id' => $this->facility_id,
                'secondment_facility' => $this->secondment_facility,
            ]
        );

        // 2. حفظ بيانات المؤهل
        ProfessionalQualification::updateOrCreate(
            ['health_professional_id' => $professional->id],
            [
                'university' => $this->university,
                'qualification' => $this->qualification,
                'graduation_batch' => $this->graduation_batch,
                'general_grade' => $this->general_grade,
                'subject_grade' => $this->subject_grade,
                'total_marks' => $this->total_marks,
            ]
        );

        // 3. حفظ حركة النيابة
        MedicalMovement::updateOrCreate(
            ['health_professional_id' => $professional->id],
            [
                'specialty' => $this->specialty,
                'movement_date' => $this->movement_date,
            ]
        );

        // 4. حفظ بيانات الإفاد أو مد الإفاد الجديد
        TrainingSecondment::create([
            'health_professional_id' => $professional->id,
            'action_type' => $this->action_type,
            'training_duration_text' => $this->training_duration_text,
            'training_entity' => $this->training_entity,
            'training_department' => $this->training_department,
            'duration_months' => $this->duration_months,
            'training_days' => $this->training_days,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'user_id' => Auth::id(),
        ]);

        session()->flash('success', 'تم تسجيل الإجراء بنجاح.');
        return redirect()->route('postgraduate.training-secondment');
    }

    /**
     * عرض واجهة الـ Blade وتمرير قائمة المنشآت إليها
     */
    public function render()
    {
        return view('livewire.postgraduate.create-training-secondment', [
            'facilities' => Facility::all()
        ])->layout('layouts.app');
    }
}
