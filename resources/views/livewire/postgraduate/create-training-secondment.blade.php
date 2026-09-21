<div>
    <div>
    <!-- رسالة النجاح -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3 text-center py-2 shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- رسالة الخطأ أو منع الحفظ -->
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3 text-center py-2 shadow-sm" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- تنبيه بموقف الموظف الحالي إن وجد -->
    @if ($activeTrainingMessage)
        <div class="alert alert-warning border-0 shadow-sm mb-3 py-2 small" role="alert">
            <i class="bi bi-exclamation-triangle-fill ms-1"></i> <strong>تنبيه موقف التدريب:</strong> {{ $activeTrainingMessage }}
        </div>
    @endif

     <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="fw-bold mb-1 text-primary"><i class="bi bi-person-plus-fill me-2"></i>تسجيل بيانات  الايفاد للتدريب </h5>
            <p class="text-muted small mb-0">إدخال افاد كادر صحي للتدريب جديد او مد ايافد </p>
        </div>
    </div>
    <!-- شريط الخطوات العلوي -->
    <div class="row g-2 mb-3">
        <div class="col-md-4">
            <div class="p-2 text-center rounded border transition {{ $currentStep == 1 ? 'bg-primary text-white shadow-sm' : 'bg-light text-muted' }}" style="cursor: pointer; font-size: 0.9rem;" wire:click="$set('currentStep', 1)">
                <span class="fw-bold">1. البيانات الأساسية</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-2 text-center rounded border transition {{ $currentStep == 2 ? 'bg-primary text-white shadow-sm' : 'bg-light text-muted' }}" style="cursor: pointer; font-size: 0.9rem;" wire:click="$set('currentStep', 2)">
                <span class="fw-bold">2. المؤهل وحركة النيابة</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-2 text-center rounded border transition {{ $currentStep == 3 ? 'bg-primary text-white shadow-sm' : 'bg-light text-muted' }}" style="cursor: pointer; font-size: 0.9rem;" wire:click="$set('currentStep', 3)">
                <span class="fw-bold">3. بيانات الإفاد للتدريب</span>
            </div>
        </div>
    </div>

    <!-- صندوق النموذج بتصميم مدمج وأنيق -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form wire:submit.prevent="save">

                <!-- الخطوة الأولى -->
                @if($currentStep == 1)
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">الرقم القومي (14 رقم) <span class="text-danger">*</span></label>
                            <input type="text" wire:model.live.debounce.500ms="national_id" class="form-control form-control-sm" maxlength="14" placeholder="أدخل الرقم القومي">
                            @error('national_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">الاسم بالكامل <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" class="form-control form-control-sm" placeholder="الاسم ثلاثي أو رباعي">
                            @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">رقم التليفون</label>
                            <input type="text" wire:model="phone" class="form-control form-control-sm" placeholder="01xxxxxxxxx">
                            @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">الوظيفة</label>
                            <select wire:model.live="profession" class="form-select form-select-sm">
                                <option value="طبيب بشري">طبيب بشري</option>
                                <option value="طبيب أسنان">طبيب أسنان</option>
                                <option value="ممارس علاج طبيعى">ممارس علاج طبيعى</option>
                                <option value="صيدلي">صيدلي</option>
                                <option value="تمريض">تمريض</option>
                                <option value="اخصائى علوم صحية">اخصائى علوم صحية</option>
                                <option value="باحث شئون قانونية">باحث شئون قانونية</option>
                                <option value="اخصائى شئون مالية وادارية">اخصائى شئون ادارية</option>
                            </select>
                            @error('profession') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">جهة العمل الأصلية</label>
                            <select wire:model="facility_id" class="form-select form-select-sm">
                                <option value="">-- اختر جهة العمل الأصلية --</option>
                                @foreach($facilities as $facility)
                                    <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                                @endforeach
                            </select>
                            @error('facility_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">جهة الانتداب / النيابة / الإعارة</label>
                            <input type="text" wire:model="secondment_facility" class="form-control form-control-sm" placeholder="اسم الجهة (أو اتركه فارغاً)">
                            @error('secondment_facility') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-primary btn-sm px-4 shadow-sm" wire:click="nextStep">التالي: المؤهل وحركة النيابة ←</button>
                    </div>
                @endif


                <!-- الخطوة الثانية -->
                @if($currentStep == 2)
                    <div class="mb-3">
                        <span class="text-primary fw-bold small d-block mb-2">بيانات المؤهل العلمي</span>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small text-muted">الجامعة</label>
                                <input type="text" wire:model="university" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">المؤهل</label>
                                <select wire:model="qualification" class="form-select form-select-sm">
                                <option value="">-- اختر المؤهل --</option>
                                <option value="بكتالوريوس الطب  والجراحة ">بكالوريوس الطب والجراحة </option>
                                <option value="بكالوريوس طب وجراحة القم والاسنان ">بكالوريوس طب وجراحة القم والاسنان </option>
                                <option value="بكالوريوس العلاج الطبيعى ">بكالوريوس العلاج الطبيعى </option>
                                <option value="بكالوريوس الصيدلة ">بكالوريوس الصيدلة </option>
                                <option value="بكالوريوس العلوم فى التمريض ">بكالوريوس العلوم فى التمريض </option>
                                <option value="بكالوريوس العلوم ">بكالوريوس العلوم </option>
                                <option value="بكالوريوس تكنولوجيا العلوم الصحية التطبيقية  ">بكالوريوس تكنولوجيا العلوم الصحية التطبيقية  </option>
                                <option value="بكالوريوس الطب البيطرى  ">بكالوريوس الطب البيطرى  </option>
                            </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">دفعة التخرج</label>
                                <input type="text" wire:model="graduation_batch" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">التقدير العام</label>
                                <input type="text" wire:model="general_grade" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">تقدير مادة التخصص</label>
                                <input type="text" wire:model="subject_grade" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">المجموع الكلي</label>
                                <input type="text" wire:model="total_marks" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 mt-4">
                        <span class="text-primary fw-bold small d-block mb-2">حركة النيابة</span>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">التخصص</label>
                                <input type="text" wire:model="specialty" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">تاريخ حركة النيابة</label>
                                <input type="month" wire:model="movement_date" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-4" wire:click="previousStep">→ السابق</button>
                        <button type="button" class="btn btn-primary btn-sm px-4 shadow-sm" wire:click="nextStep">التالي: بيانات الإفاد للتدريب ←</button>
                    </div>
                @endif


                <!-- الخطوة الثالثة -->
                @if($currentStep == 3)
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">نوع الإجراء</label>
                            <select wire:model.live="action_type" class="form-select form-select-sm">
                                <option value="إيفاد">إيفاد</option>
                                <option value="مد إيفاد">مد إيفاد</option>
                            </select>
                            @error('action_type') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        @if($action_type === 'مد إيفاد')
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-secondary">اختر الإفاد المراد مدّه</label>
                                <select wire:model.live="selectedPreviousTrainingId" class="form-select form-select-sm">
                                    <option value="">-- اختر الإفاد السابق --</option>
                                    @foreach($previousTrainings as $prev)
                                        <option value="{{ $prev->id }}">
                                            جهة التدريب: {{ $prev->training_entity }} (من {{ $prev->start_date }} إلى {{ $prev->end_date }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">جهة التدريب <span class="text-danger">*</span></label>
                            <input type="text" wire:model="training_entity" class="form-control form-control-sm">
                            @error('training_entity') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">القسم / الإدارة التدريبية</label>
                            <input type="text" wire:model="training_department" class="form-control form-control-sm">
                            @error('training_department') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">عدد الشهور <span class="text-danger">*</span></label>
                            <input type="number" wire:model.live="duration_months" class="form-control form-control-sm" min="1">
                            @error('duration_months') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">تاريخ البداية <span class="text-danger">*</span></label>
                            <input type="date" wire:model.live="start_date" class="form-control form-control-sm">
                            @error('start_date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">تاريخ النهاية (تلقائي)</label>
                            <input type="date" wire:model="end_date" class="form-control form-control-sm bg-light" readonly>
                            @error('end_date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-12 mt-3">
                        <label class="form-label small fw-bold text-secondary d-block">أيام التدريب أسبوعياً</label>

                        <!-- خيار انتداب كلي / كل الأيام -->
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="all_days"
                                    @if(is_array($training_days) && count($training_days) == 7) checked @endif
                                    wire:click="toggleAllDays">
                                <label class="form-check-label small fw-bold text-primary" for="all_days">
                                    انتداب كلي (جميع أيام الأسبوع)
                                </label>
                            </div>

                            <!-- أيام الأسبوع الفردية -->
                            <div class="d-flex flex-wrap gap-3">
                                @php
                                    $days = ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];
                                @endphp
                                @foreach($days as $day)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{ $day }}" wire:model.live="training_days" id="day_{{ $loop->index }}">
                                        <label class="form-check-label small" for="day_{{ $loop->index }}">
                                            {{ $day }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                    </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-4" wire:click="previousStep">→ السابق</button>
                        <button type="submit" class="btn btn-success btn-sm px-5 shadow-sm">حفظ البيانات النهائية</button>
                    </div>
                @endif

            </form>
        </div>
    </div>
</div>
