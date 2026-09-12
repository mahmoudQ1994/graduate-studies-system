<?php
namespace App\Livewire\Postgraduate;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\PostgraduateRegistration;
use App\Models\HealthProfessional;
use App\Models\Facility;
use App\Models\ProfessionalQualification;
use App\Models\MedicalMovement;

class PostgraduateEdit extends Component

{

    // المتغيرات العامة لتخزين المعرف والمرشح

    public $candidateId;

    public $registration;
    //متغيرات حركة النيابة في أعلى الكلاس
    public $movement_specialty;
    public $movement_date;
    // متغير التحكم في التبويبات (الأخطاء أو التنقل بين خطوات النموذج)
    public $activeTab = 1;
    // --- 1. حقول الهوية والكادر الطبي (جدول health_professionals) ---
    public $national_id;
    public $name;
    public $phone;
    public $profession = 'طبيب بشري';
    public $facility_id;

    public $secondment_facility; // جهة الانتداب
    // --- 2. حقول المؤهل والتخرج (جدول professional_qualifications) ---
    public $university;
    public $qualification;
    public $graduation_batch;

    public $general_grade;

    public $total_marks;

    public $subject_grade;



    // --- 3. حقول التخصص والقيد السابق (جدول postgraduate_registrations) ---

    public $required_degree;

    public $required_specialty;

    public $required_university;

    public $sponsorship_type; // نوع الترشيح (مثل: وزاري)

    public $application_date;

    public $prior_registration_status;

    public $prior_registration_study;

    public $prior_registration_year;

    public $cancellation_reason;

    public $master_degree_date;
    public $degree_grade; // بدلاً من master_degree_grade ليتطابق مع قاعدة البيانات



    /**

     * دالة التحميل (Mount): تعمل عند فتح صفحة التعديل لجلب البيانات القديمة وعرضها في النماذج

     */

    public function mount($id)

    {

        $this->candidateId = $id;



        // جلب سجل التسجيل مع جلب بيانات الكادر الطبي والمؤهل التابع له عبر العلاقات

        $this->registration = PostgraduateRegistration::with(['healthProfessional.qualification'])->findOrFail($id);

        $hp = $this->registration->healthProfessional;

        // تعبئة بيانات الكادر الطبي والهوية

        if ($hp) {
            $this->national_id = $hp->national_id;
            $this->name = $hp->name;
            $this->phone = $hp->phone ?? '';
            $this->profession = $hp->profession ?? 'طبيب بشري';
            $this->facility_id = $hp->facility_id ?? '';
            $this->secondment_facility = $hp->secondment_facility ?? '';
            $this->movement_specialty = $hp->movement_specialty ?? '';
            $this->movement_date = $hp->movement_date ?? '';
            // جلب بيانات المؤهل والتخرج من الجدول المرتبط

            $qual = $hp->qualification;
            if ($qual) {
                $this->university = $qual->university ?? '';
                $this->qualification = $qual->qualification ?? '';
                $this->graduation_batch = $qual->graduation_batch ?? '';
                $this->general_grade = $qual->general_grade ?? '';
                $this->total_marks = $qual->total_marks ?? '';
                $this->subject_grade = $qual->subject_grade ?? '';
            }

            // 2. جلب حركة النيابة باستعلام مباشر وآمن تماماً
            $movement = MedicalMovement::where('health_professional_id', $hp->id)->first();
            if ($movement) {
                $this->movement_specialty = $movement->specialty ?? '';
                // إذا كانت مخزنة كنص عربي مسبقاً، نتركها كما هي، وإذا كانت تاريخاً نحولها لعرضها
                $this->movement_date = $movement->movement_date ?? '';
            }

        }

        // تعبئة بيانات الدراسة المطلوبة والقيد السابق

        $this->required_degree = $this->registration->required_degree;

        $this->required_specialty = $this->registration->required_specialty;

        $this->required_university = $this->registration->required_university;

        $this->sponsorship_type = $this->registration->sponsorship_type;

        $this->application_date = $this->registration->application_date ?? date('Y-m-d');



        $this->prior_registration_status = $this->registration->prior_registration_status ?? 'لا';

        $this->prior_registration_study = $this->registration->prior_registration_study;

        $this->prior_registration_year = $this->registration->prior_registration_year;

        $this->cancellation_reason = $this->registration->cancellation_reason;

        // تعبئة بيانات الماجستير إذا كانت موجودة
        $this->master_degree_date = $this->registration->master_degree_date;
        $this->degree_grade = $this->registration->degree_grade;

    }



