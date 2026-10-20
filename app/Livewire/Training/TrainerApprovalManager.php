<?php

namespace App\Livewire\Training;

use App\Models\Training\Trainer;
use Livewire\Component;
use Livewire\WithPagination;

class TrainerApprovalManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $selectedTrainer = null; // لعرض تفاصيل سجل التدقيق لمدرب معين
    public $rejectionReason = '';
    public $showRejectModal = false;

    // تحديث البحث لإعادة ضبط الصفحة
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // اعتماد المدرب
    public function approve($id)
    {
        $trainer = Trainer::findOrFail($id);
        $trainer->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approval_date' => now(),
            'rejection_reason' => null
        ]);

        session()->flash('success', 'تم اعتماد المدرب بنجاح.');
    }

    // فتح نافذة إدخال سبب الرفض (تم تصحيح الخطأ الإملائي هنا)
    public function openRejectModal($id)
    {
        $this->selectedTrainer = Trainer::findOrFail($id);
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    // تأكيد رفض المدرب
    public function reject()
    {
        if ($this->selectedTrainer) {
            $this->selectedTrainer->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approval_date' => now(),
                'rejection_reason' => $this->rejectionReason
            ]);

            $this->showRejectModal = false;
            session()->flash('success', 'تم رفض اعتماد المدرب.');
        }
    }

    // عرض تفاصيل التدقيق وسجل الإجراءات لمدرب معين
    public function viewAuditLog($id)
    {
        $this->selectedTrainer = Trainer::with(['creator', 'approver', 'editor'])->findOrFail($id);
    }

    public function render()
    {
        $query = Trainer::with(['creator', 'approver', 'editor', 'jobGrade', 'specialization']);

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name_ar', 'like', '%' . $this->search . '%')
                  ->orWhere('national_id', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.training.trainer-approval-manager', [
            'trainers' => $query->latest()->paginate(10)
        ])->layout('layouts.app');
    }
}
