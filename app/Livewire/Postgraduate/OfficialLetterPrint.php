<?php

namespace App\Livewire\Postgraduate;

use Livewire\Component;
use App\Models\HealthProfessional;
use App\Models\Setting;
use App\Models\OfficialHeader;

class OfficialLetterPrint extends Component
{
    public $search_national_id = '';
    public $candidate = null;
    public $latestRegistration = null;
    public $settings;
    public $officialHeader;
    public $searched = false; // <-- تعريف المتغير هنا لمنع خطأ Undefined variable

    public function mount()
    {
        // جلب الإعدادات العامة
        $this->settings = Setting::first();

        // جلب بيانات التوقيعات والفوتر
        $this->officialHeader = OfficialHeader::first();
    }

    public function updatedSearchNationalId($value)
    {
        $value = trim($value);
        if (strlen($value) === 14) {
            $this->searched = true; // <-- تفعيل حالة البحث عند اكتمال الرقم
            $this->candidate = HealthProfessional::with([
                'facility',
                'qualification',
                'medicalMovements' => function($q) {
                    $q->latest();
                },
                'postgraduateRegistrations' => function($q) {
                    $q->latest();
                }
            ])->where('national_id', $value)->first();

            if ($this->candidate) {
                $this->latestRegistration = $this->candidate->postgraduateRegistrations->first();
            } else {
                $this->latestRegistration = null;
            }
        } else {
            $this->searched = false; // <-- إعادة ضبطه إذا لم يكتمل الرقم
            $this->candidate = null;
            $this->latestRegistration = null;
        }
    }

    public function render()
    {
        return view('livewire.postgraduate.official-letter-print')
            ->layout('layouts.app');
    }
}
