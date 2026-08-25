<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Setting;
use App\Models\OfficialHeader;
use App\Models\Department;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SiteSetting extends Component
{
    use WithFileUploads;

    // التبويب النشط حالياً
    public $activeTab = 'identity';

    // 1. الإعدادات العامة
    public $ministry_name;
    public $governorate_name;
    public $directorate_name;
    public $administration_name;
    public $department_name;
    public $logo;
    public $existing_logo;

    // 2. العام المالي
    public $fiscal_year;
    public $fiscal_year_start;
    public $fiscal_year_end;

    // 3. بيانات الترويسة والطباعة
    public $department_id;
    public $manager_name;
    public $undersecretary_name;
    public $phone_fax;
    public $official_email;
    public $detailed_address;

    public function mount()
    {
        $setting = Setting::first();
        if ($setting) {
            $this->ministry_name = $setting->ministry_name;
            $this->governorate_name = $setting->governorate_name;
            $this->directorate_name = $setting->directorate_name;
            $this->administration_name = $setting->administration_name;
            $this->department_name = $setting->department_name;
            $this->existing_logo = $setting->logo_path;
        }

        $header = OfficialHeader::first();
        if ($header) {
            $this->department_id = $header->department_id;
            $this->manager_name = $header->manager_name;
            $this->undersecretary_name = $header->undersecretary_name;
            $this->phone_fax = $header->phone_fax;
            $this->official_email = $header->official_email;
            $this->detailed_address = $header->detailed_address;
        }

        $this->calculateFiscalYear();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function calculateFiscalYear()
    {
        $today = Carbon::now();
        $currentYear = $today->year;

        if ($today->month >= 7) {
            $startYear = $currentYear;
            $endYear = $currentYear + 1;
        } else {
            $startYear = $currentYear - 1;
            $endYear = $currentYear;
        }

        $this->fiscal_year = $startYear . ' / ' . $endYear;
        $this->fiscal_year_start = $startYear . '-07-01';
        $this->fiscal_year_end = $endYear . '-06-30';
    }

    public function save()
    {
        $this->validate([
            'ministry_name' => 'required|string|max:255',
            'governorate_name' => 'required|string|max:255',
            'directorate_name' => 'required|string|max:255',
            'administration_name' => 'required|string|max:255',
            'department_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',

            'department_id' => 'nullable|exists:departments,id',
            'manager_name' => 'nullable|string|max:255',
            'undersecretary_name' => 'nullable|string|max:255',
            'phone_fax' => 'nullable|string|max:255',
            'official_email' => 'nullable|email|max:255',
            'detailed_address' => 'nullable|string|max:500',
        ]);

        $setting = Setting::first() ?? new Setting();
        $setting->ministry_name = $this->ministry_name;
        $setting->governorate_name = $this->governorate_name;
        $setting->directorate_name = $this->directorate_name;
        $setting->administration_name = $this->administration_name;
        $setting->department_name = $this->department_name;
        $setting->fiscal_year = $this->fiscal_year;
        $setting->fiscal_year_start = $this->fiscal_year_start;
        $setting->fiscal_year_end = $this->fiscal_year_end;

        if ($this->logo) {
            if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $setting->logo_path = $this->logo->store('settings', 'public');
            $this->existing_logo = $setting->logo_path;
        }
        $setting->save();

        OfficialHeader::updateOrCreate(
            ['id' => 1],
            [
                'department_id' => $this->department_id,
                'manager_name' => $this->manager_name,
                'undersecretary_name' => $this->undersecretary_name,
                'phone_fax' => $this->phone_fax,
                'official_email' => $this->official_email,
                'detailed_address' => $this->detailed_address,
            ]
        );

        session()->flash('success', 'تم حفظ جميع الإعدادات بنجاح!');
    }

    public function render()
    {
        return view('livewire.settings.site-settings', [
            'departments' => Department::all()
        ])->layout('layouts.app');
    }
}
