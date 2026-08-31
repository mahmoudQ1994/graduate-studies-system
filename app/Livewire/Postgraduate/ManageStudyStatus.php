<?php

namespace App\Livewire\Postgraduate;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\PostgraduateRegistration;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PostgraduateRegistrationsExport;

class ManageStudyStatus extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // متغيرات البحث والتصفية الجديدة حسب الترتيب المطلوب
    public $search_name = '';
    public $search_national_id = '';
    public $filter_required_degree = '';
    public $filter_study_status = '';
    public $filter_application_date = '';

    // متغيرات النموذج للتعديل
    public $selectedRegId = null;
    public $study_status = 'جاري فحص الطلب';
    public $execution_date;
    public $registration_date;
    public $nominated_degree_date;
    public $nominated_degree_status;
    public $apology_date;
    public $apology_reason;
    public $years_from_registration = '-';

    /**
     * إعادة تعيين ترقيم الصفحات عند تحديث خانة البحث بالاسم.
     */
    public function updatingSearchName() { $this->resetPage(); }

    /**
     * إعادة تعيين ترقيم الصفحات عند تحديث خانة البحث بالرقم القومي.
     */
    public function updatingSearchNationalId() { $this->resetPage(); }

    /**
     * إعادة تعيين ترقيم الصفحات عند تحديث فلتر نوع الدراسة.
     */
    public function updatingFilterRequiredDegree() { $this->resetPage(); }

    /**
     * إعادة تعيين ترقيم الصفحات عند تحديث فلتر موقف الدراسة.
     */
    public function updatingFilterStudyStatus() { $this->resetPage(); }

    /**
     * إعادة تعيين ترقيم الصفحات عند تحديث فلتر تاريخ تسجيل الطلب.
     */
    public function updatingFilterApplicationDate() { $this->resetPage(); }

    /**
     * جلب بيانات السجل المحدد وإعداد متغيرات النموذج لفتح نافذة التعديل مع تنسيق التواريخ.
     */
    public function editStatus($regId)
    {
        $reg = PostgraduateRegistration::findOrFail($regId);
        $this->selectedRegId = $reg->id;
        // إذا كانت مخزنة في القاعدة 'مستمر' ولها تاريخ تنفيذ، نعرضها في الواجهة باسم 'تنفيذ دراسة' ليتوافق مع شروط الـ Blade
            if ($reg->study_status == 'مستمر' && !empty($reg->execution_date)) {
                $this->study_status = 'تنفيذ دراسة';
            } else {
                $this->study_status = $reg->study_status ?? 'مستمر';
            }
        $this->execution_date = $reg->execution_date ? Carbon::parse($reg->execution_date)->format('Y-m-d') : null;
        $this->registration_date = $reg->registration_date ? Carbon::parse($reg->registration_date)->format('Y-m-d') : null;
        $this->nominated_degree_date = $reg->nominated_degree_date ? Carbon::parse($reg->nominated_degree_date)->format('Y-m-d') : null;
        $this->nominated_degree_status = $reg->nominated_degree_status;
        $this->apology_date = $reg->apology_date ? Carbon::parse($reg->apology_date)->format('Y-m-d') : null;
        $this->apology_reason = $reg->apology_reason ?? null;

        $this->calculateStudyDuration();

        $this->dispatch('open-modal', name: 'studyStatusModal');
    }

    /**
     * إعادة حساب مدة الدراسة تلقائياً عند تغيير تاريخ القيد.
     */
    public function updatedRegistrationDate() { $this->calculateStudyDuration(); }

    /**
     * إعادة حساب مدة الدراسة تلقائياً عند تغيير تاريخ الحصول على الدرجة.
     */
    public function updatedNominatedDegreeDate() { $this->calculateStudyDuration(); }

    /**
     * إعادة حساب مدة الدراسة تلقائياً عند تغيير تاريخ الاعتذار.
     */
    public function updatedApologyDate() { $this->calculateStudyDuration(); }

    /**
     * إعادة حساب مدة الدراسة تلقائياً عند تغيير تاريخ التنفيذ.
     */
    public function updatedExecutionDate() { $this->calculateStudyDuration(); }

