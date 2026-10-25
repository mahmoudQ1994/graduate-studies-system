<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Trainees\Trainee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Specialty;
use App\Models\Facility;

class TraineeManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // متغيرات البحث والفلاتر
    public $search = '';

    // متغيرات للبحث التفاعلي عن جهة العمل والوظيفة
    public $facilitySearch = '';
    public $jobTitleSearch = ''; // متغير البحث التفاعلي للوظيفة
    public $filter_workplace = '';
    public $filter_qualification = '';

    // متغيرات نموذج إضافة/تعديل متدرب
    public $traineeId;
    public $name_ar;
    public $name_en;
    public $national_id;
    public $qualification;
    public $job_title;
    public $department;
    public $workplace;
    public $email;
    public $phone_number;
    public $status = 'active';

    protected $queryString = ['search', 'filter_workplace'];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterWorkplace() { $this->resetPage(); }

    // إعادة تعيين الفلاتر
    public function resetFilters()
    {
        $this->reset(['search', 'filter_workplace', 'filter_qualification']);
    }

    // فتح النافذة المنبثقة للإضافة أو التعديل
    public function openTraineeModal($id = null)
    {
        $this->resetValidation();
        $this->reset([
            'traineeId', 'name_ar', 'name_en', 'national_id',
            'qualification', 'job_title', 'department',
            'workplace', 'email', 'phone_number', 'status',
            'facilitySearch', 'jobTitleSearch'
        ]);

        if ($id) {
            $trainee = Trainee::findOrFail($id);
            $this->traineeId = $trainee->id;
            $this->name_ar = $trainee->name_ar;
            $this->name_en = $trainee->name_en;
            $this->national_id = $trainee->national_id;
            $this->qualification = $trainee->qualification;
            $this->job_title = $trainee->job_title;
            $this->jobTitleSearch = $trainee->job_title; // تعبئة حقل البحث بالوظيفة الحالية عند التعديل
            $this->department = $trainee->department;
            $this->workplace = $trainee->workplace;
            $this->facilitySearch = $trainee->workplace; // تعبئة جهة العمل الحالية عند التعديل
            $this->email = $trainee->email;
            $this->phone_number = $trainee->phone_number;
            $this->status = $trainee->status;
        }

        $this->dispatch('show-trainee-modal');
    }

    // دالة لجلب جهات العمل المطابقة للكتابة في حقل البحث
    public function getMatchingFacilitiesProperty()
    {
        if (strlen(trim($this->facilitySearch)) < 1) {
            return collect();
        }

        return Facility::where('is_active', 1)
            ->where('name', 'like', '%' . $this->facilitySearch . '%')
            ->limit(5)
            ->get();
    }

    // دالة لاختيار جهة العمل من القائمة المنبثقة
    public function selectFacility($name)
    {
        $this->workplace = $name;
        $this->facilitySearch = $name;
    }

    // دالة لجلب الوظائف المطابقة للكتابة من جدول job_titles (بناءً على طلبك لـ 350+ وظيفة)
    public function getMatchingJobTitlesProperty()
    {
        if (strlen(trim($this->jobTitleSearch)) < 1) {
            return collect();
        }

        return DB::table('job_titles')
            ->where('is_active', 1)
            ->where('name', 'like', '%' . $this->jobTitleSearch . '%')
            ->limit(6) // عرض أفضل 6 نتائج مطابقة لتكون القائمة سريعة ونظيفة
            ->get();
    }

    // دالة لاختيار الوظيفة من القائمة المنبثقة للبحث الحي
    public function selectJobTitle($name)
    {
        $this->job_title = $name;
        $this->jobTitleSearch = $name;
    }

    // حفظ المتدرب (إضافة أو تحديث)
    public function saveTrainee()
    {
        $this->validate([
            'name_ar' => 'required|string|max:255',
            'national_id' => 'required|string|size:14|unique:trainees,national_id,' . $this->traineeId,
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ], [
            'national_id.size' => 'الرقم القومي يجب أن يكون 14 رقماً بدقة.',
            'national_id.unique' => 'هذا الرقم القومي مسجل مسبقاً لمتدرب آخر.',
            'name_ar.required' => 'اسم المتدرب بالعربي مطلوب.',
        ]);

        if ($this->traineeId) {
            // تحديث
            $trainee = Trainee::findOrFail($this->traineeId);
            $trainee->update([
                'name_ar' => $this->name_ar,
                'name_en' => $this->name_en,
                'national_id' => $this->national_id,
                'qualification' => $this->qualification,
                'job_title' => $this->job_title,
                'department' => $this->department,
                'workplace' => $this->workplace,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'status' => $this->status,
                'updated_by' => Auth::id(),
            ]);
            session()->flash('message', 'تم تحديث بيانات المتدرب بنجاح.');
        } else {
            // إضافة جديد
            Trainee::create([
                'name_ar' => $this->name_ar,
                'name_en' => $this->name_en,
                'national_id' => $this->national_id,
                'qualification' => $this->qualification,
                'job_title' => $this->job_title,
                'department' => $this->department,
                'workplace' => $this->workplace,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'status' => $this->status,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            session()->flash('message', 'تم إضافة المتدرب الجديد بنجاح.');
        }

        $this->dispatch('close-trainee-modal');
    }

    // حذف متدرب
    public function deleteTrainee($id)
    {
        $trainee = Trainee::find($id);
        if ($trainee) {
            $trainee->delete();
            session()->flash('message', 'تم حذف المتدرب بنجاح.');
        }
    }

    public function render()
    {
        $trainees = Trainee::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name_ar', 'like', '%' . $this->search . '%')
                      ->orWhere('national_id', 'like', '%' . $this->search . '%')
                      ->orWhere('phone_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filter_workplace, function ($query) {
                $query->where('workplace', 'like', '%' . $this->filter_workplace . '%');
            })
            ->when($this->filter_qualification, function ($query) {
                $query->where('qualification', 'like', '%' . $this->filter_qualification . '%');
            })
            ->latest()
            ->paginate(10);

        // جلب المؤهلات من جدول specialties
        $specialties = Specialty::where('is_active', 1)->get();

        return view('livewire.training.trainee-manager', [
            'trainees' => $trainees,
            'specialties' => $specialties,
        ])->layout('layouts.app');
    }
}
