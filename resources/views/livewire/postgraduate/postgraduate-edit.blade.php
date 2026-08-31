<div class="container-fluid py-2">
    <!-- عنوان الشاشة -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="fw-bold mb-1 text-primary"><i class="bi bi-pencil-square me-2"></i>تعديل بيانات مرشح دراسات عليا</h5>
            <p class="text-muted small mb-0">تحديث بيانات الكادر الصحي وحركة القيد الخاصة به</p>
        </div>
        <div>
            <a href="{{ route('postgraduate.candidates-list') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                <i class="bi bi-arrow-right me-1"></i> العودة للقائمة
            </a>
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

    <form wire:submit.prevent="update">

        <!-- 1. البيانات الأساسية -->
        <div class="{{ $activeTab === 1 ? '' : 'd-none' }}">
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-header bg-white py-2 px-3 fw-bold text-primary small">
                    <i class="bi bi-card-heading me-1"></i>بيانات الهوية والتحقق (تعديل)
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small mb-1">الرقم القومي (14 رقم) <span class="text-danger">*</span></label>
                            <input type="text" wire:model="national_id" maxlength="14" class="form-control form-control-sm">
                            @error('national_id') <span class="text-danger extra-small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-bold small mb-1">الاسم بالكامل <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" class="form-control form-control-sm">
                            @error('name') <span class="text-danger extra-small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small mb-1">رقم التليفون</label>
                            <input type="text" wire:model="phone" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small mb-1">الوظيفة</label>
                            <select wire:model.live="profession" class="form-select form-select-sm">
                                <option value="طبيب بشري">طبيب بشري</option>
                                <option value="طبيب أسنان">طبيب أسنان</option>
                                <option value="ممارس علاج طبيعى">ممارس علاج طبيبعى </option>
                                <option value="صيدلي">صيدلي</option>
                                <option value="تمريض">تمريض</option>
                                <option value="اخصائى علوم صحية ">اخصائى علوم صحية </option>
                                <option value="باحث شئون قانونية ">باحث شئون قانونية </option>
                                <option value="اخصائى شئون مالية وادارية  ">اخصائى شئون ادارية </option>
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
                            <label class="form-label fw-bold small mb-1
                            text-secondary">جهة الانتداب / النيابة / الإعارة</label>
                            <input type="text" wire:model="secondment_facility"
                            class="form-control form-control-sm" placeholder="اسم الجهة  الممنتدب البها ">
                        </div>
                    </div>
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
                            <input type="text" wire:model="required_specialty" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small mb-1">الجامعة المطلوبة <span class="text-danger">*</span></label>
                            <input type="text" wire:model="required_university" class="form-control form-control-sm">
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

                        <div class="col-12">
                            <hr class="my-2">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold mb-1">هل سبق القيد بالدراسات؟</label>
                            <select wire:model.live="prior_registration_status" class="form-select form-select-sm">
                                <option value="لا">لا</option>
                                <option value="نعم">نعم</option>
                            </select>
                        </div>

                        @if($prior_registration_status === 'نعم')
                            <div class="col-md-3">
                                <label class="form-label small mb-1">الدرجة العلمية السابقة</label>
                                <input type="text" wire:model="prior_registration_study" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small mb-1">سنة القيد السابقة</label>
                                <input type="text" wire:model="prior_registration_year" class="form-control form-control-sm" placeholder="YYYY">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-danger fw-bold mb-1">سبب إلغاء الدراسة السابقة إن وجد</label>
                                <input type="text" wire:model="cancellation_reason" class="form-control form-control-sm">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mb-3">
                <button type="button" wire:click="setTab(2)" class="btn btn-outline-secondary btn-sm px-4 rounded-3">
                    <i class="bi bi-arrow-right me-1"></i> السابق
                </button>
                <button type="submit" class="btn btn-primary btn-sm px-4 rounded-3 fw-bold">
                    <i class="bi bi-check-circle me-1"></i>حفظ التعديلات
                </button>
            </div>
        </div>
    </form>
</div>
