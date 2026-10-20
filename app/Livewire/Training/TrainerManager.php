<?php

namespace App\Livewire\Training;

use App\Models\Training\Trainer;
use App\Models\Training\TrainerCourse;
use App\Models\JobGrade;
use App\Models\JobStatus;
use App\Models\TechnicalSpecialization;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Specialty;
use App\Models\University;

class TrainerManager extends Component
{
    use WithFileUploads;

    // الخصائص الأساسية للمدرب
    public $trainer_type = 'trainer';
    public $name_ar, $name_en, $national_id;
    public $birth_date, $gender;
    public $phone, $email;

    // المؤهلات والوظيفة
    public $academic_qualification, $qualification_year, $university_or_institute;
    public $current_job, $workplace, $job_grade_id, $workplace_governorate, $job_status_id;

    // التخصص الفني والإداري والمالي
    public $specialization_id;
    public $postgraduate_specialization, $postgraduate_specialization_year;
    public $specialization_experience_years = 0;
    public $administrative_postgraduates, $english_proficiency_level;
    public $capacity_reward_amount = 0.00;

    // برنامج إعداد المدربين (TOT)
    public $has_tot_certificate = false;
    public $tot_provider, $tot_date;

    // المرفقات
    public $national_id_front, $national_id_back, $job_status_certificate, $tot_certificate;

    // دورات المدرب (صفوف ديناميكية)
    public $courses = [];

    public function mount()
    {
        // إضافة صف افتراضي أولي للدورات
        $this->addCourseRow();
    }

    // استنباط تاريخ الميلاد والنوع تلقائياً عند اكتمال الرقم القومي (14 رقم)
    public function updatedNationalId($value)
    {
        if (strlen($value) === 14 && is_numeric($value)) {
            $centuryCode = substr($value, 0, 1); // 2 تعني القرن العشرين (1900)، 3 تعني القرن الحادي والعشرين (2000)
            $yearDigits  = substr($value, 1, 2);
            $monthDigits = substr($value, 3, 2);
            $dayDigits   = substr($value, 5, 2);

            $century = ($centuryCode == '3') ? '20' : '19';
            $fullYear = $century . $yearDigits;

            // التحقق من صحة التاريخ
            if (checkdate((int)$monthDigits, (int)$dayDigits, (int)$fullYear)) {
                $this->birth_date = "{$fullYear}-{$monthDigits}-{$dayDigits}";
            }

            // استنباط النوع (الرقم قبل الأخير: فردي = ذكر، زوجي = أنثى)
            $genderDigit = (int)substr($value, 12, 1);
            $this->gender = ($genderDigit % 2 !== 0) ? 'male' : 'female';
        }
    }

    // تحديث مكافأة السعة تلقائياً عند اختيار الدرجة الوظيفية (يمكن ربطها بقيمة معينة من جدول الدرجات)
    public function updatedJobGradeId($value)
    {
        // مثال: جلب المكافأة المقابلة للدرجة الوظيفية من الجدول المساعد إن وجدت
        $grade = JobGrade::find($value);
        if ($grade && isset($grade->default_reward)) {
            $this->capacity_reward_amount = $grade->default_reward;
        }
    }

    public function addCourseRow()
    {
        $this->courses[] = [
            'course_type' => 'obtained', // obtained, offered, external_grant
            'course_name' => '',
            'acquisition_date' => '',
            'issuer_entity' => '',
        ];
    }

    public function removeCourseRow($index)
    {
        unset($this->courses[$index]);
        $this->courses = array_values($this->courses);
    }

    public function save()
    {
        $this->validate([
            'name_ar' => 'required|string|max:255',
            'national_id' => 'required|digits:14|unique:trainers,national_id',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'job_grade_id' => 'nullable|exists:job_grades,id',
        ]);

        // رفع المرفقات إن وجدت
        $idFrontPath = $this->national_id_front ? $this->national_id_front->store('trainers/ids', 'public') : null;
        $idBackPath = $this->national_id_back ? $this->national_id_back->store('trainers/ids', 'public') : null;
        $jobCertPath = $this->job_status_certificate ? $this->job_status_certificate->store('trainers/certificates', 'public') : null;
        $totCertPath = $this->tot_certificate ? $this->tot_certificate->store('trainers/tot', 'public') : null;

        // حفظ بيانات المدرب الرئيسية
        $trainer = Trainer::create([
            'trainer_type' => $this->trainer_type,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'national_id' => $this->national_id,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'email' => $this->email,
            'academic_qualification' => $this->academic_qualification,
            'qualification_year' => $this->qualification_year,
            'university_or_institute' => $this->university_or_institute,
            'current_job' => $this->current_job,
            'workplace' => $this->workplace,
            'job_grade_id' => $this->job_grade_id,
            'workplace_governorate' => $this->workplace_governorate,
            'job_status_id' => $this->job_status_id,
            'specialization_id' => $this->specialization_id,
            'postgraduate_specialization' => $this->postgraduate_specialization,
            'postgraduate_specialization_year' => $this->postgraduate_specialization_year,
            'specialization_experience_years' => $this->specialization_experience_years,
            'administrative_postgraduates' => $this->administrative_postgraduates,
            'english_proficiency_level' => $this->english_proficiency_level,
            'capacity_reward_amount' => $this->capacity_reward_amount,
            'has_tot_certificate' => $this->has_tot_certificate,
            'tot_provider' => $this->tot_provider,
            'tot_date' => $this->tot_date,
            'national_id_front_path' => $idFrontPath,
            'national_id_back_path' => $idBackPath,
            'job_status_certificate_path' => $jobCertPath,
            'tot_certificate_path' => $totCertPath,
        ]);

        // حفظ الدورات والدورات التدريبية التابعة للمدرب
        foreach ($this->courses as $course) {
            if (!empty($course['course_name'])) {
                TrainerCourse::create([
                    'trainer_id' => $trainer->id,
                    'course_type' => $course['course_type'],
                    'course_name' => $course['course_name'],
                    'acquisition_date' => $course['acquisition_date'] ?: null,
                    'issuer_entity' => $course['issuer_entity'],
                ]);
            }
        }

        session()->flash('success', 'تم حفظ بيانات المدرب بنجاح!');
        return redirect()->route('training.trainers'); // أو إعادة توجيه حسب المسار لديك
    }

    public function render()
    {
        return view('livewire.training.trainer-manager', [
            'jobGrades' => JobGrade::all(),
            'jobStatuses' => JobStatus::all(),
            'specializations' => TechnicalSpecialization::all(),
            'academicSpecialties' => Specialty::where('is_active', 1)->get(),
            'universities' => University::where('is_active', 1)->get(),
        ])->layout('layouts.app');
    }
}

