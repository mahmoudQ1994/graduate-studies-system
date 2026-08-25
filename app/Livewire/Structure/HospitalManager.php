<?php

namespace App\Livewire\Structure;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Facility;
use App\Models\District;
use App\Models\Sector;

#[Layout('layouts.app')]
class HospitalManager extends Component
{
    use WithPagination;

    // نمط الترقيم الصفحي باستخدام Bootstrap
    protected string $paginationTheme = 'bootstrap';

    // خصائص نموذج الإدخال والبحث المتوافقة تماماً مع حقول الجدول
    public string $name = '';
    public string $type = 'مستشفى'; // القيمة الافتراضية بالعربي
    public ?int $district_id = null;
    public ?int $sector_id = null;
    public bool $is_active = true;
    public ?int $hospital_id = null;
    public string $search = '';
    public string $filterType = '';
    public bool $isEdit = false;
    public bool $showForm = false;

    // قواعد التحقق من صحة البيانات
    protected array $rules = [
        'name' => 'required|min:3|max:150',
        'type' => 'required|string',
        'district_id' => 'required|exists:districts,id',
        'sector_id' => 'nullable|exists:sectors,id',
        'is_active' => 'boolean',
    ];

    // رسائل التنبيه والخطأ المخصصة بالعربية
    protected array $messages = [
        'name.required' => 'اسم المستشفى أو الجهة الصحية مطلوب',
        'district_id.required' => 'يرجى اختيار المركز أو المدينة التابعة لها',
        'type.required' => 'نوع الجهة الصحية مطلوب',
    ];

    /**
     * إعادة ضبط الترقيم عند كتابة كلمة بحث جديدة
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * إعادة ضبط الترقيم عند تغيير فلتر نوع المنشأة
     */
    public function updatedFilterType(): void
    {
        $this->resetPage();
    }

    /**
     * إظهار أو إخفاء كارت نموذج إدخال/تعديل البيانات
     */
    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetFields();
        }
    }

    /**
     * تفريغ وإعادة تعيين مدخلات النموذج وإغلاقه
     */
    public function resetFields(): void
    {
        $this->name = '';
        $this->type = 'مستشفى';
        $this->district_id = null;
        $this->sector_id = null;
        $this->is_active = true;
        $this->hospital_id = null;
        $this->isEdit = false;
        $this->showForm = false;
        $this->resetValidation();
    }

    /**
     * حفظ جهة صحية جديدة في قاعدة البيانات بأسمائها العربية
     */
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

    /**
     * جلب بيانات جهة معينة وتعبئتها في النموذج للتعديل
     */
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

    /**
     * تحديث بيانات الجهة الصحية في قاعدة البيانات
     */
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

    /**
     * حذف جهة صحية من قاعدة البيانات
     */
    public function delete(int $id): void
    {
        Facility::findOrFail($id)->delete();
        session()->flash('message', 'تم حذف المنشأة بنجاح');
    }

    /**
     * تغيير حالة تفعيل الجهة (مفعل / معطل)
     */
    public function toggleStatus(int $id): void
    {
        $facility = Facility::findOrFail($id);
        $facility->update(['is_active' => !$facility->is_active]);
    }

    /**
     * عرض الصفحة واستعلام البيانات
     */
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
}
