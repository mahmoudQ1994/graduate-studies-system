<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class JobTitlesImport implements ToCollection
{
    // دالة متطورة لتنظيف وتوحيد النصوص (إزالة الـ التعريف من كل الكلمات، توحيد الهمزات، وإزالة المسافات)
    private function cleanText($text)
    {
        $text = trim($text);

        // إزالة الـ التعريف من بداية أي كلمة في النص (سواء الكلمة الأولى أو الثانية)
        $text = preg_replace('/\bال/u', '', $text);

        // توحيد الهمزات (أ، إ، آ -> ا) وتخفيف التاء المربوطة والهاء إن أمكن أو توحيدها
        $text = str_replace(['أ', 'إ', 'آ'], 'ا', $text);

        // إزالة المسافات الزائدة
        return preg_replace('/\s+/', ' ', $text);
    }

    public function collection(Collection $rows)
    {
        $previewData = [];
        $duplicatesCount = 0;

        // جلب كل الوظائف الموجودة مسبقاً في قاعدة البيانات وتطهيرها للمقارنة
        $existingJobs = DB::table('job_titles')->pluck('name')->toArray();
        $cleanedExistingJobs = array_map([$this, 'cleanText'], $existingJobs);

        foreach ($rows as $index => $row) {
            $firstCell = trim($row[0] ?? '');
            if ($index === 0 && (str_contains($firstCell, 'اسم') || str_contains(strtolower($firstCell), 'name'))) {
                continue;
            }

            $jobName = trim($row[0] ?? '');
            if (empty($jobName) && isset($row[1])) {
                $jobName = trim($row[1]);
            }

            if (empty($jobName)) {
                continue;
            }

            $cleanedJobName = $this->cleanText($jobName);

            // التحقق هل الوظيفة موجودة مسبقاً في قاعدة البيانات
            $existsInDb = in_array($cleanedJobName, $cleanedExistingJobs);

            // التحقق هل تم تكرارها داخل نفس ملف الإكسيل المرفوع
            $existsInPreview = collect($previewData)->contains(function ($item) use ($cleanedJobName) {
                return $this->cleanText($item['name']) === $cleanedJobName;
            });

            if ($existsInDb || $existsInPreview) {
                $duplicatesCount++;
                continue; // استبعاد الوظيفة المتشابهة
            }

            $previewData[] = [
                'name' => $jobName,
                'status' => 'جديدة ومتاحة للاستيراد'
            ];
        }

        session()->put('import_preview', $previewData);
        session()->put('import_duplicates_count', $duplicatesCount);
    }
}
