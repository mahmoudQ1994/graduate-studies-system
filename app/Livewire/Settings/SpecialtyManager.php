<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Models\Specialty;
use App\Exports\SpecialtiesExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class SpecialtyManager extends Component
{
    use WithPagination, WithFileUploads;

    protected string $paginationTheme = 'bootstrap';

    public string $name = '';
    public bool $is_active = true;
    public ?int $specialty_id = null;
    public string $search = '';
    public bool $isEdit = false;
    public bool $showForm = false;

    public $importFile;
    public array $previewData = [];
    public bool $showPreviewModal = false;
    public bool $skipDuplicates = true;

    protected array $rules = [
        'name' => 'required|min:2|max:150|unique:specialties,name',
        'is_active' => 'boolean',
    ];

    protected array $messages = [
        'name.required' => 'اسم المؤهل الوظيفي مطلوب',
        'name.unique' => 'هذا المؤهل مسجل مسبقاً',
        'name.min' => 'اسم المؤهل يجب ألا يقل عن حرفين',
    ];

    public function updatedSearch(): void { $this->resetPage(); }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) { $this->resetFields(); }
    }

    public function resetFields(): void
    {
        $this->name = '';
        $this->is_active = true;
        $this->specialty_id = null;
        $this->isEdit = false;
        $this->showForm = false;
        $this->resetValidation();
    }

    public function store(): void
    {
        $this->validate();

        Specialty::create([
            'name' => $this->name,
            'is_active' => $this->is_active,
            'user_id' => Auth::id(),
        ]);

        session()->flash('message', 'تم إضافة المؤهل الوظيفي بنجاح');
        $this->resetFields();
    }

    public function edit(int $id): void
    {
        $specialty = Specialty::findOrFail($id);
        $this->specialty_id = $specialty->id;
        $this->name = $specialty->name;
        $this->is_active = (bool) $specialty->is_active;
        $this->isEdit = true;
        $this->showForm = true;
    }

    public function update(): void
    {
        $this->rules['name'] = 'required|min:2|max:150|unique:specialties,name,' . $this->specialty_id;
        $this->validate();

        $specialty = Specialty::findOrFail($this->specialty_id);
        $specialty->update([
            'name' => $this->name,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'تم تحديث بيانات المؤهل الوظيفي بنجاح');
        $this->resetFields();
    }

    public function delete(int $id): void
    {
        Specialty::findOrFail($id)->delete();
        session()->flash('message', 'تم حذف المؤهل الوظيفي بنجاح');
    }

    public function updatedImportFile()
    {
        $this->validate([
            'importFile' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $path = $this->importFile->getRealPath();
            $data = Excel::toArray([], $path);

            if (empty($data) || empty($data[0])) {
                session()->flash('error', 'الملف المرفوع فارغ أو غير صالح.');
                return;
            }

            $rows = $data[0];
            array_shift($rows);

            $this->previewData = [];

            foreach ($rows as $row) {
                if (empty($row[0])) continue;

                $specName = trim($row[0] ?? '');
                $existsInDb = Specialty::where('name', $specName)->exists();

                $this->previewData[] = [
                    'name' => $specName,
                    'is_duplicate' => $existsInDb,
                ];
            }

            $this->showPreviewModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء قراءة الملف: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new SpecialtiesExport, 'qualifications_export_' . date('Y-m-d') . '.xlsx');
    }

    public function confirmImport()
    {
        try {
            $savedCount = 0;

            foreach ($this->previewData as $item) {
                if ($item['is_duplicate'] && $this->skipDuplicates) {
                    continue;
                }

                Specialty::updateOrCreate(
                    ['name' => $item['name']],
                    ['is_active' => true, 'user_id' => Auth::id()]
                );

                $savedCount++;
            }

            $this->reset(['importFile', 'previewData', 'showPreviewModal']);
            session()->flash('message', "تم استيراد وحفظ $savedCount مؤهل وظيفي بنجاح.");
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء الحفظ: ' . $e->getMessage());
        }
    }

    public function cancelImport()
    {
        $this->reset(['importFile', 'previewData', 'showPreviewModal']);
    }

    public function render()
    {
        $specialties = Specialty::with('user')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(6);

        return view('livewire.settings.specialty-manager', [
            'specialties' => $specialties,
        ]);
    }
}