    /**

     * دالة لتغيير التبويب النشط (الخطوات) داخل واجهة العرض

     */

    public function setTab($tab)

    {

        $this->activeTab = $tab;

    }
    /**
     * دالة الحفظ والتحديث (Update): تقوم بالتحقق وحفظ التعديلات في الجداول الثلاثة تباعاً
     */
    public function update()
    {
        // التحقق من صحة المدخلات الأساسية قبل الحفظ
        $this->validate([
            'name' => 'required|string|max:255',
            'national_id' => 'required|digits:14',
            'required_degree' => 'required',
            'required_specialty' => 'required|string',
            'required_university' => 'required|string',
            'sponsorship_type' => 'required',
        ]);

        $hp = $this->registration->healthProfessional;
        if ($hp) {
            // 1. تحديث جدول الكادر الطبي (البيانات الأساسية وجهة الانتداب)
            $hp->update([
                'national_id' => $this->national_id,
                'name' => $this->name,
                'phone' => $this->phone,
                'profession' => $this->profession,
                'facility_id' => $this->facility_id,
                'secondment_facility' => $this->secondment_facility,
                'movement_specialty' => $this->movement_specialty,
                'movement_date' => $this->movement_date,
            ]);

            // 2. تحديث أو إنشـاء بيانات المؤهل والتخرج في جدول professional_qualifications
            ProfessionalQualification::updateOrCreate(
                ['health_professional_id' => $hp->id],
                [
                    'university' => $this->university,
                    'qualification' => $this->qualification,
                    'graduation_batch' => $this->graduation_batch,
                    'general_grade' => $this->general_grade,
                    'total_marks' => $this->total_marks,
                    'subject_grade' => $this->subject_grade,
                ]
            );

         // معالجة تاريخ حركة النيابة لتحويله إلى نص عربي مثل (سبتمبر 2025)
            $formattedMovementDate = $this->movement_date;

            if (!empty($this->movement_date)) {
                try {
                    // إذا كان القادم من الحقل بصيغة تاريخ مثل 2025-09 أو 2025-09-01
                    $formattedMovementDate = Carbon::parse($this->movement_date)->locale('ar')->translatedFormat('F Y');
                } catch (\Exception $e) {
                    // إذا كان مخزناً مصلحاً أو نصاً عادياً، اتركه كما هو
                    $formattedMovementDate = $this->movement_date;
                }
            }

            // تحديث أو إنشاء حركة النيابة
            $movement = MedicalMovement::updateOrCreate(
                ['health_professional_id' => $hp->id],
                [
                    'specialty' => $this->movement_specialty,
                    'movement_date' => $formattedMovementDate, // سيحفظ كـ (سبتمبر 2025) مثلاً
                ]
            );

            // وتحديثها في الكادر الطبي أيضاً إذا لزم الأمر
            $hp->update([
                'movement_specialty' => $this->movement_specialty,
                'movement_date' => $formattedMovementDate,
            ]);
        }



        // 3. تحديث جدول التخصص والقيد والتسجيل (postgraduate_registrations)

        $this->registration->update([

            'required_degree' => $this->required_degree,

            'required_specialty' => $this->required_specialty,

            'required_university' => $this->required_university,

            'sponsorship_type' => $this->sponsorship_type,

            'prior_registration_status' => $this->prior_registration_status,

            'prior_registration_study' => $this->prior_registration_study,

            'prior_registration_year' => $this->prior_registration_year,

            'cancellation_reason' => $this->cancellation_reason,

            'master_degree_date' => $this->master_degree_date,
            'degree_grade' => $this->degree_grade,

        ]);



        // إرسال رسالة نجاح وإعادة التوجيه إلى صفحة القائمة الرئيسية

        session()->flash('success', 'تم تعديل بيانات المرشح بنجاح.');

        return redirect()->route('postgraduate.candidates-list');

    }



    /**

     * دالة العرض (Render): تعرض ملف الابليد وتمرر له قائمة الجهات (Facilities)

     */

    public function render()

    {

        return view('livewire.postgraduate.postgraduate-edit', [

            'facilities' => Facility::all(),

        ])->layout('layouts.app', ['title' => 'تعديل بيانات مرشح دراسات عليا']);

    }

}
