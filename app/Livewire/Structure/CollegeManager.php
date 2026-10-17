<?php

namespace App\Livewire\Structure;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Models\College;
use App\Models\University;
use App\Exports\CollegesExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class CollegeManager extends Component
{
    use WithPagination, WithFileUploads;

    protected string $paginationTheme = 'bootstrap';

    public string $name = '';
    public ?int $university_id = null;
    public bool $is_active = true;
    public ?int $college_id = null;
    public string $search = '';
    public string $filterUniversity = '';
    public bool $isEdit = false;
    public bool $showForm = false;

    // متغيرات الاستيراد والمعاينة
    public $importFile;
    public array $previewData = [];
    public bool $showPreviewModal = false;
    public bool $skipDuplicates = true;

    protected array $rules = [
        'name' => 'required|min:3|max:150',
        'university_id' => 'required|exists:universities,id',
        'is_active' => 'boolean',
    ];

    protected array $messages = [
        'name.required' => 'اسم الكلية مطلوب',
        'university_id.required' => 'يجب اختيار الجامعة التابعة لها الكلية',
        'university_id.exists' => 'الجامعة المخطارة غير مسجلة بالنظام',
    ];

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilterUniversity(): void { $this->resetPage(); }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) { $this->resetFields(); }
    }

    public function resetFields(): void
    {
        $this->name = '';
        $this->university_id = null;
        $this->is_active = true;
        $this->college_id = null;
        $this->isEdit = false;
        $this->showForm = false;
        $this->resetValidation();
    }

    public function store(): void
    {
        $this->validate();

        College::create([
            'name' => $this->name,
            'university_id' => $this->university_id,
            'user_id' => Auth::id(),
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'تم إضافة الكلية بنجاح');
        $this->resetFields();
    }

    public function edit(int $id): void
    {
        $college = College::findOrFail($id);
        $this->college_id = $college->id;
        $this->name = $college->name;
        $this->university_id = $college->university_id;
        $this->is_active = (bool) $college->is_active;
        $this->isEdit = true;
        $this->showForm = true;
    }

    public function update(): void
    {
        $this->validate();

        $college = College::findOrFail($this->college_id);
        $college->update([
            'name' => $this->name,
            'university_id' => $this->university_id,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'تم تحديث بيانات الكلية بنجاح');
        $this->resetFields();
    }

    public function delete(int $id): void
    {
        College::findOrFail($id)->delete();
        session()->flash('message', 'تم حذف الكلية بنجاح');
    }

    public function export()
    {
        return Excel::download(new CollegesExport, 'colleges_export_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * خطوة 1: قراءة ملف الإكسل ومعاينة البيانات وفحص التكرار
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
            array_shift($rows); // تخطي الهيدر

            $this->previewData = [];

            foreach ($rows as $row) {
                if (empty($row[0])) continue;

                $collegeName = trim($row[0] ?? '');
                $uniName = trim($row[1] ?? '');

                // البحث عن الجامعة بالاسم أو ربطها بجدول الجامعات
                $university = University::where('name', 'like', '%' . $uniName . '%')->first();
                $uniId = $university ? $university->id : null;

                // فحص هل الكلية موجودة مسبقاً بنفس الجامعة؟
                $existsInDb = false;
                if ($uniId) {
                    $existsInDb = College::where('name', $collegeName)->where('university_id', $uniId)->exists();
                }

                $this->previewData[] = [
                    'name' => $collegeName,
                    'university_name' => $uniName,
                    'university_id' => $uniId,
                    'is_duplicate' => $existsInDb,
                ];
            }

            $this->showPreviewModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء قراءة الملف: ' . $e->getMessage());
        }
    }

    /**
     * خطوة 2: تأكيد وحفظ الكليات المستوردة
     */
    public function confirmImport()
    {
        try {
            $savedCount = 0;

            foreach ($this->previewData as $item) {
                if (empty($item['university_id'])) continue; // تخطي إن لم تكن الجامعة معروفة

                if ($item['is_duplicate'] && $this->skipDuplicates) {
                    continue; // تخطي المكرر
                }

                College::updateOrCreate(
                    [
                        'name' => $item['name'],
                        'university_id' => $item['university_id']
                    ],
                    [
                        'user_id' => Auth::id(),
                        'is_active' => true,
                    ]
                );

                $savedCount++;
            }

            $this->reset(['importFile', 'previewData', 'showPreviewModal']);
            session()->flash('message', "تم استيراد وحفظ $savedCount كلية بنجاح.");
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
        $colleges = College::with(['university', 'user'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterUniversity, function ($query) {
                $query->where('university_id', $this->filterUniversity);
            })
            ->latest()
            ->paginate(6);

        return view('livewire.structure.college-manager', [
            'colleges' => $colleges,
            'universities' => University::where('is_active', true)->get(),
        ]);
    }
}
