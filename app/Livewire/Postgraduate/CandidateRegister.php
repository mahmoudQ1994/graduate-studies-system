<?php

namespace App\Livewire\Postgraduate;

use Livewire\Component;
use App\Models\HealthProfessional;
use App\Models\ProfessionalQualification;
use App\Models\MedicalMovement;
use App\Models\PostgraduateRegistration;
use App\Models\Facility;
use Carbon\Carbon;

class CandidateRegister extends Component
{
    public int $activeTab = 1;

    // 1. البيانات الأساسية
    public string $national_id = '';
    public string $name = '';
    public string $phone = '';
    public string $profession = 'طبيب بشري';
    public $facility_id = null; // جهة العمل الأصلية
    public string $secondment_facility = ''; // جهة الانتداب / النيابة / الإعارة

    // حالة الفحص والموقف السابق
    public $existingStudent = null;
    public $activeRegistration = null;
    public bool $canRegisterNew = true;
    public string $doctorateEligibilityError = '';

    // 2. بيانات المؤهل والتخرج
    public string $university = '';
    public string $faculty = '';
    public string $graduation_batch = '';
    public string $general_grade = '';
    public string $subject_grade = '';
    public string $total_marks = '';

    // بيانات حركة النيابة
    public string $movement_specialty = '';
    public string $movement_date = '';

    // 3. بيانات الدراسة المطلوبة والترشيح
    public string $required_degree = 'ماجستير';
    public string $required_specialty = '';
    public string $required_university = '';
    public string $sponsorship_type = 'وزاري'; // نوع الترشيح (وزاري / على النفقة الخاصة)

    // بيانات القيد السابق
    public string $prior_registration_status = 'لا';
    public string $prior_study_outcome = 'تم الحصول عليها';
    public string $prior_registration_study = '';
    public string $prior_registration_year = '';
    public string $prior_degree_date = '';
    public string $cancellation_reason = '';
    public bool $isPriorAutoFilled = false;

    public string $application_date = '';

