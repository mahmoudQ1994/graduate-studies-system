<?php

namespace App\Livewire\Structure;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Models\Facility;
use App\Models\District;
use App\Models\Sector;
use App\Exports\FacilitiesExport;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class HospitalManager extends Component
{
    use WithPagination, WithFileUploads;

    protected string $paginationTheme = 'bootstrap';

    public string $name = '';
    public string $type = ' ';
    public ?int $district_id = null;
    public ?int $sector_id = null;
    public bool $is_active = true;
    public ?int $hospital_id = null;
    public string $search = '';
    public string $filterType = '';
    public bool $isEdit = false;
    public bool $showForm = false;
    public $importFile;

    // متغيرات المعاينة الجديدة
    public array $previewData = [];
    public bool $showPreviewModal = false;
    public bool $skipDuplicates = true;

    protected array $rules = [
        'name' => 'required|min:3|max:150',
        'type' => 'required|in:ديوان المديرية,مستشفى عام,مستشفى مركزي,مستشفى نوعي,إدارة صحية,مركز طب أسرة,وحدة طب أسرة',
        'district_id' => 'required|exists:districts,id',
        'sector_id' => 'nullable|exists:sectors,id',
        'is_active' => 'boolean',
    ];

    protected array $messages = [
        'name.required' => 'اسم المستشفى أو الجهة الصحية مطلوب',
        'name.min' => 'اسم الجهة يجب ألا يقل عن 3 أحرف',
        'type.required' => 'نوع الجهة الصحية مطلوب',
        'type.in' => 'نوع المنشأة المختار غير صالح',
        'district_id.required' => 'يرجى اختيار المركز أو المدينة التابعة لها',
        'district_id.exists' => 'المركز المختار غير مسجل بالنظام',
        'sector_id.exists' => 'القطاع المختار غير مسجل بالنظام',
    ];

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilterType(): void { $this->resetPage(); }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) { $this->resetFields(); }
    }

    public function resetFields(): void
    {
        $this->name = '';
        $this->type = '';
        $this->district_id = null;
        $this->sector_id = null;
        $this->is_active = true;
        $this->hospital_id = null;
        $this->isEdit = false;
        $this->showForm = false;
        $this->resetValidation();
    }

    public function store(): void
    {
        $this->validate();
        Facility::create([
            'name' => $this->name,
            'type' => $this->type,
            'district_id' => $this->district_id,
            'sector_id' => $this->sector_id,
            'is_active' => $this->is_active,
        ]);
        session()->flash('message', 'تم إضافة المنشأة الصحية بنجاح');
        $this->resetFields();
    }

    public function edit(int $id): void
    {
        $facility = Facility::findOrFail($id);
        $this->hospital_id = $facility->id;
        $this->name = $facility->name;
        $this->type = $facility->type;
        $this->district_id = $facility->district_id;
        $this->sector_id = $facility->sector_id;
        $this->is_active = (bool) $facility->is_active;
        $this->isEdit = true;
        $this->showForm = true;
    }

    public function update(): void
    {
        $this->validate();
        $facility = Facility::findOrFail($this->hospital_id);
        $facility->update([
            'name' => $this->name,
            'type' => $this->type,
            'district_id' => $this->district_id,
            'sector_id' => $this->sector_id,
            'is_active' => $this->is_active,
        ]);
        session()->flash('message', 'تم تحديث بيانات المنشأة بنجاح');
        $this->resetFields();
    }

    public function delete(int $id): void
    {
        Facility::findOrFail($id)->delete();
        session()->flash('message', 'تم حذف المنشأة بنجاح');
    }

    public function toggleStatus(int $id): void
    {
        $facility = Facility::findOrFail($id);
        $facility->update(['is_active' => !$facility->is_active]);
    }

    public function render()
    {
        $hospitals = Facility::with(['district', 'sector'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterType, function ($query) {
                $query->where('type', $this->filterType);
            })
            ->latest()
            ->paginate(6);

        return view('livewire.structure.hospital-facility', [
            'hospitals' => $hospitals,
            'districts' => District::where('is_active', true)->get(),
            'sectors' => Sector::where('is_active', true)->get(),
        ]);
    }

    public function export()
    {
        return Excel::download(new FacilitiesExport, 'facilities_export_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * خطوة أولى: قراءة الملف وعرضه في نافذة المعاينة دون حفظه مباشرة
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
            array_shift($rows); // تخطي صف العناوين (الهيدر)

            $this->previewData = [];

            foreach ($rows as $row) {
                if (empty($row[0]) && empty($row[1])) continue;

                $name = trim($row[0] ?? '');
                $type = trim($row[1] ?? 'مستشفى عام');
                $districtName = trim($row[2] ?? '');
                $sectorName = trim($row[3] ?? '');

                // فحص هل الاسم مسجل مسبقاً في القاعدة؟
                $existsInDb = Facility::where('name', $name)->exists();

                $this->previewData[] = [
                    'name' => $name,
                    'type' => $type,
                    'district' => $districtName,
                    'sector' => $sectorName,
                    'is_duplicate' => $existsInDb,
                ];
            }

            $this->showPreviewModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء قراءة الملف: ' . $e->getMessage());
        }
    }

    /**
     * خطوة ثانية: التأكيد النهائي وحفظ البيانات بعد مراجعة المستخدم
     */
    public function confirmImport()
    {
        try {
            $savedCount = 0;

            foreach ($this->previewData as $item) {
                if ($item['is_duplicate'] && $this->skipDuplicates) {
                    continue; // تخطي المكرر إذا اختار المستخدم ذلك
                }

                $district = District::where('name', 'like', '%' . $item['district'] . '%')->first();
                $district_id = $district ? $district->id : 1;

                $sector_id = null;
                if (!empty($item['sector'])) {
                    $sector = Sector::where('name', 'like', '%' . $item['sector'] . '%')->first();
                    $sector_id = $sector ? $sector->id : null;
                }

                Facility::updateOrCreate(
                    ['name' => $item['name']],
                    [
                        'type' => $item['type'],
                        'district_id' => $district_id,
                        'sector_id' => $sector_id,
                        'is_active' => true,
                    ]
                );

                $savedCount++;
            }

            $this->reset(['importFile', 'previewData', 'showPreviewModal']);
            session()->flash('message', "تم استيراد وحفظ $savedCount منشأة صحية بنجاح.");
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء الحفظ: ' . $e->getMessage());
        }
    }

    public function cancelImport()
    {
        $this->reset(['importFile', 'previewData', 'showPreviewModal']);
    }
}
