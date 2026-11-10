<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Models\Trainees\Trainee;
use Maatwebsite\Excel\Concerns\ToCollection;

class TraineesImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $previewData = [];
        $duplicatesCount = 0;

        foreach ($rows as $index => $row) {
            // تخطي صف العناوين الأول
            $firstCell = trim($row[0] ?? '');
            if ($index === 0 && (str_contains($firstCell, 'اسم') || str_contains(strtolower($firstCell), 'name'))) {
                continue;
            }

            // قراءة الأعمدة بالترتيب الصحيح بناءً على تصميم الشيت الخاص بك
            $nameAr       = trim($row[0] ?? ''); // الاسم بالعربي
            $nameEn       = trim($row[1] ?? ''); // الاسم بالإنجليزي

            // معالجة الرقم القومي لضمان قراءته كنص صحيح وتجنب المشاكل العلمية
            $nationalId   = trim($row[2] ?? '');
            if (str_contains($nationalId, 'E') || str_contains($nationalId, 'e')) {
                $nationalId = number_format((float)$nationalId, 0, '', '');
            }

            $jobTitle     = trim($row[3] ?? ''); // الوظيفة
            $qualification= trim($row[4] ?? ''); // المؤهل
            $department   = trim($row[5] ?? ''); // الإدارة
            $workplace    = trim($row[6] ?? ''); // جهة العمل
            $phoneNumber  = trim($row[7] ?? ''); // رقم الموبايل
            $email        = trim($row[8] ?? ''); // البريد الإلكتروني

            // التحقق من وجود البيانات الأساسية (الاسم والرقم القومي)
            if (empty($nameAr) || empty($nationalId) || strlen($nationalId) !== 14) {
                continue; // تخطي الصفوف غير المكتملة أو التي بها أرقام قومية غير صحيحة
            }

            // فحص التكرار بالرقم القومي (في قاعدة البيانات أو داخل الملف المرفوع)
            $existsInDb = Trainee::where('national_id', $nationalId)->exists();
            $existsInPreview = collect($previewData)->contains('national_id', $nationalId);

            if ($existsInDb || $existsInPreview) {
                $duplicatesCount++;
                continue; // استبعاد المتدرب المكرر
            }

            $previewData[] = [
                'name_ar'       => $nameAr,
                'name_en'       => $nameEn,
                'national_id'   => $nationalId,
                'job_title'     => $jobTitle,
                'qualification' => $qualification,
                'department'    => $department,
                'workplace'     => $workplace,
                'phone_number'  => $phoneNumber,
                'email'         => $email,
            ];
        }

        session()->put('trainees_import_preview', $previewData);
        session()->put('trainees_import_duplicates', $duplicatesCount);
    }
}