    public function mount()
    {
        $this->application_date = Carbon::now()->format('Y-m-d');
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    protected function rules()
    {
        return [
            'national_id' => 'required|digits:14',
            'name' => 'required|string|min:3',
            'profession' => 'required',
            'required_degree' => 'required',
            'required_specialty' => 'required',
            'required_university' => 'required',
            'sponsorship_type' => 'required|in:وزاري,على النفقة الخاصة',
        ];
    }

    public function updatedNationalId($value): void
    {
        $value = trim($value);
        if (strlen($value) === 14) {
            $this->existingStudent = HealthProfessional::with(['qualification', 'movement', 'postgraduateRegistrations'])
                ->where('national_id', $value)
                ->first();

            if ($this->existingStudent) {
                $this->name = $this->existingStudent->name;
                $this->phone = $this->existingStudent->phone ?? '';
                $this->profession = $this->existingStudent->profession ?? 'طبيب بشري';
                $this->facility_id = $this->existingStudent->facility_id;
                $this->secondment_facility = $this->existingStudent->secondment_facility ?? '';

                if ($this->existingStudent->qualification) {
                    $this->university = $this->existingStudent->qualification->university ?? '';
                    $this->faculty = $this->existingStudent->qualification->faculty ?? '';
                    $this->graduation_batch = $this->existingStudent->qualification->graduation_batch ?? '';
                    $this->general_grade = $this->existingStudent->qualification->general_grade ?? '';
                    $this->subject_grade = $this->existingStudent->qualification->subject_grade ?? '';
                    $this->total_marks = $this->existingStudent->qualification->total_marks ?? '';
                }

                if ($this->existingStudent->movement) {
                    $this->movement_specialty = $this->existingStudent->movement->specialty ?? '';
                    $this->movement_date = $this->existingStudent->movement->movement_date ?? '';
                }

                $lastRegistration = $this->existingStudent->postgraduateRegistrations()->latest()->first();

                if ($lastRegistration) {
                    $this->activeRegistration = $lastRegistration;
                    $this->canRegisterNew = !in_array($lastRegistration->study_status, ['مستمر', 'قيد الدراسة', 'مفتوح']);

                    $this->prior_registration_status = 'نعم';
                    $this->prior_registration_study = $lastRegistration->required_degree . ' - ' . $lastRegistration->required_specialty;
                    $this->prior_registration_year = $lastRegistration->registration_date ?? '';

                    if ($lastRegistration->study_status === 'حاصل على الدرجة') {
                        $this->prior_study_outcome = 'تم الحصول عليها';
                        $this->prior_degree_date = $lastRegistration->degree_grant_date ?? '';
                        $this->cancellation_reason = '';
                    } else {
                        $this->prior_study_outcome = 'اعتذر أو تم الإلغاء';
                        $this->cancellation_reason = $lastRegistration->cancellation_reason ?? $lastRegistration->study_status;
                        $this->prior_degree_date = '';
                    }

                    $this->isPriorAutoFilled = true;
                } else {
                    $this->resetPriorFields();
                }
            } else {
                $this->resetPriorFields();
                $this->canRegisterNew = true;
            }
        }
        $this->checkDoctorateEligibility();
    }

    private function resetPriorFields()
    {
        $this->activeRegistration = null;
        $this->prior_registration_status = 'لا';
        $this->prior_study_outcome = 'تم الحصول عليها';
        $this->prior_registration_study = '';
        $this->prior_registration_year = '';
        $this->prior_degree_date = '';
        $this->cancellation_reason = '';
        $this->isPriorAutoFilled = false;
    }

    public function updatedRequiredDegree()
    {
        $this->checkDoctorateEligibility();
    }

    public function checkDoctorateEligibility()
    {
        $this->doctorateEligibilityError = '';

        if ($this->required_degree === 'دكتوراة') {
            $hasMaster = false;

            if ($this->existingStudent) {
                $hasMaster = $this->existingStudent->postgraduateRegistrations()
                    ->where('required_degree', 'ماجستير')
                    ->where('study_status', 'حاصل على الدرجة')
                    ->exists();
            }

            if (!$hasMaster && $this->prior_registration_status === 'نعم' && $this->prior_study_outcome === 'تم الحصول عليها') {
                if (str_contains($this->prior_registration_study, 'ماجستير')) {
                    $hasMaster = true;
                }
            }

            if (!$hasMaster) {
                $this->doctorateEligibilityError = 'شروط الترشح للدكتوراة: يجب أن يكون المرشح حاصلاً على درجة الماجستير أولاً!';
            }
        }
    }

    public function save()
    {
        if (!$this->canRegisterNew) {
            session()->flash('error', 'لا يمكن تسجيل ترشيح جديد لطبيب موقفه الدراسي السابق ما زال مفتوحاً!');
            return;
        }

        $this->checkDoctorateEligibility();
        if ($this->doctorateEligibilityError) {
            session()->flash('error', $this->doctorateEligibilityError);
            return;
        }

        $this->validate();

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

        ProfessionalQualification::updateOrCreate(
            ['health_professional_id' => $professional->id],
            [
                'university' => $this->university,
                'faculty' => $this->faculty,
                'graduation_batch' => $this->graduation_batch,
                'general_grade' => $this->general_grade,
                'subject_grade' => in_array($this->profession, ['طبيب بشري', 'طبيب أسنان']) ? $this->subject_grade : null,
                'total_marks' => $this->total_marks,
            ]
        );

        if (in_array($this->profession, ['طبيب بشري', 'طبيب أسنان'])) {
            MedicalMovement::updateOrCreate(
                ['health_professional_id' => $professional->id],
                [
                    'specialty' => $this->movement_specialty,
                    'movement_date' => $this->movement_date,
                ]
            );
        }

        PostgraduateRegistration::create([
            'health_professional_id' => $professional->id,
            'required_degree' => $this->required_degree,
            'required_specialty' => $this->required_specialty,
            'required_university' => $this->required_university,
            'sponsorship_type' => $this->sponsorship_type,
            'prior_registration_status' => $this->prior_registration_status,
            'prior_registration_study' => $this->prior_registration_status === 'نعم' ? $this->prior_registration_study : null,
            'prior_registration_year' => $this->prior_registration_status === 'نعم' ? $this->prior_registration_year : null,
            'nominated_degree_date' => ($this->prior_registration_status === 'نعم' && $this->prior_study_outcome === 'تم الحصول عليها') ? $this->prior_degree_date : null,
            'cancellation_reason' => ($this->prior_registration_status === 'نعم' && $this->prior_study_outcome !== 'تم الحصول عليها') ? $this->cancellation_reason : null,
            'application_date' => $this->application_date,
            'study_status' => 'مستمر',
        ]);

        session()->flash('success', 'تم تسجيل طلب الترشيح بنجاح.');
        return redirect()->route('postgraduate.search');
    }

    public function render()
    {
        return view('livewire.postgraduate.candidate-register', [
            'facilities' => Facility::where('is_active', true)->get(),
        ])->layout('layouts.app');
    }
}
