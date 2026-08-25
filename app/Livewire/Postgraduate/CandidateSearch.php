<?php

namespace App\Livewire\Postgraduate;

use Livewire\Component;
use App\Models\HealthProfessional;

class CandidateSearch extends Component
{
    public string $search_national_id = '';
    public string $search_name = '';

    public function updatingSearchNationalId()
    {
        if (!empty($this->search_national_id)) {
            $this->search_name = '';
        }
    }

    public function updatingSearchName()
    {
        if (!empty($this->search_name)) {
            $this->search_national_id = '';
        }
    }

    public function resetResults()
    {
        $this->reset(['search_national_id', 'search_name']);
    }

    public function render()
    {
        $candidate = null;
        $searched = false;

        if (!empty($this->search_national_id) && strlen(trim($this->search_national_id)) >= 3) {
            $searched = true;
            $candidate = HealthProfessional::with([
                'facility.district',
                'qualification',
                'postgraduateRegistrations.studyLeaves'
            ])->where('national_id', 'like', '%' . trim($this->search_national_id) . '%')->first();
        } elseif (!empty($this->search_name) && strlen(trim($this->search_name)) >= 3) {
            $searched = true;
            $candidate = HealthProfessional::with([
                'facility.district',
                'qualification',
                'postgraduateRegistrations.studyLeaves'
            ])->where('name', 'like', '%' . trim($this->search_name) . '%')->first();
        }

        return view('livewire.postgraduate.candidate-search', [
            'candidate' => $candidate,
            'searched' => $searched,
        ])->layout('layouts.app');
    }
}