/**
     * تحديث الموقف وتصفية الحقول المتعارضة تلقائياً عند تغيير موقف الدراسة.
     */
    public function updatedStudyStatus($value)
    {
        if ($value == 'تنفيذ دراسة') {
            // عند اختيار تنفيذ دراسة من القائمة، نسمح بتسجيل تاريخ التنفيذ والقيد
            $this->nominated_degree_status = null;
            $this->nominated_degree_date = null;
            $this->apology_date = null;
            $this->apology_reason = null;
        } elseif ($value == 'اعتذار') {
            $this->nominated_degree_status = 'اعتذر ولم يحصل على الدرجة';
            $this->nominated_degree_date = null;
            $this->execution_date = null;
        } elseif ($value == 'حصل على الدرجة') {
            $this->nominated_degree_status = 'حصل على الدرجة';
            $this->apology_date = null;
            $this->apology_reason = null;
            $this->execution_date = null;
        } else {
            $this->nominated_degree_status = null;
            $this->nominated_degree_date = null;
            $this->apology_date = null;
            $this->apology_reason = null;
            $this->execution_date = null;
        }
        $this->calculateStudyDuration();
    }

    //  * حساب الفترة الزمنية المنقضية (مع استبعاد تاريخ التنفيذ نهائياً).
    //  */
    public function calculateStudyDuration()
    {
        if (!empty($this->registration_date)) {
            try {
                $start = Carbon::parse($this->registration_date);
                $end = null;

                // اعتماد تاريخ الحصول على الدرجة أو الاعتذار فقط كنهاية للمدة، وباقي الحالات تحسب حتى الوقت الحالي
                if ($this->study_status == 'حصل على الدرجة' && !empty($this->nominated_degree_date)) {
                    $end = Carbon::parse($this->nominated_degree_date);
                } elseif ($this->study_status == 'اعتذار' && !empty($this->apology_date)) {
                    $end = Carbon::parse($this->apology_date);
                } else {
                    $end = Carbon::now();
                }

                if ($end && $end->greaterThanOrEqualTo($start)) {
                    $diff = $start->diff($end);
                    $years = $diff->y;
                    $months = $diff->m;
                    $days = $diff->d;

                    $parts = [];

                    if ($years > 0) {
                        if ($years == 1) {
                            $parts[] = 'سنة واحدة';
                        } elseif ($years == 2) {
                            $parts[] = 'سنتان';
                        } elseif ($years > 2 && $years <= 10) {
                            $parts[] = "{$years} سنوات";
                        } else {
                            $parts[] = "{$years} سنة";
                        }
                    }

                    if ($months > 0) {
                        if ($months == 1) {
                            $parts[] = 'شهر واحد';
                        } elseif ($months == 2) {
                            $parts[] = 'شهران';
                        } elseif ($months > 2 && $months <= 10) {
                            $parts[] = "{$months} شهور";
                        } else {
                            $parts[] = "{$months} شهرًا";
                        }
                    }

                    if ($years == 0 && $months == 0) {
                        if ($days > 0) {
                            if ($days == 1) {
                                $parts[] = 'يوم واحد';
                            } elseif ($days == 2) {
                                $parts[] = 'يومان';
                            } elseif ($days > 2 && $days <= 10) {
                                $parts[] = "{$days} أيام";
                            } else {
                                $parts[] = "{$days} يومًا";
                            }
                        } else {
                            $parts[] = 'أقل من يوم';
                        }
                    }

                    $this->years_from_registration = implode(' و ', $parts);
                } else {
                    $this->years_from_registration = 'تاريخ النهاية يسبق تاريخ القيد';
                }
            } catch (\Exception $e) {
                $this->years_from_registration = 'خطأ في التواريخ';
            }
        } else {
            $this->years_from_registration = '-';
        }
    }

    /**
     * التحقق من البيانات المدخلة وتحديث سجل المرشح في قاعدة البيانات مع تصفية الحقول غير المرتبطة بالحالة وحفظ النتيجة بدقة.
     */
    public function updateStatus()
    {
        $rules = ['study_status' => 'required'];

        $inputStatus = $this->study_status;

        if ($inputStatus == 'تنفيذ دراسة') {
            $rules['execution_date'] = 'required|date';
            $rules['registration_date'] = 'required|date';
        } elseif ($inputStatus == 'حصل على الدرجة') {
            $rules['nominated_degree_date'] = 'required|date';
        } elseif ($inputStatus == 'اعتذار') {
            $rules['apology_date'] = 'required|date';
            $rules['apology_reason'] = 'required|string|max:500';
        }

        $this->validate($rules);

        $this->calculateStudyDuration();

        $reg = PostgraduateRegistration::findOrFail($this->selectedRegId);

        // إذا اختر المسؤول "تنفيذ دراسة"، سيتم تخزين study_status في القاعدة "مستمر" مع حفظ تاريخ التنفيذ والقيد
        $dbStudyStatus = ($inputStatus == 'تنفيذ دراسة') ? 'مستمر' : $inputStatus;

        $reg->update([
            'study_status' => $dbStudyStatus,
            'execution_date' => ($inputStatus == 'تنفيذ دراسة' || $inputStatus == 'مستمر') ? $this->execution_date : null,
            'registration_date' => $this->registration_date,
            'nominated_degree_date' => $inputStatus == 'حصل على الدرجة' ? $this->nominated_degree_date : null,
            'nominated_degree_status' => $inputStatus == 'اعتذار' ? 'اعتذر ولم يحصل على الدرجة' : ($inputStatus == 'حصل على الدرجة' ? $this->nominated_degree_status : null),
            'apology_date' => $inputStatus == 'اعتذار' ? $this->apology_date : null,
            'apology_reason' => $inputStatus == 'اعتذار' ? $this->apology_reason : null,
            'years_from_registration' => $this->years_from_registration,
        ]);

        $this->selectedRegId = null;
        session()->flash('success', 'تم تحديث بيانات الدراسة بنجاح.');
        $this->dispatch('close-modal', name: 'studyStatusModal');
    }

    public function exportExcel()
{
    $filters = [
        'search_name' => $this->search_name,
        'search_national_id' => $this->search_national_id,
        'filter_required_degree' => $this->filter_required_degree,
        'filter_study_status' => $this->filter_study_status,
        'filter_application_date' => $this->filter_application_date,
    ];

    return Excel::download(new PostgraduateRegistrationsExport($filters), 'postgraduate_study_status.xlsx');
}

    /**
     * تطبيق الفلاتر والبحث واسترجاع البيانات مع الترقيم لعرضها في واجهة المستخدم.
     */
    public function render()
    {
        $query = PostgraduateRegistration::with(['healthProfessional']);

        if ($this->search_name) {
            $query->whereHas('healthProfessional', function ($q) {
                $q->where('name', 'like', '%' . $this->search_name . '%');
            });
        }

        if ($this->search_national_id) {
            $query->whereHas('healthProfessional', function ($q) {
                $q->where('national_id', 'like', '%' . $this->search_national_id . '%');
            });
        }

        if ($this->filter_required_degree) {
            $query->where('required_degree', $this->filter_required_degree);
        }

        if ($this->filter_study_status) {
            $query->where('study_status', $this->filter_study_status);
        }

        if ($this->filter_application_date) {
            $query->whereDate('application_date', $this->filter_application_date);
        }

        return view('livewire.postgraduate.manage-study-status', [
            'registrations' => $query->latest()->paginate(10),
        ])->layout('layouts.app');
    }
}
