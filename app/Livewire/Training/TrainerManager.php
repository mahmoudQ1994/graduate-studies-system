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

    public $trainerId = null; // 1. متغير لتحديد وضع التعديل (إن وجد)
    public $existingTrainer = null;
    public $nationalIdMessage = null;

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

    // 2. تحديث دالة mount لدعم استقبال بيانات المدرب عند الضغط على التعديل
    public function mount($trainer = null)
    {
        if ($trainer) {
            $trainerModel = is_numeric($trainer) ? Trainer::find($trainer) : $trainer;

            if ($trainerModel) {
                $this->trainerId = $trainerModel->id;
                $this->trainer_type = $trainerModel->trainer_type;
                $this->name_ar = $trainerModel->name_ar;
                $this->name_en = $trainerModel->name_en;
                $this->national_id = $trainerModel->national_id;
                $this->birth_date = $trainerModel->birth_date;
                $this->gender = $trainerModel->gender;
                $this->phone = $trainerModel->phone;
                $this->email = $trainerModel->email;
                $this->academic_qualification = $trainerModel->academic_qualification;
                $this->qualification_year = $trainerModel->qualification_year;
                $this->university_or_institute = $trainerModel->university_or_institute;
                $this->current_job = $trainerModel->current_job;
                $this->workplace = $trainerModel->workplace;
                $this->job_grade_id = $trainerModel->job_grade_id;
                $this->workplace_governorate = $trainerModel->workplace_governorate;
                $this->job_status_id = $trainerModel->job_status_id;
                $this->specialization_id = $trainerModel->specialization_id;
                $this->postgraduate_specialization = $trainerModel->postgraduate_specialization;
                $this->postgraduate_specialization_year = $trainerModel->postgraduate_specialization_year;
                $this->specialization_experience_years = $trainerModel->specialization_experience_years;
                $this->administrative_postgraduates = $trainerModel->administrative_postgraduates;
                $this->english_proficiency_level = $trainerModel->english_proficiency_level;
                $this->capacity_reward_amount = $trainerModel->capacity_reward_amount;
                $this->has_tot_certificate = $trainerModel->has_tot_certificate;
                $this->tot_provider = $trainerModel->tot_provider;
                $this->tot_date = $trainerModel->tot_date;

                // جلب الدورات الخاصة بالمحاضر وعرضها بالصفوف
                $existingCourses = $trainerModel->courses;
                if ($existingCourses && $existingCourses->count() > 0) {
                    $this->courses = $existingCourses->map(function($c) {
                        return [
                            'course_type' => $c->course_type,
                            'course_name' => $c->course_name,
                            'acquisition_date' => $c->acquisition_date,
                            'issuer_entity' => $c->issuer_entity,
                        ];
                    })->toArray();
                }
            }
        }

        // إذا لم تكن هناك دورات مضافة، نضيف صفاً فارغاً افتراضياً
        if (empty($this->courses)) {
            $this->addCourseRow();
        }
    }

    // استنباط تاريخ الميلاد والنوع تلقائياً عند اكتمال الرقم القومي (14 رقم)
    public function updatedNationalId($value)
    {
        if (strlen($value) === 14 && is_numeric($value)) {
            // البحث عن المدرب بالرقم القومي (مع استثناء المدرب الحالي إذا كنا في وضع التعديل)
            $query = Trainer::where('national_id', $value);
            if ($this->trainerId) {
                $query->where('id', '!=', $this->trainerId);
            }
            $this->existingTrainer = $query->first();

            if ($this->existingTrainer) {
                $statusText = match($this->existingTrainer->status) {
                    'approved' => 'معتمد',
                    'pending' => 'قيد الانتظار للمراجعة',
                    'rejected' => 'مرفوض',
                    default => $this->existingTrainer->status
                };

                $this->nationalIdMessage = "تنبيه: هذا الرقم القومي مسجل مسبقاً للمدرب ({$this->existingTrainer->name_ar}) وموقفه الحالي: [ {$statusText} ].";
            } else {
                $this->nationalIdMessage = null;
                $this->existingTrainer = null;
            }

            // استنباط تاريخ الميلاد والنوع كما هو متبع
            $centuryCode = substr($value, 0, 1);
            $yearDigits  = substr($value, 1, 2);
            $monthDigits = substr($value, 3, 2);
            $dayDigits   = substr($value, 5, 2);

            $century = ($centuryCode == '3') ? '20' : '19';
            $fullYear = $century . $yearDigits;

            if (checkdate((int)$monthDigits, (int)$dayDigits, (int)$fullYear)) {
                $this->birth_date = "{$fullYear}-{$monthDigits}-{$dayDigits}";
            }

            $genderDigit = (int)substr($value, 12, 1);
            $this->gender = ($genderDigit % 2 !== 0) ? 'male' : 'female';
        } else {
            $this->nationalIdMessage = null;
            $this->existingTrainer = null;
        }
    }

    // تحديث مكافأة السعة تلقائياً عند اختيار الدرجة الوظيفية
    public function updatedJobGradeId($value)
    {
        $grade = JobGrade::find($value);
        if ($grade && isset($grade->default_reward)) {
            $this->capacity_reward_amount = $grade->default_reward;
        }
    }

    public function addCourseRow()
    {
        $this->courses[] = [
            'course_type' => 'obtained',
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

    // 3. تعديل دالة save لتتحقق هل هو (إنشاء جديد) أم (تعديل وتحديث)
    public function save()
    {
        // التحقق من تكرار الرقم القومي عند الإضافة أو تغييره لتكرار خطأ
        $duplicateQuery = Trainer::where('national_id', $this->national_id);
        if ($this->trainerId) {
            $duplicateQuery->where('id', '!=', $this->trainerId);
        }
        $duplicateCheck = $duplicateQuery->first();

        if ($duplicateCheck) {
            $statusText = match($duplicateCheck->status) {
                'approved' => 'معتمد',
                'pending' => 'قيد الانتظار',
                'rejected' => 'مرفوض',
                default => $duplicateCheck->status
            };

            session()->flash('error', "عذراً، لا يمكن الحفظ لأن الرقم القومي مسجل مسبقاً للمحاضر ({$duplicateCheck->name_ar}) وموقفه: [ {$statusText} ].");
            return;
        }

        // قواعد التحقق مع استثناء الرقم القومي للمدرب الحالي في وضع التعديل
        $this->validate([
            'name_ar' => 'required|string|max:255',
            'national_id' => 'required|digits:14|unique:trainers,national_id,' . $this->trainerId,
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'job_grade_id' => 'nullable|exists:job_grades,id',
        ]);

        // رفع المرفقات الجديدة إن وجدت
        $idFrontPath = $this->national_id_front ? $this->national_id_front->store('trainers/ids', 'public') : null;
        $idBackPath = $this->national_id_back ? $this->national_id_back->store('trainers/ids', 'public') : null;
        $jobCertPath = $this->job_status_certificate ? $this->job_status_certificate->store('trainers/certificates', 'public') : null;
        $totCertPath = $this->tot_certificate ? $this->tot_certificate->store('trainers/tot', 'public') : null;

        if ($this->trainerId) {
            // === حالة التعديل (Update) ===
            $trainer = Trainer::find($this->trainerId);

            $updateData = [
                'trainer_type' => $this->trainer_type,
                'updated_by' => auth()->id(), // تسجيل من قام بالتعديل لضمان التدقيق
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
            ];

            if ($idFrontPath) $updateData['national_id_front_path'] = $idFrontPath;
            if ($idBackPath) $updateData['national_id_back_path'] = $idBackPath;
            if ($jobCertPath) $updateData['job_status_certificate_path'] = $jobCertPath;
            if ($totCertPath) $updateData['tot_certificate_path'] = $totCertPath;

            $trainer->update($updateData);

            // تحديث الدورات (إزالة القديم وإدراج الجديد)
            $trainer->courses()->delete();
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

            session()->flash('success', 'تم تعديل بيانات المدرب بنجاح!');
        } else {
            // === حالة الإضافة الجديدة (Create) ===
            $trainer = Trainer::create([
                'trainer_type' => $this->trainer_type,
                'status' => 'pending',
                'created_by' => auth()->id(),
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

            session()->flash('success', 'تم حفظ بيانات المدرب بنجاح وأصبحت قيد المراجعة!');
        }

        return redirect()->route('training.trainers.list');
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
