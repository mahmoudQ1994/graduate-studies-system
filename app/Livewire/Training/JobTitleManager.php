<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\JobTitlesExport;
use App\Imports\JobTitlesImport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class JobTitleManager extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $jobTitleId;
    public $name;
    public $is_active = true;

    public $excelFile;

    // متغيرات مرحلة المراجعة (Preview)
    public $previewData = [];
    public $duplicatesCount = 0;
    public $showPreviewModal = false;

    public function updatingSearch() { $this->resetPage(); }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['jobTitleId', 'name', 'is_active', 'excelFile']);
        $this->is_active = true;

        if ($id) {
            $job = DB::table('job_titles')->where('id', $id)->first();
            abort_if(!$job, 404);
            $this->jobTitleId = $job->id;
            $this->name = $job->name;
            $this->is_active = $job->is_active;
        }

        $this->dispatch('show-job-modal');
    }

    public function saveJob()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:job_titles,name,' . $this->jobTitleId,
        ], [
            'name.required' => 'اسم الوظيفة مطلوب.',
            'name.unique' => 'هذه الوظيفة مسجلة مسبقاً.',
        ]);

        if ($this->jobTitleId) {
            DB::table('job_titles')->where('id', $this->jobTitleId)->update([
                'name' => $this->name,
                'is_active' => $this->is_active,
                'updated_at' => now(),
            ]);
            session()->flash('message', 'تم تحديث الوظيفة بنجاح.');
        } else {
            DB::table('job_titles')->insert([
                'name' => $this->name,
                'is_active' => $this->is_active,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            session()->flash('message', 'تم إضافة الوظيفة بنجاح.');
        }

        $this->dispatch('close-job-modal');
    }

    // الخطوة الأولى: رفع الملف وقراءته لعرض جدول المراجعة
    public function previewExcel()
    {
        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ], [
            'excelFile.required' => 'يرجى اختيار ملف مرفق.',
            'excelFile.mimes' => 'يجب أن يكون الملف بصيغة Excel أو CSV.',
        ]);

        try {
            Excel::import(new JobTitlesImport, $this->excelFile);

            $this->previewData = session()->get('import_preview', []);
            $this->duplicatesCount = session()->get('import_duplicates_count', 0);

            $this->dispatch('close-import-modal'); // إغلاق نافذة الرفع العادية
            $this->dispatch('show-preview-modal');  // فتح نافذة المراجعة

        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء قراءة الملف: ' . $e->getMessage());
        }
    }

    // الخطوة الثانية: تأكيد الحفظ النهائي للبيانات المراجعة لقاعدة البيانات
    public function confirmImport()
    {
        if (empty($this->previewData)) {
            session()->flash('error', 'لا توجد بيانات جديدة للاستيراد.');
            return;
        }

        $count = 0;
        foreach ($this->previewData as $item) {
            DB::table('job_titles')->insert([
                'name' => $item['name'],
                'is_active' => true,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $count++;
        }

        session()->flash('message', "تم حفظ واستيراد $count وظيفة جديدة بنجاح في النظام.");
        $this->reset(['excelFile', 'previewData', 'duplicatesCount']);
        $this->dispatch('close-preview-modal');
    }

    public function exportExcel(): BinaryFileResponse
    {
        return Excel::download(new JobTitlesExport, 'job_titles_' . date('Y-m-d') . '.xlsx');
    }

    public function deleteJob($id)
    {
        if (DB::table('job_titles')->where('id', $id)->exists()) {
            DB::table('job_titles')->where('id', $id)->delete();
            session()->flash('message', 'تم حذف الوظيفة بنجاح.');
        }
    }

    public function render()
    {
        $jobs = DB::table('job_titles')
            ->leftJoin('users', 'job_titles.created_by', '=', 'users.id')
            ->when($this->search, function ($query) {
                $query->where('job_titles.name', 'like', '%' . $this->search . '%');
            })
            ->select('job_titles.*', 'users.name as creator_name')
            ->latest('job_titles.created_at')
            ->paginate(10);

        return view('livewire.training.job-title-manager', [
            'jobs' => $jobs,
        ])->layout('layouts.app');
    }
}
