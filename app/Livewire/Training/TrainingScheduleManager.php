<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TrainingProgram;
use App\Models\TrainingProgramSchedule;
use App\Models\ProgramScheduleTrainer;
use App\Models\TrainingHall;
use App\Models\TrainingProgramType;
use App\Models\Training\Trainer; // استيراد نموذج المحاضرين
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Exports\TrainingSchedulesExport;
use Maatwebsite\Excel\Facades\Excel;

class TrainingScheduleManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // متغير لتحديد التبويب النشط حالياً
    public $activeTab = 1;

    // متغيرات الفلاتر في أعلى الشاشة
    public $search = '';
    public $filter_from_date = '';
    public $filter_to_date = '';
    public $filter_program_type = '';
    public $filter_status = '';
    public $filter_trainer = '';
    public $filter_hall = '';

    // المتغيرات الخاصة بنافذة الجدولة والتنشيط
    public $programId;
    public $selectedProgram;

    public $hall_id;
    public $start_date;
    public $end_date;
    public $time_slot = 'صباحي';
    public $max_capacity = 25;
    public $schedule_status = 'draft';
    public $notes;

    // مصفوفة إدارة المحاضرين المتعددين وحقول البحث الخاصة بهم
    public $trainers = [];
    public $trainerSearch = [];

    // قائمة التواريخ المتاحة للبرنامج بناءً على تاريخ البداية والنهاية واستبعاد الجمعة
    public $availableDates = [];

    protected $queryString = [
        'search', 'filter_from_date', 'filter_to_date',
        'filter_program_type', 'filter_status', 'filter_trainer', 'filter_hall'
    ];

    public function setTab($tabNumber)
    {
        $this->activeTab = $tabNumber;
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterFromDate() { $this->resetPage(); }
    public function updatingFilterToDate() { $this->resetPage(); }
    public function updatingFilterProgramType() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterTrainer() { $this->resetPage(); }
    public function updatingFilterHall() { $this->resetPage(); }

    // زر إعادة تعيين الفلاتر
    public function resetFilters()
    {
        $this->reset([
            'search', 'filter_from_date', 'filter_to_date',
            'filter_program_type', 'filter_status', 'filter_trainer', 'filter_hall'
        ]);
    }

    // دالة تصدير البيانات إلى ملف Excel
    public function exportExcel()
    {
        $programs = TrainingProgram::with(['programType', 'hall', 'schedule.hall', 'schedule.trainers'])
            ->where('approval_status', 'approved')
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('name_ar', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filter_program_type, function ($query) {
                $query->where('program_type_id', $this->filter_program_type);
            })
            ->whereHas('schedule', function ($query) {
                $query->when($this->filter_from_date, function ($q) {
                    $q->where('start_date', '>=', $this->filter_from_date);
                })
                ->when($this->filter_to_date, function ($q) {
                    $q->where('end_date', '<=', $this->filter_to_date);
                })
                ->when($this->filter_status, function ($q) {
                    $q->where('schedule_status', $this->filter_status);
                })
                ->when($this->filter_hall, function ($q) {
                    $q->where('hall_id', $this->filter_hall);
                })
                ->when($this->filter_trainer, function ($q) {
                    $q->whereHas('trainers', function ($tq) {
                        $tq->where('trainer_name', 'like', '%' . $this->filter_trainer . '%');
                    });
                });
            })
            ->latest()
            ->get();

        $filename = "training_schedules_" . date('Y-m-d') . ".xlsx";

        return Excel::download(new TrainingSchedulesExport($programs), $filename);
    }

    // المراقب الحركي لـ Livewire عند تغيير تاريخ البداية
    public function updatedStartDate($value)
    {
        if ($value && $this->selectedProgram) {
            $this->calculateEndDate();
        }
    }

    // دالة حساب تاريخ النهاية واستبعاد أيام الجمعة وتوليد التواريخ المتاحة
    public function calculateEndDate()
    {
        if (!$this->start_date || !$this->selectedProgram) {
            return;
        }

        $durationDays = (int) $this->selectedProgram->duration_days;
        if ($durationDays <= 0) {
            $durationDays = 1;
        }

        $currentDate = Carbon::parse($this->start_date);
        $addedDays = 0;
        $this->availableDates = [];

        while ($addedDays < $durationDays) {
            // استبعاد يوم الجمعة
            if (!$currentDate->isFriday()) {
                $this->availableDates[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'formatted' => $currentDate->format('Y-m-d') . ' (' . $this->getArabicDayName($currentDate->dayOfWeek) . ')'
                ];
                $addedDays++;
            }

            if ($addedDays < $durationDays) {
                $currentDate->addDay();
            }
        }

        $this->end_date = $currentDate->format('Y-m-d');
    }

    // ترجمة أسماء الأيام للغة العربية
    private function getArabicDayName($dayOfWeek)
    {
        $days = [
            Carbon::SUNDAY => 'الأحد',
            Carbon::MONDAY => 'الإثنين',
            Carbon::TUESDAY => 'الثلاثاء',
            Carbon::WEDNESDAY => 'الأربعاء',
            Carbon::THURSDAY => 'الخميس',
            Carbon::FRIDAY => 'الجمعة',
            Carbon::SATURDAY => 'السبت',
        ];

        return $days[$dayOfWeek] ?? '';
    }

    // جلب المحاضرين المطابقين للبحث في صفوف جدول المحاضرين
    public function getMatchingTrainersForIndex($index)
    {
        $search = $this->trainerSearch[$index] ?? '';

        if (strlen(trim($search)) < 1) {
            return collect();
        }

        return Trainer::where('status', 'approved')
            ->where(function($q) use ($search) {
                $q->where('name_ar', 'like', '%' . $search . '%')
                  ->orWhere('national_id', 'like', '%' . $search . '%');
            })
            ->limit(5)
            ->get();
    }

    // اختيار المحاضر من القائمة المنبثقة للبحث
    public function selectTrainerForRow($index, $name)
    {
        $this->trainers[$index]['trainer_name'] = $name;
        $this->trainerSearch[$index] = $name;
    }

    // فتح نافذة الجدولة لبرنامج معين
    public function openScheduleModal($id)
    {
        $this->programId = $id;
        $this->selectedProgram = TrainingProgram::with('schedule.trainers')->findOrFail($id);
        $this->activeTab = 1;

        $schedule = $this->selectedProgram->schedule;

        if ($schedule) {
            $this->hall_id = $schedule->hall_id;
            $this->start_date = $schedule->start_date;
            $this->end_date = $schedule->end_date;
            $this->time_slot = $schedule->time_slot;
            $this->max_capacity = $schedule->max_capacity;
            $this->schedule_status = $schedule->schedule_status;
            $this->notes = $schedule->notes;

            $this->calculateEndDate();

            $this->trainers = $schedule->trainers->map(function($t) {
                return [
                    'id' => $t->id,
                    'trainer_name' => $t->trainer_name,
                    'assigned_days' => is_array($t->assigned_days) ? $t->assigned_days : explode(',', $t->assigned_days),
                    'session_topics' => $t->session_topics,
                ];
            })->toArray();
        } else {
            $this->hall_id = $this->selectedProgram->hall_id;
            $this->start_date = now()->format('Y-m-d');
            $this->time_slot = 'صباحي';
            $this->max_capacity = 25;
            $this->schedule_status = 'draft';
            $this->notes = '';

            $this->calculateEndDate();

            $this->trainers = [
                [
                    'id' => null,
                    'trainer_name' => $this->selectedProgram->consultant_name ?? '',
                    'assigned_days' => array_column($this->availableDates, 'date'),
                    'session_topics' => 'المحتوى العلمي العام'
                ]
            ];
        }

        // تهيئة حقول البحث للمحاضرين
        $this->trainerSearch = collect($this->trainers)->mapWithKeys(function ($t, $index) {
            return [$index => $t['trainer_name']];
        })->toArray();

        $this->dispatch('show-schedule-modal');
    }

    public function addTrainerRow()
    {
        $newIndex = count($this->trainers);
        $this->trainers[] = [
            'id' => null,
            'trainer_name' => '',
            'assigned_days' => [],
            'session_topics' => ''
        ];
        $this->trainerSearch[$newIndex] = '';
    }

    public function removeTrainerRow($index)
    {
        unset($this->trainers[$index]);
        unset($this->trainerSearch[$index]);
        $this->trainers = array_values($this->trainers);
        $this->trainerSearch = array_values($this->trainerSearch);
    }

    public function saveSchedule()
    {
        $this->validate([
            'hall_id' => 'required|exists:training_halls,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'max_capacity' => 'required|integer|min:1',
            'schedule_status' => 'required|in:draft,active,in_progress,completed,cancelled',
            'trainers' => 'required|array|min:1',
            'trainers.*.trainer_name' => 'required|string|max:255',
        ]);

        $schedule = TrainingProgramSchedule::updateOrCreate(
            ['training_program_id' => $this->programId],
            [
                'hall_id' => $this->hall_id,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'time_slot' => $this->time_slot,
                'max_capacity' => $this->max_capacity,
                'schedule_status' => $this->schedule_status,
                'notes' => $this->notes,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]
        );

        $schedule->trainers()->delete();

        foreach ($this->trainers as $trainerData) {
            if (!empty($trainerData['trainer_name'])) {
                $assignedDaysString = is_array($trainerData['assigned_days'])
                    ? implode(',', $trainerData['assigned_days'])
                    : $trainerData['assigned_days'];

                ProgramScheduleTrainer::create([
                    'training_program_schedule_id' => $schedule->id,
                    'trainer_name' => $trainerData['trainer_name'],
                    'assigned_days' => $assignedDaysString,
                    'session_topics' => $trainerData['session_topics'] ?? null,
                ]);
            }
        }

        session()->flash('message', 'تم حفظ الجدولة وتنشيط البرنامج بنجاح.');
        $this->dispatch('close-schedule-modal');
    }

    public function render()
    {
        $programs = TrainingProgram::query()
            ->with(['programType', 'hall', 'schedule.hall', 'schedule.trainers'])
            ->where('approval_status', 'approved')
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('name_ar', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filter_program_type, function ($query) {
                $query->where('program_type_id', $this->filter_program_type);
            })
            ->where(function ($query) {
                $query->whereHas('schedule', function ($q) {
                    $q->when($this->filter_from_date, function ($subQ) {
                        $subQ->where('start_date', '>=', $this->filter_from_date);
                    })
                    ->when($this->filter_to_date, function ($subQ) {
                        $subQ->where('end_date', '<=', $this->filter_to_date);
                    })
                    ->when($this->filter_status, function ($subQ) {
                        $subQ->where('schedule_status', $this->filter_status);
                    })
                    ->when($this->filter_hall, function ($subQ) {
                        $subQ->where('hall_id', $this->filter_hall);
                    })
                    ->when($this->filter_trainer, function ($subQ) {
                        $subQ->whereHas('trainers', function ($tq) {
                            $tq->where('trainer_name', 'like', '%' . $this->filter_trainer . '%');
                        });
                    });
                });

                if (empty($this->filter_from_date) && empty($this->filter_to_date) && empty($this->filter_status) && empty($this->filter_hall) && empty($this->filter_trainer)) {
                    $query->orWhereDoesntHave('schedule');
                }
            })
            ->latest()
            ->paginate(10);

        $halls = TrainingHall::all();
        $programTypes = TrainingProgramType::all();

        return view('livewire.training.training-schedule-manager', [
            'programs' => $programs,
            'halls' => $halls,
            'programTypes' => $programTypes,
        ])->layout('layouts.app');
    }
}
