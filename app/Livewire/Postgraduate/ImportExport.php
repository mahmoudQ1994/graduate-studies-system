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

    public function export()
    {
        return Excel::download(new PostgraduateExport, 'postgraduate_template.xlsx');
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new PostgraduateImport, $this->file->path());

            session()->flash('success', 'تم استيراد البيانات وتحديثها بنجاح.');
            $this->reset('file');
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.postgraduate.import-export')->layout('layouts.app');
    }
}


