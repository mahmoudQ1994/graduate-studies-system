<?php



namespace App\Livewire\Postgraduate;



use Livewire\Component;

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

    public $faculty;

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

                $this->faculty = $qual->faculty ?? '';

                $this->graduation_batch = $qual->graduation_batch ?? '';

                $this->general_grade = $qual->general_grade ?? '';

                $this->total_marks = $qual->total_marks ?? '';

                $this->subject_grade = $qual->subject_grade ?? '';

            }

            // 2. جلب حركة النيابة باستعلام مباشر وآمن تماماً
            $movement = MedicalMovement::where('health_professional_id', $hp->id)->first();
            if ($movement) {
                $this->movement_specialty = $movement->specialty ?? '';
                $this->movement_date = $movement->movement_date ?? '';
            }

        }



        // جلب بيانات حركة النيابة من الجدول المرتبط medical_movements

        $movement = $hp->medicalMovement; // أو $hp->medicalMovements()->first() حسب تعريف العلاقة

        if ($movement) {

            $this->movement_specialty = $movement->specialty ?? '';

            $this->movement_date = $movement->movement_date ?? '';

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

                    'faculty' => $this->faculty,

                    'graduation_batch' => $this->graduation_batch,

                    'general_grade' => $this->general_grade,

                    'total_marks' => $this->total_marks,

                    'subject_grade' => $this->subject_grade,

                ]

            );

            // 3. تحديث أو إنشاء حركة النيابة بشكل مباشر وآمن
            $movement = MedicalMovement::where('health_professional_id', $hp->id)->first();

            if ($movement) {
                // إذا كان السجل موجوداً، قم بتحديثه
                $movement->update([
                    'specialty' => $this->movement_specialty,
                    'movement_date' => $this->movement_date,
                ]);
            } else {
                // إذا لم يكن موجوداً، قم بإنشاء سجل جديد مرتبط بالطبيب
                MedicalMovement::create([
                    'health_professional_id' => $hp->id,
                    'specialty' => $this->movement_specialty,
                    'movement_date' => $this->movement_date,
                ]);
            }

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
