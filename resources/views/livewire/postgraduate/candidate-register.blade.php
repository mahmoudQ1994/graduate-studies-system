<div class="container-fluid py-2">
    <!-- عنوان الشاشة -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="fw-bold mb-1 text-primary"><i class="bi bi-person-plus-fill me-2"></i>تسجيل بيانات مرشح جديد</h5>
            <p class="text-muted small mb-0">إدخال وترشيح كادر صحي لقيد دراسات عليا جديد</p>
        </div>
    </div>

    @if (session()->has('error'))
        <div class="alert alert-danger py-2 px-3 rounded-3 shadow-sm mb-3 small">{{ session('error') }}</div>
    @endif

    <!-- Wizard Tabs (مصغرة ومتناسقة) -->
    <div class="row g-2 mb-3 text-center">
        <div class="col-md-4">
            <button type="button"
                    wire:click="setTab(1)"
                    class="btn w-100 py-2 px-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 border {{ $activeTab === 1 ? 'btn-primary text-white fw-bold' : 'btn-light bg-white text-secondary' }}">
                <i class="bi bi-card-heading fs-5"></i>
                <div class="text-end">
                    <div class="lh-1 extra-small text-uppercase opacity-75" style="font-size: 11px;">الخطوة الأولى</div>
                    <div class="fw-bold small">1. البيانات الأساسية</div>
                </div>
            </button>
        </div>
        <div class="col-md-4">
            <button type="button"
                    wire:click="setTab(2)"
                    class="btn w-100 py-2 px-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 border {{ $activeTab === 2 ? 'btn-primary text-white fw-bold' : 'btn-light bg-white text-secondary' }}">
                <i class="bi bi-mortarboard-fill fs-5"></i>
                <div class="text-end">
                    <div class="lh-1 extra-small text-uppercase opacity-75" style="font-size: 11px;">الخطوة الثانية</div>
                    <div class="fw-bold small">2. المؤهل وحركة النيابة</div>
                </div>
            </button>
        </div>
        <div class="col-md-4">
            <button type="button"
                    wire:click="setTab(3)"
                    class="btn w-100 py-2 px-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 border {{ $activeTab === 3 ? 'btn-primary text-white fw-bold' : 'btn-light bg-white text-secondary' }}">
                <i class="bi bi-journal-plus fs-5"></i>
                <div class="text-end">
                    <div class="lh-1 extra-small text-uppercase opacity-75" style="font-size: 11px;">الخطوة الثالثة</div>
                    <div class="fw-bold small">3. التخصص والقيد السابق</div>
                </div>
            </button>
        </div>
    </div>

    <form wire:submit.prevent="save">

        <!-- 1. البيانات الأساسية -->
        <div class="{{ $activeTab === 1 ? '' : 'd-none' }}">
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-header bg-white py-2 px-3 fw-bold text-primary small">
                    <i class="bi bi-card-heading me-1"></i>بيانات الهوية والتحقق
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small mb-1">الرقم القومي (14 رقم) <span class="text-danger">*</span></label>
                            <input type="text" wire:model.live.debounce.500ms="national_id" maxlength="14" class="form-control form-control-sm" placeholder="أدخل الرقم القومي">
                            @error('national_id') <span class="text-danger extra-small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-bold small mb-1">الاسم بالكامل <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" class="form-control form-control-sm" placeholder="الاسم ثلاثي أو رباعي">
                            @error('name') <span class="text-danger extra-small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small mb-1">رقم التليفون</label>
                            <input type="text" wire:model="phone" class="form-control form-control-sm" placeholder="01xxxxxxxxx">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small mb-1">الوظيفة</label>
                            <select wire:model.live="profession" class="form-select form-select-sm">
                                <option value="طبيب بشري">طبيب بشري</option>
                                <option value="طبيب أسنان">طبيب أسنان</option>
                                <option value="صيدلي">صيدلي</option>
                                <option value="تمريض">تمريض</option>
                                <option value="أخصائي">أخصائي</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small mb-1">جهة العمل الأصلية</label>
                            <select wire:model="facility_id" class="form-select form-select-sm">
                                <option value="">-- اختر جهة العمل --</option>
                                @foreach($facilities as $fac)
                                    <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small mb-1 text-secondary">جهة الانتداب / النيابة / الإعارة</label>
                            <input type="text" wire:model="secondment_facility" class="form-control form-control-sm" placeholder="اسم الجهة (أو اتركه فارغاً)">
                        </div>
                    </div>

                    @if($activeRegistration)
                        @if(!$canRegisterNew)
                            <div class="alert alert-danger d-flex align-items-center p-2 mt-3 mb-0 rounded-3 small">
                                <i class="bi bi-x-circle-fill fs-6 me-2"></i>
                                <div>
                                    <strong>تنبيه محظور: لا يمكن التسجيل!</strong> المرشح مقيد حالياً بـ ({{ $activeRegistration->required_degree }} - {{ $activeRegistration->required_specialty }}) بجامعة {{ $activeRegistration->required_university }} وموقفه الحالي <strong>{{ $activeRegistration->study_status }}</strong>.
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info d-flex align-items-center p-2 mt-3 mb-0 rounded-3 small">
                                <i class="bi bi-info-circle-fill fs-6 me-2"></i>
                                <div>
                                    <strong>تنبيه الموقف السابق:</strong> سبق له القيد وموقفه الحالي مغلق كـ <strong>{{ $activeRegistration->study_status }}</strong>. يمكنك التسجيل له.
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <div class="text-end mb-3">
                <button type="button" wire:click="setTab(2)" class="btn btn-primary btn-sm px-4 rounded-3">
                    التالي: المؤهل وحركة النيابة <i class="bi bi-arrow-left ms-1"></i>
                </button>
            </div>
        </div>

        <!-- 2. بيانات المؤهل وحركة النيابة -->
        <div class="{{ $activeTab === 2 ? '' : 'd-none' }}">
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-header bg-white py-2 px-3 fw-bold text-secondary small">
                    <i class="bi bi-mortarboard-fill me-1"></i>بيانات المؤهل التخصصي والتخرج
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-md-4"><label class="form-label small fw-bold mb-1">جامعة التخرج</label><input type="text" wire:model="university" class="form-control form-control-sm"></div>
                        <div class="col-md-4"><label class="form-label small fw-bold mb-1">كلية التخرج</label><input type="text" wire:model="faculty" class="form-control form-control-sm"></div>
                        <div class="col-md-4"><label class="form-label small fw-bold mb-1">دفعة التخرج</label><input type="text" wire:model="graduation_batch" class="form-control form-control-sm"></div>
                        <div class="col-md-4"><label class="form-label small fw-bold mb-1">التقدير العام</label><input type="text" wire:model="general_grade" class="form-control form-control-sm"></div>
                        <div class="col-md-4"><label class="form-label small fw-bold mb-1">المجموع التراكمي</label><input type="text" wire:model="total_marks" class="form-control form-control-sm"></div>

                        @if(in_array($profession, ['طبيب بشري', 'طبيب أسنان']))
                            <div class="col-md-4"><label class="form-label small text-primary fw-bold mb-1">تقدير المادة (للأطباء)</label><input type="text" wire:model="subject_grade" class="form-control form-control-sm"></div>
                            <div class="col-md-6"><label class="form-label small text-primary fw-bold mb-1">تخصص حركة النيابة/الإعارة</label><input type="text" wire:model="movement_specialty" class="form-control form-control-sm"></div>
                            <div class="col-md-6"><label class="form-label small text-primary fw-bold mb-1">تاريخ حركة النيابة</label><input type="text" wire:model="movement_date" class="form-control form-control-sm" placeholder="YYYY-MM-DD"></div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mb-3">
                <button type="button" wire:click="setTab(1)" class="btn btn-outline-secondary btn-sm px-4 rounded-3">
                    <i class="bi bi-arrow-right me-1"></i> السابق
                </button>
                <button type="button" wire:click="setTab(3)" class="btn btn-primary btn-sm px-4 rounded-3">
                    التالي: الترشيح والقيد السابق <i class="bi bi-arrow-left ms-1"></i>
                </button>
            </div>
        </div>

        <!-- 3. الدراسة المطلوبة والقيد السابق -->
        <div class="{{ $activeTab === 3 ? '' : 'd-none' }}">
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-header bg-white py-2 px-3 fw-bold text-success small">
                    <i class="bi bi-journal-plus me-1"></i>بيانات الدراسة المطلوبة ونوع الترشيح
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small mb-1">نوع الدراسة <span class="text-danger">*</span></label>
                            <select wire:model.live="required_degree" class="form-select form-select-sm">
                                <option value="دبلوم">دبلوم</option>
                                <option value="ماجستير">ماجستير</option>
                                <option value="دكتوراة">دكتوراة</option>
                                <option value="زمالة">زمالة</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small mb-1">التخصص المطلوب <span class="text-danger">*</span></label>
                            <input type="text" wire:model="required_specialty" class="form-control form-control-sm" placeholder="مثال: الباطنة العامة">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small mb-1">الجامعة المطلوبة <span class="text-danger">*</span></label>
                            <input type="text" wire:model="required_university" class="form-control form-control-sm" placeholder="مثال: جامعة سوهاج">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small mb-1 text-primary">نوع الترشيح <span class="text-danger">*</span></label>
                            <select wire:model="sponsorship_type" class="form-select form-select-sm border-primary fw-bold">
                                <option value="وزاري">ترشيح وزاري</option>
                                <option value="على النفقة الخاصة">على النفقة الخاصة</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small mb-1 text-muted">تاريخ تسجيل الطلب</label>
                            <input type="date" wire:model="application_date" class="form-control form-control-sm bg-light" readonly>
                        </div>

                        @if($doctorateEligibilityError)
                            <div class="col-12">
                                <div class="alert alert-warning p-2 mb-0 rounded-3 border-warning small">
                                    <i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i>
                                    <strong>تنبيه شرط الدكتوراه:</strong> {{ $doctorateEligibilityError }}
                                </div>
                            </div>
                        @endif

                        <div class="col-12">
                            <hr class="my-2">
                            @if($isPriorAutoFilled)
                                <span class="badge bg-info text-dark mb-1"><i class="bi bi-magic me-1"></i>تم جلب بيانات القيد السابق تلقائياً</span>
                            @endif
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold mb-1">هل سبق القيد بالدراسات؟</label>
                            <select wire:model.live="prior_registration_status" class="form-select form-select-sm" {{ $isPriorAutoFilled ? 'disabled' : '' }}>
                                <option value="لا">لا</option>
                                <option value="نعم">نعم</option>
                            </select>
                        </div>

                        @if($prior_registration_status === 'نعم')
                            <div class="col-md-3">
                                <label class="form-label small fw-bold mb-1">حالة الموقف السابق</label>
                                <select wire:model.live="prior_study_outcome" class="form-select form-select-sm" {{ $isPriorAutoFilled ? 'disabled' : '' }}>
                                    <option value="تم الحصول عليها">تم الحصول على الدرجة</option>
                                    <option value="اعتذر أو تم الإلغاء">اعتذر / تم إلغاء القيد</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small mb-1">الدرجة العلمية السابقة</label>
                                <input type="text" wire:model="prior_registration_study" class="form-control form-control-sm" placeholder="مثال: دبلوم باطنة" {{ $isPriorAutoFilled ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small mb-1">سنة القيد السابقة</label>
                                <input type="text" wire:model="prior_registration_year" class="form-control form-control-sm" placeholder="YYYY" {{ $isPriorAutoFilled ? 'readonly' : '' }}>
                            </div>

                            @if($prior_study_outcome === 'تم الحصول عليها')
                                <div class="col-md-4">
                                    <label class="form-label small text-success fw-bold mb-1">تاريخ الحصول على الدرجة</label>
                                    <input type="date" wire:model="prior_degree_date" class="form-control form-control-sm" {{ $isPriorAutoFilled ? 'readonly' : '' }}>
                                </div>
                            @else
                                <div class="col-md-6">
                                    <label class="form-label small text-danger fw-bold mb-1">سبب إلغاء الدراسة السابقة</label>
                                    <input type="text" wire:model="cancellation_reason" class="form-control form-control-sm" placeholder="اذكر السبب..." {{ $isPriorAutoFilled ? 'readonly' : '' }}>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mb-3">
                <button type="button" wire:click="setTab(2)" class="btn btn-outline-secondary btn-sm px-4 rounded-3">
                    <i class="bi bi-arrow-right me-1"></i> السابق
                </button>
                <button type="submit" class="btn btn-success btn-sm px-4 rounded-3 fw-bold" {{ (!$canRegisterNew || $doctorateEligibilityError) ? 'disabled' : '' }}>
                    <i class="bi bi-check-circle me-1"></i>حفظ القيد والترشيح
                </button>
            </div>
        </div>
    </form>
</div>
