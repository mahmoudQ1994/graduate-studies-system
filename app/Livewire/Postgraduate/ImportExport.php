<?php

namespace App\Livewire\Postgraduate;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Exports\PostgraduateExport;
use App\Imports\PostgraduateImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportExport extends Component
{
    use WithFileUploads;

    public $file;
    public $previewData = [];
    public $showPreview = false;

    public function export()
    {
        return Excel::download(new PostgraduateExport, 'postgraduate_template.xlsx');
    }

    public function previewImport()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $importer = new PostgraduateImport();
            $this->previewData = $importer->preview($this->file->path());
            $this->showPreview = true;

            session()->flash('success', 'تم فحص الملف بنجاح. يمكنك مراجعة السجلات وتعديل الموافقة قبل الاعتماد.');
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء فحص الملف: ' . $e->getMessage());
        }
    }

    // تحديد الكل أو إلغاء الكل
    public function toggleAll($status)
    {
        foreach ($this->previewData['details'] as &$item) {
            $item['approved'] = (bool) $status;
        }
    }

    // تنفيذ الرفع بناءً على السجلات التي وافق عليها المستخدم
    public function confirmSelectedImport()
    {
        try {
            // استخراج فهارس الصفوف التي تم تفعيل الموافقة عليها
            $approvedIndices = collect($this->previewData['details'])
                ->where('approved', true)
                ->pluck('row_index')
                ->toArray();

            if (empty($approvedIndices)) {
                session()->flash('error', 'يرجى تحديد سجل واحد على الأقل للموافقة على رفعه.');
                return;
            }

            $importer = new PostgraduateImport();
            $importer->executeImportSelected($this->file->path(), $approvedIndices);

            session()->flash('success', 'تم استيراد وتحديث السجلات المعتمدة بنجاح.');
            $this->reset(['file', 'previewData', 'showPreview']);
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء التنفيذ: ' . $e->getMessage());
        }
    }

    public function cancelPreview()
    {
        $this->reset(['file', 'previewData', 'showPreview']);
        session()->flash('success', 'تم إلغاء العملية.');
    }

    public function render()
    {
        return view('livewire.postgraduate.import-export')->layout('layouts.app');
    }
}
