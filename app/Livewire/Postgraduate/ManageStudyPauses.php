<?php

namespace App\Livewire\Postgraduate;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\RegistrationPause;
use App\Models\PostgraduateRegistration;

class ManageStudyPauses extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search_national_id = '';
    public $search_name = '';

    public $modal_national_id = '';
    public $selectedRegistration = null;
    public $registration_id = null;
    public $pause_start_date;
    public $pause_end_date;
    public $resume_date;
    public $pause_reason;
    public $notes;
    public $selectedPauseId = null;

    protected $rules = [
        'registration_id'  => 'required|exists:postgraduate_registrations,id',
        'pause_start_date' => 'required|date',
        'pause_end_date'   => 'nullable|date|after_or_equal:pause_start_date',
        'resume_date'      => 'nullable|date|after_or_equal:pause_start_date',
        'pause_reason'     => 'required|string|max:255',
        'notes'            => 'nullable|string',
    ];

    protected $messages = [
        'registration_id.required'   => 'برجاء اختيار مرشح عن طريق إدخال الرقم القومي الصحيح.',
        'pause_start_date.required'  => 'تاريخ بداية الإيقاف مطلوب.',
        'pause_reason.required'      => 'سبب الإيقاف مطلوب.',
    ];

    public function updatedModalNationalId($value)
    {
        $this->selectedRegistration = null;
        $this->registration_id = null;

        if (strlen($value) >= 14) {
            $registration = PostgraduateRegistration::with(['healthProfessional'])
                ->whereHas('healthProfessional', function ($q) use ($value) {
                    $q->where('national_id', $value);
                })->latest()->first();

            if ($registration) {
                $this->selectedRegistration = $registration;
                $this->registration_id = $registration->id;
            }
        }
    }

    public function openAddModal($regId = null, $pauseId = null)
    {
        $this->resetValidation();
        $this->reset([
            'modal_national_id', 'selectedRegistration', 'registration_id',
            'pause_start_date', 'pause_end_date', 'resume_date',
            'pause_reason', 'notes', 'selectedPauseId'
        ]);

        if ($pauseId) {
            $pause = RegistrationPause::with('registration.healthProfessional')->find($pauseId);
            if ($pause) {
                $this->selectedPauseId = $pause->id;
                $this->registration_id = $pause->postgraduate_registration_id;
                $this->selectedRegistration = $pause->registration;
                $this->modal_national_id = $pause->registration->healthProfessional->national_id ?? '';
                $this->pause_start_date = $pause->pause_start_date;
                $this->pause_end_date = $pause->pause_end_date;
                $this->resume_date = $pause->resume_date;
                $this->pause_reason = $pause->pause_reason;
                $this->notes = $pause->notes;
            }
        } elseif ($regId) {
            $reg = PostgraduateRegistration::with(['healthProfessional'])->find($regId);
            if ($reg) {
                $this->selectedRegistration = $reg;
                $this->registration_id = $reg->id;
                $this->modal_national_id = $reg->healthProfessional->national_id ?? '';
            }
        }

        $this->dispatch('open-modal', name: 'pauseModal');
    }

    public function savePause()
    {
        $this->validate();

        // جلب تفاصيل التسجيل للتحقق من تواريخ الانتهاء
        $registration = PostgraduateRegistration::find($this->registration_id);

        if ($registration) {
            $endDate = $this->pause_end_date ?? $this->pause_start_date;

            // 1. التحقق من تاريخ الحصول على الدرجة
            if (!empty($registration->nominated_degree_date)) {
                if ($this->pause_start_date > $registration->nominated_degree_date || $endDate > $registration->nominated_degree_date) {
                    $this->addError('pause_start_date', 'عذراً، فترة إيقاف القيد تقع كلياً أو جزئياً بعد تاريخ الحصول على الدرجة (' . $registration->nominated_degree_date . ').');
                    return;
                }
            }

            // 2. التحقق من تاريخ الاعتذار
            if (!empty($registration->apology_date)) {
                if ($this->pause_start_date > $registration->apology_date || $endDate > $registration->apology_date) {
                    $this->addError('pause_start_date', 'عذراً، فترة إيقاف القيد تقع كلياً أو جزئياً بعد تاريخ الاعتذار (' . $registration->apology_date . ').');
                    return;
                }
            }
        }

        RegistrationPause::updateOrCreate(
            ['id' => $this->selectedPauseId],
            [
                'postgraduate_registration_id' => $this->registration_id,
                'pause_start_date'             => $this->pause_start_date,
                'pause_end_date'               => $this->pause_end_date,
                'resume_date'                  => $this->resume_date,
                'pause_reason'                 => $this->pause_reason,
                'notes'                        => $this->notes,
                'user_id' => auth()->id(),
            ]
        );

        session()->flash('success', 'تم حفظ فترة إيقاف القيد بنجاح.');
        $this->dispatch('close-modal', name: 'pauseModal');
    }

    public function deletePause($id)
    {
        RegistrationPause::findOrFail($id)->delete();
        session()->flash('success', 'تم الحذف بنجاح.');
    }

    public function render()
    {
        $pauses = RegistrationPause::with(['registration.healthProfessional'])
            ->when($this->search_national_id, function ($query) {
                $query->whereHas('registration.healthProfessional', function ($q) {
                    $q->where('national_id', 'like', '%' . $this->search_national_id . '%');
                });
            })
            ->when($this->search_name, function ($query) {
                $query->whereHas('registration.healthProfessional', function ($q) {
                    $q->where('name', 'like', '%' . $this->search_name . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.postgraduate.manage-study-pauses', ['pauses' => $pauses])->layout('layouts.app');
    }
}
