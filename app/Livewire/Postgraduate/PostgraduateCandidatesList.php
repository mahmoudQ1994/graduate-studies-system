<?php

namespace App\Livewire\Postgraduate;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PostgraduateRegistration;

class PostgraduateCandidatesList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // متغيرات البحث والفلترة المطابقة للجدول الحقيقي
    public $searchName = '';
    public $searchNationalId = '';
    public $filterDegree = '';
    public $filterSponsorship = '';

    public function updatingSearchName() { $this->resetPage(); }
    public function updatingSearchNationalId() { $this->resetPage(); }
    public function updatingFilterDegree() { $this->resetPage(); }
    public function updatingFilterSponsorship() { $this->resetPage(); }

    public function resetFilters()
    {
        $this->reset(['searchName', 'searchNationalId', 'filterDegree', 'filterSponsorship']);
        $this->resetPage();
    }

    public function deleteCandidate($id)
    {
        try {
            $registration = PostgraduateRegistration::find($id);

            if ($registration) {
                // يمكنك اختيار ما إذا كنت تريد حذف سجل التسجيل فقط أو حذف الممارس الصحي المرتبط به بالكامل
                // $registration->healthProfessional()?->delete(); // إذا أردت حذف الكادر الطبي السجل بالكامل
                $registration->delete(); // حذف طلب التسجيل الحالي

                session()->flash('success', 'تم حذف بيانات المرشح بنجاح.');
            } else {
                session()->flash('error', 'عذراً، لم يتم العثور على بيانات المرشح المطلوب.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'فشلت عملية الحذف لوجود بيانات مرتبطة بهذا السجل.');
        }
    }

    public function render()
    {
        // جلب البيانات مع ربط جدول الكادر الطبي جهة العمل
        $query = PostgraduateRegistration::with(['healthProfessional.facility']);

        // البحث بالاسم (من جدول الكادر الطبي المرتبط)
        if (!empty($this->searchName)) {
            $query->whereHas('healthProfessional', function ($q) {
                $q->where('name', 'like', '%' . $this->searchName . '%');
            });
        }

        // البحث بالرقم القومي (من جدول الكادر الطبي المرتبط)
        if (!empty($this->searchNationalId)) {
            $query->whereHas('healthProfessional', function ($q) {
                $q->where('national_id', 'like', '%' . $this->searchNationalId . '%');
            });
        }

        // فلتر نوع الدراسة (العمود الحقيقي: required_degree)
        if (!empty($this->filterDegree)) {
            $query->where('required_degree', $this->filterDegree);
        }

        // فلتر نوع الترشيح / التمويل (العمود الحقيقي: sponsorship_type)
        if (!empty($this->filterSponsorship)) {
            $query->where('sponsorship_type', $this->filterSponsorship);
        }

        $candidates = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.postgraduate.postgraduate-candidates-list', [
            'candidates' => $candidates,
        ])->layout('layouts.app', ['title' => 'قائمة المرشحين بالدراسات العليا']);
    }
}
