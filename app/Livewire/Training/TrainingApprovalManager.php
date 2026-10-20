<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TrainingProgram;
use App\Models\TrainingProgramApproval;
use Illuminate\Support\Facades\Auth;
use App\Models\RejectionReason;

class TrainingApprovalManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $selectedProgramId = null;
    public $actionStatus = 'approved'; // approved أو rejected

    public $rejection_reason_id = null;
    public $notes = '';
    public $viewingProgram = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // فتح نافذة اتخاذ القرار للبرنامج
    public $currentProgram = null;

    public function openApprovalModal($id)
    {
        $this->currentProgram = TrainingProgram::findOrFail($id);
        $this->selectedProgramId = $id;
        $this->notes = '';
        $this->rejection_reason_id = null;
        $this->actionStatus = 'approved';
    }

    public function submitApproval()
    {
        $this->validate([
            'actionStatus' => 'required|in:approved,rejected',
            'rejection_reason_id' => 'required_if:actionStatus,rejected|nullable|exists:rejection_reasons,id',
            'notes' => 'nullable|string|max:500', // <-- جعلناه قابلاً للترك فارغاً (nullable) بشكل تام
        ], [
            'rejection_reason_id.required_if' => 'يرجى اختيار سبب الرفض الأساسي من القائمة.',
        ]);

        if (!$this->selectedProgramId) {
            return;
        }

        $program = TrainingProgram::findOrFail($this->selectedProgramId);

        $program->update([
            'approval_status' => $this->actionStatus,
            'updated_by' => Auth::id(),
        ]);

        TrainingProgramApproval::create([
            'training_program_id' => $program->id,
            'user_id' => Auth::id(),
            'role_stage' => 'committee_approver',
            'status' => $this->actionStatus,
            'rejection_reason_id' => $this->actionStatus == 'rejected' ? $this->rejection_reason_id : null,
            'notes' => $this->notes, // يتم تخزين الملاحظات الإضافية إن وجدت، أو تترك فارغة
            'action_at' => now(),
        ]);

        session()->flash('message', 'تم تحديث حالة اعتماد البرنامج التدريبي بنجاح.');

        $this->dispatch('close-approval-modal');
        $this->selectedProgramId = null;
        $this->currentProgram = null;
        $this->reset(['notes', 'actionStatus', 'rejection_reason_id']);
        $this->resetPage();
    }

    public function showProgramDetails($id)
    {
        $this->viewingProgram = TrainingProgram::with([
            'programType',
            'hall',
            'creator',
            'approvals.user',
            'approvals.rejectionReason'
        ])->findOrFail($id);
    }

    public function render()
    {
        $programs = TrainingProgram::query()
            ->with([
                'programType',
                'hall',
                'creator',
                'approvals' => function($q) {
                    $q->latest('action_at');
                },
                'approvals.user'
            ])
            ->when($this->search, function ($query) {
                $query->where('name_ar', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.training.training-approval-manager', [
            'programs' => $programs,
            'rejectionReasons' => RejectionReason::all(),
        ])->layout('layouts.app', ['title' => 'اعتمادات وموافقات البرامج التدريبية']);
    }
}
