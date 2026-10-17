<?php

namespace App\Imports;

use App\Models\Facility;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class FacilitiesImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    public function model(array $row)
    {
        return null; // الاعتماد أصبح على دالة المعاينة والـ Confirm في الكلاس الرئيسي
    }
}
