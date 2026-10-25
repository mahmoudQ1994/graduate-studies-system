<div>
    <div class="container-fluid py-3">
        <!-- العنوان الرئيسي وأزرار التحكم -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-primary mb-0">
                <i class="bi bi-people-fill me-1"></i> إدارة المتدربين والبيانات الوظيفية
            </h5>
            <div>
                <button type="button" wire:click="openTraineeModal" class="btn btn-primary btn-sm">
                    <i class="bi bi-person-plus-fill me-1"></i> إضافة متدرب جديد
                </button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- كارد فلاتر البحث المتقدم -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
            <div class="card-header bg-light fw-bold text-secondary py-2 border-bottom">
                <i class="bi bi-funnel me-1 text-primary"></i> بحث وفلترة المتدربين
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">بحث بالاسم أو الرقم القومي أو الموبايل:</label>
                        <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="اكتب للبحث...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">جهة العمل:</label>
                        <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="filter_workplace" placeholder="فلترة بجهة العمل...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">المؤهل:</label>
                        <select class="form-select form-select-sm" wire:model.live.debounce.300ms="filter_qualification">
                            <option value="">-- اختر المؤهل --</option>
                            @foreach($specialties ?? [] as $spec)
                                <option value="{{ $spec->name }}">{{ $spec->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> إعادة تعيين
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول العرض الرئيسي للمتدربين -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <span class="fw-bold text-secondary">
                    <i class="bi bi-table me-1 text-primary"></i> قائمة المتدربين المسجلين
                </span>
                <span class="badge bg-primary px-3 py-2" style="font-size: 0.85rem;">
                    إجمالي النتائج: {{ $trainees->total() }}
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-hover table-striped mb-0 text-center align-middle" style="white-space: nowrap; font-size: 0.88rem;">
                        <thead class="table-light text-secondary sticky-top" style="z-index: 10;">
                            <tr style="height: 45px;">
                                <th class="py-3">م</th>
                                <th class="py-3 text-start">الاسم</th>
                                <th class="py-3">الوظيفة</th>
                                <th class="py-3">المؤهل</th>
                                <th class="py-3">الإدارة</th>
                                <th class="py-3">جهة العمل</th>
                                <th class="py-3">الرقم القومي</th>
                                <th class="py-3">الايميل</th>
                                <th class="py-3">رقم الموبايل</th>
                                <th class="py-3">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trainees as $index => $trainee)
                                <tr wire:key="trainee-row-{{ $trainee->id }}" style="height: 50px;">
                                    <td class="text-muted fw-semibold">{{ $trainees->firstItem() + $index }}</td>
                                    <td class="fw-bold text-start text-dark">{{ $trainee->name_ar }}</td>
                                    <td>{{ $trainee->job_title ?? '---' }}</td>
                                    <td>{{ $trainee->qualification ?? '---' }}</td>
                                    <td>{{ $trainee->department ?? '---' }}</td>
                                    <td><span class="badge bg-light text-dark border px-2 py-1">{{ $trainee->workplace ?? '---' }}</span></td>
                                    <td class="font-monospace fw-bold text-secondary">{{ $trainee->national_id }}</td>
                                    <td class="text-muted">{{ $trainee->email ?? '---' }}</td>
                                    <td class="font-monospace fw-bold">{{ $trainee->phone_number ?? '---' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <button type="button" wire:click="openTraineeModal({{ $trainee->id }})" class="btn btn-outline-primary btn-sm px-2 py-1" title="تعديل">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" wire:click="deleteTrainee({{ $trainee->id }})" wire:confirm="هل أنت متأكد من حذف هذا المتدرب؟" class="btn btn-outline-danger btn-sm px-2 py-1" title="حذف">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <div class="py-4">
                                            <i class="bi bi-folder2-open display-4 text-secondary opacity-50 mb-2 d-block"></i>
                                            <span>لا توجد بيانات متدربين مطابقة لخيارات البحث.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($trainees->hasPages())
                <div class="card-footer bg-white py-3 border-top">
                    {{ $trainees->links() }}
                </div>
            @endif
        </div>

        <!-- Modal نافذة إضافة وتعديل المتدرب -->
        <div wire:ignore.self class="modal fade" id="traineeModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white py-2">
                        <h6 class="modal-title fw-bold">
                            <i class="bi bi-person-badge me-1"></i>
                            {{ $traineeId ? 'تعديل بيانات المتدرب' : 'إضافة متدرب جديد' }}
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الاسم (عربي): *</label>
                                    <input type="text" class="form-control form-control-sm" wire:model="name_ar" placeholder="الاسم ثلاثي أو رباعي">
                                    @error('name_ar') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الاسم (إنجليزي):</label>
                                    <input type="text" class="form-control form-control-sm" wire:model="name_en" placeholder="Name in English for certificates">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الرقم القومي (14 رقم): *</label>
                                    <input type="text" class="form-control form-control-sm font-monospace" wire:model="national_id" maxlength="14" placeholder="الرقم القومي">
                                    @error('national_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <!-- الوظيفة مع البحث التفاعلي (Autocomplete) -->
                                <div class="col-md-6 position-relative">
                                    <label class="form-label fw-bold">الوظيفة:</label>

                                    <!-- حقل مخفي لتخزين اسم الوظيفة المختارة -->
                                    <input type="hidden" wire:model="job_title">

                                    <!-- حقل البحث الحي -->
                                    <input type="text"
                                           class="form-control form-control-sm"
                                           wire:model.live.debounce.300ms="jobTitleSearch"
                                           placeholder="اكتب للبحث عن الوظيفة..."
                                           autocomplete="off">

                                    <!-- القائمة المنسدلة للنتائج المطابقة -->
                                    @if(!empty($this->matchingJobTitles) && $this->matchingJobTitles->isNotEmpty() && $jobTitleSearch !== $job_title)
                                        <div class="card position-absolute w-100 shadow-sm border mt-1" style="z-index: 1050; max-height: 160px; overflow-y: auto;">
                                            <ul class="list-group list-group-flush small mb-0">
                                                @foreach($this->matchingJobTitles as $job)
                                                    <li>
                                                        <button type="button"
                                                                wire:click.prevent="selectJobTitle('{{ $job->name }}')"
                                                                class="list-group-item list-group-item-action py-2 px-2 text-end">
                                                            <span class="fw-bold text-dark">{{ $job->name }}</span>
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    @error('job_title') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">المؤهل: *</label>
                                    <select class="form-select form-select-sm" wire:model="qualification">
                                        <option value="">-- اختر المؤهل --</option>
                                        @foreach($specialties ?? [] as $spec)
                                            <option value="{{ $spec->name }}">{{ $spec->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('qualification') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الإدارة:</label>
                                    <input type="text" class="form-control form-control-sm" wire:model="department" placeholder="مثال: ديوان المديرية">
                                </div>

                                <!-- جهة العمل مع البحث التفاعلي (Autocomplete) -->
                                <div class="col-md-6 position-relative">
                                    <label class="form-label fw-bold">جهة العمل: *</label>
                                    <input type="text"
                                           class="form-control form-control-sm"
                                           wire:model.live.debounce.300ms="facilitySearch"
                                           placeholder="اكتب للبحث عن المستشفى أو الإدارة..."
                                           autocomplete="off">

                                    <input type="hidden" wire:model="workplace">

                                    @if(!empty($this->facilitySearch) && ($this->facilitySearch !== $workplace) && $this->matchingFacilities->isNotEmpty())
                                        <div class="card position-absolute w-100 shadow-sm border mt-1" style="z-index: 1050; max-height: 160px; overflow-y: auto;">
                                            <ul class="list-group list-group-flush small mb-0">
                                                @foreach($this->matchingFacilities as $facility)
                                                    <li>
                                                        <button type="button"
                                                                wire:click.prevent="selectFacility('{{ $facility->name }}')"
                                                                class="list-group-item list-group-item-action py-2 px-2 text-end">
                                                            <span class="fw-bold text-dark d-block">{{ $facility->name }}</span>
                                                            <small class="text-muted" style="font-size: 0.7rem;">
                                                                النوع: {{ $facility->type ?? 'جهة حكومية' }}
                                                            </small>
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    @error('workplace') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">رقم الموبايل:</label>
                                    <input type="text" class="form-control form-control-sm font-monospace" wire:model="phone_number" placeholder="010xxxxxxxx">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">البريد الإلكتروني:</label>
                                    <input type="email" class="form-control form-control-sm" wire:model="email" placeholder="example@gmail.com">
                                    @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2 bg-white">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal">إغلاق</button>
                        <button type="button" wire:click="saveTrainee" class="btn btn-success btn-sm px-4 fw-bold">
                            <i class="bi bi-check-circle me-1"></i> حفظ البيانات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('show-trainee-modal', () => {
            var myModal = new bootstrap.Modal(document.getElementById('traineeModal'));
            myModal.show();
        });

        $wire.on('close-trainee-modal', () => {
            var myModalEl = document.getElementById('traineeModal');
            var modal = bootstrap.Modal.getInstance(myModalEl);
            if (modal) {
                modal.hide();
            }
        });
    </script>
    @endscript
</div>
