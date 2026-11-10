<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-header bg-primary text-white text-center py-3 rounded-top-4">
                    <h4 class="fw-bold mb-0">
                        <i class="bi bi-journal-check me-2"></i> بوابة التسجيل في البرامج التدريبية
                    </h4>
                    <p class="small mb-0 opacity-75 mt-1">أدخل رقمك القومي لاسترجاع بياناتك واختيار الدورة المناسبة</p>
                </div>
                <div class="card-body p-4 bg-light">

                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show fw-bold text-center" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show fw-bold text-center" role="alert">
                            <i class="bi bi-exclamation-octagon-fill me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session()->has('info'))
                        <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form wire:submit.prevent="submitRegistration">
                        <div class="card border-0 shadow-sm p-3 mb-4 bg-white rounded-3">
                            <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">
                                <i class="bi bi-person-vcard me-1"></i> البيانات الشخصية والوظيفية
                            </h6>
                            <div class="row g-3">
                                <!-- الرقم القومي -->
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">الرقم القومي (14 رقم): *</label>
                                    <input type="text" class="form-control font-monospace fw-bold fs-5 text-center"
                                           wire:model.live.debounce.500ms="national_id"
                                           maxlength="14"
                                           placeholder="أدخل الرقم القومي للبحث أو التسجيل"
                                           {{ $is_existing_trainee ? 'readonly' : '' }}>
                                    @error('national_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                    @if($is_existing_trainee)
                                        <small class="text-muted d-block mt-1"><i class="bi bi-lock-fill me-1"></i> الرقم القومي مسجل بالنظام ولا يمكن تعديله.</small>
                                    @endif
                                </div>

                                <!-- الاسم بالعربي -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الاسم (عربي): *</label>
                                    <input type="text" class="form-control form-control-sm" wire:model="name_ar" placeholder="الاسم رباعي">
                                    @error('name_ar') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <!-- الاسم بالإنجليزي -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الاسم (إنجليزي):</label>
                                    <input type="text" class="form-control form-control-sm" wire:model="name_en" placeholder="Name in English">
                                </div>

                                <!-- الوظيفة البحث التفاعلي -->
                                <div class="col-md-6 position-relative">
                                    <label class="form-label fw-bold">الوظيفة:</label>
                                    <input type="hidden" wire:model="job_title">
                                    <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="jobTitleSearch" placeholder="ابحث عن الوظيفة..." autocomplete="off">
                                    @if(!empty($this->matchingJobTitles) && $this->matchingJobTitles->isNotEmpty() && $jobTitleSearch !== $job_title)
                                        <div class="card position-absolute w-100 shadow-sm border mt-1" style="z-index: 1050; max-height: 150px; overflow-y: auto;">
                                            <ul class="list-group list-group-flush small mb-0">
                                                @foreach($this->matchingJobTitles as $job)
                                                    <li>
                                                        <button type="button" wire:click.prevent="selectJobTitle('{{ $job->name }}')" class="list-group-item list-group-item-action py-2 text-end fw-bold">
                                                            {{ $job->name }}
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>

                                <!-- المؤهل -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">المؤهل:</label>
                                    <select class="form-select form-select-sm" wire:model="qualification">
                                        <option value="">-- اختر المؤهل --</option>
                                        @foreach($specialties ?? [] as $spec)
                                            <option value="{{ $spec->name }}">{{ $spec->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- الإدارة -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الإدارة / القسم:</label>
                                    <input type="text" class="form-control form-control-sm" wire:model="department" placeholder="اسم الإدارة أو القسم">
                                </div>

                                <!-- جهة العمل -->
                                <div class="col-md-6 position-relative">
                                    <label class="form-label fw-bold">جهة العمل:</label>
                                    <input type="hidden" wire:model="workplace">
                                    <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="facilitySearch" placeholder="ابحث عن جهة العمل..." autocomplete="off">
                                    @if(!empty($this->facilitySearch) && ($this->facilitySearch !== $workplace) && $this->matchingFacilities->isNotEmpty())
                                        <div class="card position-absolute w-100 shadow-sm border mt-1" style="z-index: 1050; max-height: 150px; overflow-y: auto;">
                                            <ul class="list-group list-group-flush small mb-0">
                                                @foreach($this->matchingFacilities as $facility)
                                                    <li>
                                                        <button type="button" wire:click.prevent="selectFacility('{{ $facility->name }}')" class="list-group-item list-group-item-action py-2 text-end fw-bold">
                                                            {{ $facility->name }}
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>

                                <!-- الموبايل والإيميل -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">رقم الموبايل:</label>
                                    <input type="text" class="form-control form-control-sm font-monospace" wire:model="phone_number" placeholder="010xxxxxxxx">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">البريد الإلكتروني:</label>
                                    <input type="email" class="form-control form-control-sm" wire:model="email" placeholder="example@domain.com">
                                </div>
                            </div>
                        </div>

                        <!-- اختيار الدورة السعة -->
                        <div class="card border-0 shadow-sm p-3 mb-4 bg-white rounded-3">
                            <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">
                                <i class="bi bi-calendar-event me-1"></i> اختيار البرنامج التدريبي
                            </h6>
                            <div class="mb-3">
                                <label class="form-label fw-bold">اختر البرنامج المتاح: *</label>
                                <select class="form-select" wire:model="selected_schedule_id">
                                    <option value="">-- اختر البرنامج التدريبي والموعد --</option>
                                    @foreach($schedules as $sched)
                                        @php
                                            $isFull = $sched->enrolled_count >= $sched->max_capacity;
                                        @endphp
                                        <option value="{{ $sched->id }}" {{ $isFull ? 'disabled' : '' }}>
                                            {{ $sched->program_name }}
                                            ({{ $sched->start_date }} إلى {{ $sched->end_date }}) -
                                            الفترة: {{ $sched->time_slot }}
                                            [{{ $isFull ? 'مكتمل العدد' : 'المقاعد المتبقية: ' . ($sched->max_capacity - $sched->enrolled_count) }}]
                                        </option>
                                    @endforeach
                                </select>
                                @error('selected_schedule_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg px-5 fw-bold shadow-sm rounded-pill">
                                <i class="bi bi-check-circle-fill me-1"></i> تأكيد وإتمام التسجيل
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
