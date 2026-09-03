<?php

namespace App\Livewire\Postgraduate;

use Livewire\Component;
use App\Models\HealthProfessional;
use App\Models\PostgraduateRegistration;
use App\Models\ProfessionalQualification;
use App\Models\MedicalMovement;
use App\Models\Facility;

class CandidateRegister extends Component
{
    public $activeTab = 1;

    // بيانات الهوية والأساسية
    public $national_id;
    public $name;
    public $phone;
    public $profession = 'طبيب بشري';
    public $facility_id;
    public $secondment_facility;

    // المؤهلات وحركة النيابة
    public $university;
    public $faculty;
    public $graduation_batch;
    public $general_grade;
    public $total_marks;
    public $subject_grade;
    public $movement_specialty;
    public $movement_date;

    // الدراسة المطلوبة والترشيح
    public $required_degree = 'دبلوم';
    public $required_specialty;
    public $required_university;
    public $sponsorship_type = 'وزاري';
    public $application_date;

    // متغيرات القيد السابق والتحقق
    public $prior_registration_status = 'لا';
    public $prior_study_outcome = 'تم الحصول عليها';
    public $prior_registration_study;
    public $prior_registration_year;
    public $prior_degree_date;
    public $cancellation_reason;
    public $isPriorAutoFilled = false;

    // متغيرات التحكم والحظر
    public $canRegisterNew = true;
    public $activeRegistration = null;
    public $doctorateEligibilityError = null;

    public $master_degree_date;
    public $degree_grade;
    public $isMasterAutoFilled = false;

    // دالة تهيئة المكون
    public function mount()
    {
        $this->application_date = date('Y-m-d');
        $this->checkDoctorateEligibility();
    }

    // دالة التنقل بين علامات التبويب (Tabs)
    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    // دالة التحقق اللحظي عند إدخال أو تغيير الرقم القومي وجلب البيانات السابقة
    public function updatedNationalId($value)
    {
        $value = trim($value);

        if (strlen($value) !== 14) {
            $this->resetCandidateData();
            $this->checkDoctorateEligibility();
            return;
        }

        $professional = HealthProfessional::where('national_id', $value)->first();

        if ($professional) {
            $this->name = $professional->name ?? '';
            $this->phone = $professional->phone ?? '';
            $this->profession = $professional->profession ?? 'طبيب بشري';
            $this->facility_id = $professional->facility_id ?? '';
            $this->secondment_facility = $professional->secondment_facility ?? '';

            // 1. جلب بيانات المؤهل التخصصي السابق إن وجدت لعرضها في الشاشة
            $qualification = ProfessionalQualification::where('health_professional_id', $professional->id)->first();
            if ($qualification) {
                $this->university = $qualification->university;
                $this->faculty = $qualification->faculty;
                $this->graduation_batch = $qualification->graduation_batch;
                $this->general_grade = $qualification->general_grade;
                $this->total_marks = $qualification->total_marks;
                $this->subject_grade = $qualification->subject_grade;
            }

            // 2. جلب بيانات حركة النيابة السابقة إن وجدت لعرضها في الشاشة
            $movement = MedicalMovement::where('health_professional_id', $professional->id)->first();
            if ($movement) {
                $this->movement_specialty = $movement->specialty;
                $this->movement_date = $movement->movement_date;
            }

            // 3. جلب آخر تسجيل دراسات عليا
            $latestReg = PostgraduateRegistration::where('health_professional_id', $professional->id)
                ->orderBy('id', 'desc')
                ->first();

            if ($latestReg) {
                $this->activeRegistration = $latestReg;

                $openStatuses = ['جاري فحص الطلب', 'مستمر', 'قيد الدراسة', 'ساري'];

                if (in_array($latestReg->study_status, $openStatuses) || in_array($latestReg->nominated_degree_status, $openStatuses)) {
                    $this->canRegisterNew = false;
                } else {
                    $this->canRegisterNew = true;

                    $this->prior_registration_status = 'نعم';
                    $this->isPriorAutoFilled = true;
                    $this->prior_registration_study = $latestReg->required_degree . ' - ' . $latestReg->required_specialty;
                    $this->prior_registration_year = date('Y', strtotime($latestReg->registration_date ?? $latestReg->created_at));

                    if ($latestReg->study_status === 'حصل على الدرجة' || $latestReg->nominated_degree_status === 'حصل على الدرجة') {
                        $this->prior_study_outcome = 'تم الحصول عليها';
                        $this->prior_degree_date = $latestReg->nominated_degree_date ?? $latestReg->degree_date ?? null;
                    } else {
                        $this->prior_study_outcome = 'اعتذر أو تم الإلغاء';
                        $this->cancellation_reason = $latestReg->cancellation_reason ?? 'اعتذار أو إلغاء سابق';
                    }
                }
            } else {
                $this->resetPriorData();
            }
        } else {
            $this->resetCandidateData();
        }

        $this->checkDoctorateEligibility();
    }

