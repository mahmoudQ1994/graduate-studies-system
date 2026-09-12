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

    // متغيرات البحث والتصفية
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
    public $degree_grade;
    public $apology_date;
    public $apology_reason;
    public $rejection_date;
    public $rejection_reason;
    public $years_from_registration = '-';

    public function updatingSearchName() { $this->resetPage(); }
    public function updatingSearchNationalId() { $this->resetPage(); }
    public function updatingFilterRequiredDegree() { $this->resetPage(); }
    public function updatingFilterStudyStatus() { $this->resetPage(); }
    public function updatingFilterApplicationDate() { $this->resetPage(); }

    public function editStatus($regId)
    {
        $reg = PostgraduateRegistration::findOrFail($regId);
        $this->selectedRegId = $reg->id;

        // إذا كانت الحالة في قاعدة البيانات 'مستمر' ولديه تاريخ تنفيذ، نعرضه في الواجهة باسم 'تنفيذ دراسة'
        if ($reg->study_status == 'مستمر' && !empty($reg->execution_date)) {
            $this->study_status = 'تنفيذ دراسة';
        } else {
            $this->study_status = $reg->study_status ?? 'جاري فحص الطلب';
        }

        $this->execution_date = $reg->execution_date ? Carbon::parse($reg->execution_date)->format('Y-m-d') : null;
        $this->registration_date = $reg->registration_date ? Carbon::parse($reg->registration_date)->format('Y-m-d') : null;
        $this->nominated_degree_date = $reg->nominated_degree_date ? Carbon::parse($reg->nominated_degree_date)->format('Y-m-d') : null;
        $this->nominated_degree_status = $reg->nominated_degree_status;
        $this->degree_grade = $reg->degree_grade ?? null;
        $this->apology_date = $reg->apology_date ? Carbon::parse($reg->apology_date)->format('Y-m-d') : null;
        $this->apology_reason = $reg->apology_reason ?? null;
        $this->rejection_date = $reg->rejection_date ? Carbon::parse($reg->rejection_date)->format('Y-m-d') : null;
        $this->rejection_reason = $reg->rejection_reason ?? null;

        $this->calculateStudyDuration();
        $this->dispatch('open-modal');
    }

    public function updatedRegistrationDate() { $this->calculateStudyDuration(); }
    public function updatedNominatedDegreeDate() { $this->calculateStudyDuration(); }
    public function updatedApologyDate() { $this->calculateStudyDuration(); }
    public function updatedExecutionDate() { $this->calculateStudyDuration(); }

    public function updatedStudyStatus($value)
    {
        // إعادة حساب المدة عند تغيير الحالة بناءً على التواريخ الحالية المعبأة
        $this->calculateStudyDuration();
    }

    public function calculateStudyDuration()
    {
        if ($this->study_status == 'عدم القبول بالدراسة' || empty($this->registration_date)) {
            $this->years_from_registration = ($this->study_status == 'عدم القبول بالدراسة') ? 'لا يحسب (مرفود)' : '-';
            return;
        }

        try {
            $start = Carbon::parse($this->registration_date);
            $end = null;

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
                    $parts[] = $years == 1 ? 'سنة واحدة' : ($years == 2 ? 'سنتان' : "{$years} سنوات");
                }
                if ($months > 0) {
                    $parts[] = $months == 1 ? 'شهر واحد' : ($months == 2 ? 'شهران' : "{$months} شهور");
                }
                if ($years == 0 && $months == 0) {
                    $parts[] = $days > 0 ? ($days == 1 ? 'يوم واحد' : ($days == 2 ? 'يومان' : "{$days} أيام")) : 'أقل من يوم';
                }

                $this->years_from_registration = implode(' و ', $parts);
            } else {
                $this->years_from_registration = 'تاريخ النهاية يسبق تاريخ القيد';
            }
        } catch (\Exception $e) {
            $this->years_from_registration = 'خطأ في التواريخ';
        }
    }

    public function updateStatus()
    {
        $rules = ['study_status' => 'required'];
        $inputStatus = $this->study_status;

        if ($inputStatus == 'تنفيذ دراسة') {
            $rules['execution_date'] = 'required|date';
            $rules['registration_date'] = 'required|date';
        } elseif ($inputStatus == 'حصل على الدرجة') {
            $rules['registration_date'] = 'required|date';
            $rules['nominated_degree_date'] = 'required|date';
            $rules['degree_grade'] = 'required|string';
        } elseif ($inputStatus == 'اعتذار') {
            $rules['apology_date'] = 'required|date';
            $rules['apology_reason'] = 'required|string|max:500';
        } elseif ($inputStatus == 'عدم القبول بالدراسة') {
            $rules['rejection_date'] = 'required|date';
            $rules['rejection_reason'] = 'required|string|max:500';
        }

        $this->validate($rules);
        $this->calculateStudyDuration();

        $reg = PostgraduateRegistration::findOrFail($this->selectedRegId);

        // تحديد الحالة لتخزينها في قاعدة البيانات
        $dbStudyStatus = $inputStatus;
        if ($inputStatus == 'تنفيذ دراسة') {
            $dbStudyStatus = 'مستمر';
        }

        // تنفيذ التحديث مع التأكد من إسناد قيمة execution_date بشكل مباشر من المتغير العام
        $reg->update([
            'study_status' => $dbStudyStatus,
            'execution_date' => ($inputStatus == 'تنفيذ دراسة') ? $this->execution_date : $reg->execution_date,
            'registration_date' => in_array($inputStatus, ['تنفيذ دراسة', 'مستمر', 'حصل على الدرجة', 'اعتذار']) ? $this->registration_date : $reg->registration_date,
            'nominated_degree_date' => $inputStatus == 'حصل على الدرجة' ? $this->nominated_degree_date : null,
            'nominated_degree_status' => $inputStatus == 'حصل على الدرجة' ? 'حصل على الدرجة' : ($inputStatus == 'اعتذار' ? 'اعتذر ولم يحصل على الدرجة' : null),
            'degree_grade' => $inputStatus == 'حصل على الدرجة' ? $this->degree_grade : null,
            'apology_date' => $inputStatus == 'اعتذار' ? $this->apology_date : null,
            'apology_reason' => $inputStatus == 'اعتذار' ? $this->apology_reason : null,
            'rejection_date' => $inputStatus == 'عدم القبول بالدراسة' ? $this->rejection_date : null,
            'rejection_reason' => $inputStatus == 'عدم القبول بالدراسة' ? $this->rejection_reason : null,
            'years_from_registration' => $this->years_from_registration,
        ]);

        $this->selectedRegId = null;
        session()->flash('success', 'تم تحديث بيانات الدراسة للمرشح وحفظ تاريخ التنفيذ بنجاح.');
        $this->dispatch('close-modal');
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

    public function render()
    {
        $query = PostgraduateRegistration::with(['healthProfessional']);

        if ($this->search_name) {
            $query->whereHas('healthProfessional', fn($q) => $q->where('name', 'like', '%' . $this->search_name . '%'));
        }
        if ($this->search_national_id) {
            $query->whereHas('healthProfessional', fn($q) => $q->where('national_id', 'like', '%' . $this->search_national_id . '%'));
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
            'registrations' => $query->latest()->paginate(3),
        ])->layout('layouts.app');
    }
}
