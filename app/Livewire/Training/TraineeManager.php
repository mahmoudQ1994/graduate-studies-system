<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Trainees\Trainee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Specialty;
use App\Models\Facility;
use App\Imports\TraineesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TraineesExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TraineeManager extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    // متغيرات البحث والفلاتر
    public $search = '';
    public $facilitySearch = '';
    public $jobTitleSearch = '';
    public $filter_workplace = '';
    public $filter_qualification = '';

    // متغيرات إضافة/تعديل متدرب
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

    // متغيرات استيراد الإكسيل والمراجعة
    public $excelFile;
    public $previewData = [];
    public $duplicatesCount = 0;

    protected $queryString = ['search', 'filter_workplace'];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterWorkplace() { $this->resetPage(); }

    public function resetFilters()
    {
        $this->reset(['search', 'filter_workplace', 'filter_qualification']);
    }

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
            $this->jobTitleSearch = $trainee->job_title;
            $this->department = $trainee->department;
            $this->workplace = $trainee->workplace;
            $this->facilitySearch = $trainee->workplace;
            $this->email = $trainee->email;
            $this->phone_number = $trainee->phone_number;
            $this->status = $trainee->status;
        }

        $this->dispatch('show-trainee-modal');
    }

    public function getMatchingFacilitiesProperty()
    {
        if (strlen(trim($this->facilitySearch)) < 1) return collect();

        return Facility::where('is_active', 1)
            ->where('name', 'like', '%' . $this->facilitySearch . '%')
            ->limit(5)->get();
    }

    //دالة تنزيل الملف اكسيل
    public function exportTraineesExcel(): BinaryFileResponse
    {
        return Excel::download(
            new TraineesExport($this->search, $this->filter_workplace, $this->filter_qualification),
            'trainees_list_' . date('Y-m-d') . '.xlsx'
        );
    }

    public function selectFacility($name)
    {
        $this->workplace = $name;
        $this->facilitySearch = $name;
    }

    public function getMatchingJobTitlesProperty()
    {
        if (strlen(trim($this->jobTitleSearch)) < 1) return collect();

        return DB::table('job_titles')
            ->where('is_active', 1)
            ->where('name', 'like', '%' . $this->jobTitleSearch . '%')
            ->limit(6)->get();
    }

    public function selectJobTitle($name)
    {
        $this->job_title = $name;
        $this->jobTitleSearch = $name;
    }

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

    // دالة المعاينة وقراءة الملف
    public function previewTraineesExcel()
    {
        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ], [
            'excelFile.required' => 'يرجى اختيار الملف.',
            'excelFile.mimes' => 'الصيغة المسموحة Excel أو CSV.',
        ]);

        try {
            Excel::import(new TraineesImport, $this->excelFile);

            $this->previewData = session()->get('trainees_import_preview', []);
            $this->duplicatesCount = session()->get('trainees_import_duplicates', 0);

            $this->dispatch('close-import-modal');
            $this->dispatch('show-preview-modal');
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء قراءة الملف: ' . $e->getMessage());
        }
    }

    // دالة التأكيد والحفظ في قاعدة البيانات
    public function confirmImportTrainees()
    {
        if (empty($this->previewData)) {
            session()->flash('error', 'لا توجد بيانات صالحة للحفظ.');
            return;
        }

        $count = 0;
        foreach ($this->previewData as $item) {
            Trainee::create([
                'name_ar' => $item['name_ar'],
                'national_id' => $item['national_id'],
                'job_title' => $item['job_title'],
                'qualification' => $item['qualification'],
                'workplace' => $item['workplace'],
                'phone_number' => $item['phone_number'],
                'email' => $item['email'],
                'status' => 'active',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            $count++;
        }

        session()->flash('message', "تم استيراد وحفظ $count متدرب بنجاح.");
        $this->reset(['excelFile', 'previewData', 'duplicatesCount']);
        $this->dispatch('close-preview-modal');
    }

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

        $specialties = Specialty::where('is_active', 1)->get();

        return view('livewire.training.trainee-manager', [
            'trainees' => $trainees,
            'specialties' => $specialties,
        ])->layout('layouts.app');
    }
}
