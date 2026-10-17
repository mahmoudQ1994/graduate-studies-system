<?php
namespace App\Livewire\Structure;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Models\University;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UniversitiesExport;

#[Layout('layouts.app')]
class UniversityManager extends Component
{
    use WithPagination, WithFileUploads;

    protected string $paginationTheme = 'bootstrap';

    public string $name = '';
    public string $type = 'حكومية';
    public bool $is_active = true;
    public ?int $university_id = null;
    public string $search = '';
    public bool $isEdit = false;
    public bool $showForm = false;

    // متغيرات الاستيراد والمعاينة
    public $importFile;
    public array $previewData = [];
    public bool $showPreviewModal = false;
    public bool $skipDuplicates = true;

    protected array $rules = [
        'name' => 'required|min:3|max:150|unique:universities,name',
        'type' => 'required|in:حكومية,خاصة,أهلية,أخرى',
        'is_active' => 'boolean',
    ];

    protected array $messages = [
        'name.required' => 'اسم الجامعة مطلوب',
        'name.unique' => 'هذه الجامعة مسجلة مسبقاً',
        'type.required' => 'نوع الجامعة مطلوب',
        'type.in' => 'نوع الجامعة المختار غير صالح',
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
        $this->type = 'حكومية';
        $this->is_active = true;
        $this->university_id = null;
        $this->isEdit = false;
        $this->showForm = false;
        $this->resetValidation();
    }

    public function store(): void
    {
        $this->validate();

        University::create([
            'name' => $this->name,
            'type' => $this->type,
            'user_id' => Auth::id(),
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'تم إضافة الجامعة بنجاح');
        $this->resetFields();
    }

    public function edit(int $id): void
    {
        $university = University::findOrFail($id);
        $this->university_id = $university->id;
        $this->name = $university->name;
        $this->type = $university->type;
        $this->is_active = (bool) $university->is_active;
        $this->isEdit = true;
        $this->showForm = true;
    }

    public function update(): void
    {
        $this->rules['name'] = 'required|min:3|max:150|unique:universities,name,' . $this->university_id;
        $this->validate();

        $university = University::findOrFail($this->university_id);
        $university->update([
            'name' => $this->name,
            'type' => $this->type,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'تم تحديث بيانات الجامعة بنجاح');
        $this->resetFields();
    }

    public function delete(int $id): void
    {
        University::findOrFail($id)->delete();
        session()->flash('message', 'تم حذف الجامعة بنجاح');
    }

    public function export()
    {
        return Excel::download(new UniversitiesExport, 'universities_export_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * خطوة 1: قراءة ملف إكسل معالجة المعاينة وفحص التكرار
     */
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
            array_shift($rows); // تخطي الصف الأول (العناوين)

            $this->previewData = [];

            foreach ($rows as $row) {
                if (empty($row[0])) continue;

                $uniName = trim($row[0] ?? '');
                $uniType = trim($row[1] ?? 'حكومية');

                // فحص هل الاسم مسجل مسبقاً؟
                $existsInDb = University::where('name', $uniName)->exists();

                $this->previewData[] = [
                    'name' => $uniName,
                    'type' => in_array($uniType, ['حكومية', 'خاصة', 'أهلية', 'أخرى']) ? $uniType : 'حكومية',
                    'is_duplicate' => $existsInDb,
                ];
            }

            $this->showPreviewModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء قراءة الملف: ' . $e->getMessage());
        }
    }

    /**
     * خطوة 2: تأكيد الحفظ واستيراد البيانات
     */
    public function confirmImport()
    {
        try {
            $savedCount = 0;

            foreach ($this->previewData as $item) {
                if ($item['is_duplicate'] && $this->skipDuplicates) {
                    continue; // تخطي المكرر
                }

                University::updateOrCreate(
                    ['name' => $item['name']],
                    [
                        'type' => $item['type'],
                        'user_id' => Auth::id(),
                        'is_active' => true,
                    ]
                );

                $savedCount++;
            }

            $this->reset(['importFile', 'previewData', 'showPreviewModal']);
            session()->flash('message', "تم استيراد وحفظ $savedCount جامعة بنجاح.");
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
        $universities = University::with(['user', 'colleges'])
            ->withCount('colleges')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('type', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(6);

        return view('livewire.structure.university-manager', [
            'universities' => $universities,
        ]);
    }
}