    public function updatedRequiredDegree($value)
    {
        $this->checkDoctorateEligibility();
    }

    public function updatedMasterDegreeDate($value)
    {
        $this->checkDoctorateEligibility();
    }

    public function updatedDegreeGrade($value)
    {
        $this->checkDoctorateEligibility();
    }

    public function checkDoctorateEligibility()
    {
        $this->doctorateEligibilityError = null;

        if ($this->required_degree === 'دكتوراة') {
            $hasMaster = false;

            if (!empty($this->national_id) && strlen($this->national_id) === 14) {
                $professional = HealthProfessional::where('national_id', $this->national_id)->first();

                if ($professional) {
                    $masterRecord = PostgraduateRegistration::where('health_professional_id', $professional->id)
                        ->where('required_degree', 'ماجستير')
                        ->where(function($q) {
                            $q->where('study_status', 'حصل على الدرجة')
                              ->orWhere('nominated_degree_status', 'حصل على الدرجة');
                        })->latest()->first();

                    if ($masterRecord) {
                        $hasMaster = true;
                        $this->isMasterAutoFilled = true;
                        if (empty($this->master_degree_date)) {
                            $this->master_degree_date = $masterRecord->nominated_degree_date ?? $masterRecord->degree_date ?? '';
                        }
                        if (empty($this->degree_grade)) {
                            $this->degree_grade = $masterRecord->general_grade ?? $masterRecord->degree_grade ?? '';
                        }
                    }
                }
            }

            if (!$hasMaster) {
                if (!empty($this->master_degree_date) && !empty($this->degree_grade)) {
                    $hasMaster = true;
                } else {
                    $this->isMasterAutoFilled = false;
                    $this->doctorateEligibilityError = 'الترشيح للحصول على درجة الدكتوراه يستوجب الحصول على درجة الماجستير...';
                }
            }
        } else {
            $this->isMasterAutoFilled = false;
        }
    }

    private function resetCandidateData()
    {
        $this->name = '';
        $this->phone = '';
        $this->university = '';
        $this->faculty = '';
        $this->graduation_batch = '';
        $this->general_grade = '';
        $this->total_marks = '';
        $this->subject_grade = '';
        $this->movement_specialty = '';
        $this->movement_date = '';
        $this->activeRegistration = null;
        $this->canRegisterNew = true;
        $this->resetPriorData();
    }

    private function resetPriorData()
    {
        $this->isPriorAutoFilled = false;
        $this->prior_registration_status = 'لا';
        $this->prior_study_outcome = 'تم الحصول عليها';
        $this->prior_registration_study = '';
        $this->prior_registration_year = '';
        $this->prior_degree_date = '';
        $this->cancellation_reason = '';
    }

    public function save()
    {
        $this->validate([
            'national_id' => 'required|digits:14',
            'name' => 'required|string|max:255',
        ]);

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

        // حفظ المؤهل التخصصي فقط إذا تم إدخال بيانات تخصه
        if (!empty($this->university) || !empty($this->faculty)) {
            ProfessionalQualification::updateOrCreate(
                ['health_professional_id' => $professional->id],
                [
                    'university' => $this->university,
                    'faculty' => $this->faculty,
                    'graduation_batch' => $this->graduation_batch,
                    'general_grade' => $this->general_grade,
                    'subject_grade' => $this->subject_grade,
                    'total_marks' => $this->total_marks,
                ]
            );
        }

        // حفظ حركة النيابة فقط للأطباء وإذا وُجدت بيانات
        if (in_array($this->profession, ['طبيب بشري', 'طبيب أسنان'])) {
            if (!empty($this->movement_specialty) || !empty($this->movement_date)) {
                MedicalMovement::updateOrCreate(
                    ['health_professional_id' => $professional->id],
                    [
                        'specialty' => $this->movement_specialty,
                        'movement_date' => $this->movement_date,
                    ]
                );
            }
        }

        PostgraduateRegistration::create([
            'health_professional_id' => $professional->id,
            'required_degree' => $this->required_degree,
            'required_specialty' => $this->required_specialty,
            'required_university' => $this->required_university,
            'sponsorship_type' => $this->sponsorship_type,
            'application_date' => $this->application_date ?? now(),
            'study_status' => 'جاري فحص الطلب',
            'nominated_degree_status' => 'جاري فحص الطلب',
            'master_degree_date' => $this->master_degree_date,
            'degree_grade' => $this->degree_grade,
        ]);

        session()->flash('success', 'تم حفظ تسجيل الترشيح للدراسات العليا بنجاح!');

        return redirect()->route('livewire.postgraduate.candidate-register');
    }

    public function render()
    {
        return view('livewire.postgraduate.candidate-register', [
            'facilities' => Facility::all(),
        ])->layout('layouts.app');
    }
}
