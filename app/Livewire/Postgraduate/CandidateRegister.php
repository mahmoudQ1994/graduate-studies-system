<?php

namespace App\Livewire\Postgraduate;

use Livewire\Component;
use App\Models\HealthProfessional;
use App\Models\PostgraduateRegistration;
use App\Models\ProfessionalQualification;
use App\Models\MedicalMovement;
use App\Models\Facility;
use App\Models\User;

class CandidateRegister extends Component
{

    /**
     * تعريف علاقة المستخدم (User)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
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
    public $qualification;
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

    /**
     * دالة تهيئة المكون (Mount)
     */
    public function mount()
    {
        $this->application_date = date('Y-m-d');
        $this->checkDoctorateEligibility();
    }

    /**
     * دالة التنقل بين علامات التبويب (Tabs)
     */
    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    /**
     * دالة التحقق اللحظي عند إدخال أو تغيير الرقم القومي وجلب البيانات السابقة
     */
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
                $this->qualification = $qualification->qualification;
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

                        // حفظ قيمة study_status القديمة (التي أصبحت 'حصل على الدرجة') في متغير سبب/موقف الإلغاء
                        $this->cancellation_reason = $latestReg->study_status;

                        // جلب بيانات الدبلوم أو المؤهل السابق لتعبئتها تلقائياً للماجستير
                        if ($latestReg->required_degree === 'دبلوم') {
                            $this->isMasterAutoFilled = true;
                            $this->master_degree_date = $latestReg->nominated_degree_date ?? $latestReg->degree_date;
                            $this->degree_grade = $latestReg->general_grade ?? $latestReg->degree_grade;
                        }
                    } else {
                        $this->prior_study_outcome = 'اعتذر أو تم الإلغاء';

                        // حفظ قيمة study_status القديمة في متغير سبب/موقف الإلغاء
                        $this->cancellation_reason = $latestReg->study_status;
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

    /**
     * دالة التحديث عند تغيير نوع الدراسة المطلوبة
     */
    public function updatedRequiredDegree($value)
    {
        $this->checkDoctorateEligibility();
    }

    /**
     * دالة التحديث عند تغيير تاريخ الحصول على الماجستير
     */
    public function updatedMasterDegreeDate($value)
    {
        $this->checkDoctorateEligibility();
    }

    /**
     * دالة التحديث عند تغيير تقدير الدرجة العلمية
     */
    public function updatedDegreeGrade($value)
    {
        $this->checkDoctorateEligibility();
    }

    /**
     * دالة التحقق من أهلية الترشيح للدكتوراه أو الماجستير بناءً على المؤهلات السابقة
     */
    public function checkDoctorateEligibility()
    {
        $this->doctorateEligibilityError = null;

        if ($this->required_degree === 'دكتوراة' || $this->required_degree === 'ماجستير') {
            $targetDegree = ($this->required_degree === 'دكتوراة') ? 'ماجستير' : 'دبلوم';
            $hasPreviousDegree = false;

            if (!empty($this->national_id) && strlen($this->national_id) === 14) {
                $professional = HealthProfessional::where('national_id', $this->national_id)->first();

                if ($professional) {
                    $previousRecord = PostgraduateRegistration::where('health_professional_id', $professional->id)
                        ->where('required_degree', $targetDegree)
                        ->where(function($q) {
                            $q->where('study_status', 'حصل على الدرجة')
                                ->orWhere('nominated_degree_status', 'حصل على الدرجة');
                        })->latest()->first();

                    if ($previousRecord) {
                        $hasPreviousDegree = true;
                        $this->isMasterAutoFilled = true;
                        if (empty($this->master_degree_date)) {
                            $this->master_degree_date = $previousRecord->nominated_degree_date ?? $previousRecord->degree_date ?? '';
                        }
                        if (empty($this->degree_grade)) {
                            $this->degree_grade = $previousRecord->general_grade ?? $previousRecord->degree_grade ?? '';
                        }
                    }
                }
            }

            if (!$hasPreviousDegree && $this->required_degree === 'دكتوراة') {
                if (!empty($this->master_degree_date) && !empty($this->degree_grade)) {
                    $hasPreviousDegree = true;
                } else {
                    $this->isMasterAutoFilled = false;
                    $this->doctorateEligibilityError = 'الترشيح للحصول على درجة الدكتوراه يستوجب الحصول على درجة الماجستير...';
                }
            }
        } else {
            $this->isMasterAutoFilled = false;
        }
    }

    /**
     * دالة إعادة تعيين بيانات المرشح الأساسية
     */
    private function resetCandidateData()
    {
        $this->name = '';
        $this->phone = '';
        $this->university = '';
        $this->qualification = '';
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

    /**
     * دالة إعادة تعيين بيانات القيد السابق
     */
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

    /**
     * دالة حفظ بيانات المرشح والتسجيل الجديد في قاعدة البيانات
     */
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

        if (!empty($this->university) ||  !empty($this->qualification) || !empty($this->graduation_batch) || !empty($this->general_grade) || !empty($this->total_marks) || !empty($this->subject_grade)) {
            ProfessionalQualification::updateOrCreate(
                ['health_professional_id' => $professional->id],
                [
                    'university' => $this->university,
                    'qualification' => $this->qualification,
                    'qualification' => $this->qualification,
                    'graduation_batch' => $this->graduation_batch,
                    'general_grade' => $this->general_grade,
                    'subject_grade' => $this->subject_grade,
                    'total_marks' => $this->total_marks,
                ]
            );
        }

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
            'application_date' => date('Y-m-d'),
            'study_status' => 'جاري فحص الطلب',
            'nominated_degree_status' => 'جاري فحص الطلب',

            // حقول الترشيح السابق التي تم تفعيل حفظها هنا
            'prior_registration_status' => $this->prior_registration_status,
            'prior_registration_study' => $this->prior_registration_study,
            'prior_registration_year' => $this->prior_registration_year,
            'cancellation_reason' => $this->cancellation_reason,

            'master_degree_date' => $this->master_degree_date,
            'degree_grade' => $this->degree_grade,
            // 'user_id' => auth()->id(),
        ]);

        session()->flash('success', 'تم حفظ تسجيل الترشيح للدراسات العليا بنجاح!');

        return redirect()->route('register');
    }

    /**
     * دالة عرض صفحة المكون ورسم الواجهة (Render)
     */
    public function render()
    {
        return view('livewire.postgraduate.candidate-register', [
            'facilities' => Facility::all(),
        ])->layout('layouts.app');
    }
}
