<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\Trainees\Trainee;
use Illuminate\Support\Facades\DB;
use App\Models\Specialty;
use App\Models\Facility;

class RegisterTraining extends Component
{
    // بيانات البحث والتحقق
    public $national_id = '';
    public $is_existing_trainee = false;
    public $trainee_id = null;

    // بيانات المتدرب
    public $name_ar = '';
    public $name_en = '';
    public $qualification = '';
    public $job_title = '';
    public $department = '';
    public $workplace = '';
    public $email = '';
    public $phone_number = '';

    // اختيار الجدول التدريبي
    public $selected_schedule_id = '';

    // البحث التفاعلي للجهة والوظيفة
    public $facilitySearch = '';
    public $jobTitleSearch = '';

    protected function rules()
    {
        return [
            'national_id' => 'required|string|size:14',
            'name_ar' => 'required|string|max:255',
            'selected_schedule_id' => 'required|exists:training_program_schedules,id',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ];
    }

    protected $messages = [
        'national_id.required' => 'الرقم القومي مطلوب.',
        'national_id.size' => 'الرقم القومي يجب أن يكون 14 رقماً بالضبط.',
        'name_ar.required' => 'الاسم بالعربي مطلوب.',
        'selected_schedule_id.required' => 'يرجى اختيار البرنامج التدريبي المراد التسجيل به.',
    ];

    // البحث الفوري بالرقم القومي بمجرد كتابة 14 رقماً
    public function updatedNationalId($value)
    {
        $value = trim($value);
        if (strlen($value) === 14) {
            $trainee = Trainee::where('national_id', $value)->first();

            if ($trainee) {
                $this->is_existing_trainee = true;
                $this->trainee_id = $trainee->id;
                $this->name_ar = $trainee->name_ar;
                $this->name_en = $trainee->name_en;
                $this->qualification = $trainee->qualification;
                $this->job_title = $trainee->job_title;
                $this->jobTitleSearch = $trainee->job_title;
                $this->department = $trainee->department;
                $this->workplace = $trainee->workplace;
                $this->facilitySearch = $trainee->workplace;
                $this->email = $trainee->email;
                $this->phone_number = $trainee->phone_number;

                session()->flash('info', 'مرحباً بك مجدداً! تم التعرف على بياناتك المسجلة مسبقاً، يمكنك مراجعتها واختيار البرنامج التدريبي.');
            } else {
                $this->is_existing_trainee = false;
                $this->trainee_id = null;
            }
        }
    }

    public function getMatchingFacilitiesProperty()
    {
        if (strlen(trim($this->facilitySearch)) < 1) return collect();
        return Facility::where('is_active', 1)->where('name', 'like', '%' . $this->facilitySearch . '%')->limit(5)->get();
    }

    public function selectFacility($name)
    {
        $this->workplace = $name;
        $this->facilitySearch = $name;
    }

    public function getMatchingJobTitlesProperty()
    {
        if (strlen(trim($this->jobTitleSearch)) < 1) return collect();
        return DB::table('job_titles')->where('is_active', 1)->where('name', 'like', '%' . $this->jobTitleSearch . '%')->limit(6)->get();
    }

    public function selectJobTitle($name)
    {
        $this->job_title = $name;
        $this->jobTitleSearch = $name;
    }

    // حفظ وتأكيد التسجيل في البرنامج التدريبي
    public function submitRegistration()
    {
        $this->validate();

        // 1. التحقق من السعة القصوى للجدول التدريبي (مع استبعاد المرفوضين/المستبعدين)
        $schedule = DB::table('training_program_schedules')->where('id', $this->selected_schedule_id)->first();

        if (!$schedule) {
            session()->flash('error', 'البرنامج التدريبي المحدد غير موجود.');
            return;
        }

        // نحتسب فقط التسجيلات الفعالة غير المستبعدة
        $enrolledCount = DB::table('trainee_enrollments')
            ->where('training_program_schedule_id', $this->selected_schedule_id)
            ->where(function($q) {
                $q->where('status', '!=', 'rejected')
                  ->orWhereNull('status');
            })
            ->count();

        if ($enrolledCount >= $schedule->max_capacity) {
            session()->flash('error', 'عذراً، تم اكتمال العدد الأقصى المسموح به لهذا البرنامج التدريبي (' . $schedule->max_capacity . ' متدرب). يرجى اختيار موعد أو برنامج آخر.');
            return;
        }

        DB::beginTransaction();
        try {
            // 2. حفظ أو تحديث بيانات المتدرب الأساسية
            if ($this->is_existing_trainee && $this->trainee_id) {
                $trainee = Trainee::findOrFail($this->trainee_id);
                $trainee->update([
                    'name_ar' => $this->name_ar,
                    'name_en' => $this->name_en,
                    'qualification' => $this->qualification,
                    'job_title' => $this->job_title,
                    'department' => $this->department,
                    'workplace' => $this->workplace,
                    'email' => $this->email,
                    'phone_number' => $this->phone_number,
                ]);
            } else {
                $trainee = Trainee::create([
                    'name_ar' => $this->name_ar,
                    'name_en' => $this->name_en,
                    'national_id' => $this->national_id,
                    'qualification' => $this->qualification,
                    'job_title' => $this->job_title,
                    'department' => $this->department,
                    'workplace' => $this->workplace,
                    'email' => $this->email,
                    'phone_number' => $this->phone_number,
                    'status' => 'active',
                ]);
                $this->trainee_id = $trainee->id;
            }

            // 3. التحقق من عدم التسجيل المسبق لنفس المتدرب في نفس البرنامج
            $alreadyEnrolled = DB::table('trainee_enrollments')
                ->where('trainee_id', $this->trainee_id)
                ->where('training_program_schedule_id', $this->selected_schedule_id)
                ->exists();

            if ($alreadyEnrolled) {
                session()->flash('error', 'أنت مسجل بالفعل في هذا البرنامج التدريبي مسبقاً!');
                DB::rollBack();
                return;
            }

            // 4. إنشاء سجل التسجيل في جدول trainee_enrollments بحالة "قيد المراجعة" افتراضياً
            DB::table('trainee_enrollments')->insert([
                'trainee_id' => $this->trainee_id,
                'training_program_schedule_id' => $this->selected_schedule_id,
                'status' => 'pending', // الحالة الافتراضية: جاري الاعتماد
                'attendance_status' => 'pending',
                'job_title' => $this->job_title,
                'department' => $this->department,
                'workplace' => $this->workplace,
                'enrolled_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            session()->flash('success', 'تم تسجيلك بنجاح! طلبك الآن قيد المراجعة من قبل إدارة التدريب.');

            // إعادة تعيين الحقول
            $this->reset();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'حدث خطأ أثناء حفظ التسجيل: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // جلب الجداول التدريبية مع حساب المقاعد المشغولة (باستبعاد المرفوضين فقط)
        $schedules = DB::table('training_program_schedules as tps')
            ->join('training_programs as tp', 'tp.id', '=', 'tps.training_program_id')
            ->select(
                'tps.id',
                'tps.start_date',
                'tps.end_date',
                'tps.time_slot',
                'tps.max_capacity',
                'tp.name_ar as program_name',
                DB::raw('(SELECT COUNT(*) FROM trainee_enrollments WHERE training_program_schedule_id = tps.id AND (status != "rejected" OR status IS NULL)) as enrolled_count')
            )
            ->get();

        $specialties = Specialty::where('is_active', 1)->get();

        return view('livewire.public.register-training', [
            'schedules' => $schedules,
            'specialties' => $specialties,
        ])->layout('layouts.guest');
    }
}
