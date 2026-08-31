<?php

namespace App\Exports;

use App\Models\PostgraduateRegistration;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PostgraduateRegistrationsExport implements FromQuery, WithHeadings, WithMapping
{
    public $search_name, $search_national_id, $filter_required_degree, $filter_study_status, $filter_application_date;

    public function __construct($filters)
    {
        $this->search_name = $filters['search_name'] ?? null;
        $this->search_national_id = $filters['search_national_id'] ?? null;
        $this->filter_required_degree = $filters['filter_required_degree'] ?? null;
        $this->filter_study_status = $filters['filter_study_status'] ?? null;
        $this->filter_application_date = $filters['filter_application_date'] ?? null;
    }

    public function query()
    {
        $query = PostgraduateRegistration::with(['healthProfessional']);

        if ($this->search_name) {
            $query->whereHas('healthProfessional', fn($q) => $q->where('name', 'like', '%' . $this->search_name . '%'));
        }
        if ($this->search_national_id) {
            $query->whereHas('healthProfessional', fn($q) => $q->where('national_id', 'like', '%' . $this->search_national_id . '%'));
        }
        if ($this->filter_required_degree) {
            $query->where('required_degree', $this->filter_required_degree);
        }
        if ($this->filter_study_status) {
            $query->where('study_status', $this->filter_study_status);
        }
        if ($this->filter_application_date) {
            $query->whereDate('application_date', $this->filter_application_date);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'الاسم',
            'الرقم القومي',
            'الوظيفة',
            'الدراسة المسجل بها',
            'تخصص الدراسة المطلوبة',
            'الجامعة المطلوب الدراسة بها',
            'تاريخ تسجيل الطلب',
            'موقف تنفيذ الدراسة',
            'تاريخ القيد بالدراسة',
            'موقف الحصول على الدرجة',
            'مدة الدراسة'
        ];
    }

    public function map($registration): array
    {
        return [
            $registration->healthProfessional->name ?? '-',
            $registration->healthProfessional->national_id ?? '-',
            $registration->healthProfessional->profession ?? '-',       // تم الربط مع حقل الوظيفة الصحيح
            $registration->required_degree ?? '-',                       // الدراسة المسجل بها (دبلوم/ماجستير...)
            $registration->required_specialty ?? '-',                    // تخصص الدراسة المطلوبة
            $registration->required_university ?? '-',                   // الجامعة المطلوبة للدراسة
            $registration->application_date ?? '-',                      // تاريخ تسجيل الطلب
            $registration->study_status ?? '-',                          // موقف تنفيذ الدراسة
            $registration->registration_date ?? '-',                     // تاريخ القيد بالدراسة
            $registration->nominated_degree_status ?? '-',               // موقف الحصول على الدرجة
            $registration->years_from_registration ?? '-',               // مدة الدراسة
        ];
    }

}
