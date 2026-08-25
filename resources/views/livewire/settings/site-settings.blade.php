<div class="container-fluid p-0">
    <!-- عنوان الصفحة -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-gear-fill text-primary me-2"></i>الإعدادات العامة للنظام</h4>
            <p class="text-muted small mb-0">إدارة الهوية الرسمية، العام المالي، وبيانات الترويسة والطباعة</p>
        </div>
    </div>

    <!-- رسالة النجاح -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <!-- شريط التبويبات (Navigation Tabs) -->
        <div class="card-header bg-white border-bottom p-3">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <button class="nav-link fw-semibold {{ $activeTab === 'identity' ? 'active' : '' }}" wire:click="setTab('identity')">
                        <i class="bi bi-building me-1"></i> الهوية والشعار
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold {{ $activeTab === 'fiscal' ? 'active' : '' }}" wire:click="setTab('fiscal')">
                        <i class="bi bi-calendar3 me-1"></i> العام المالي
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold {{ $activeTab === 'print' ? 'active' : '' }}" wire:click="setTab('print')">
                        <i class="bi bi-printer me-1"></i> الترويسة والطباعة
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <form wire:submit.prevent="save">

                <!-- 1. تبويب الهوية والشعار -->
                @if($activeTab === 'identity')
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary">اسم الوزارة</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-building"></i></span>
                                <input type="text" wire:model="ministry_name" class="form-control bg-light @error('ministry_name') is-invalid @enderror" placeholder="مثال: وزارة الصحة والسكان">
                            </div>
                            @error('ministry_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary">اسم المحافظة</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" wire:model="governorate_name" class="form-control bg-light @error('governorate_name') is-invalid @enderror" placeholder="مثال: محافظة سوهاج">
                            </div>
                            @error('governorate_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary">اسم المديرية</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-hospital"></i></span>
                                <input type="text" wire:model="directorate_name" class="form-control bg-light @error('directorate_name') is-invalid @enderror" placeholder="مثال: مديرية الشؤون الصحية">
                            </div>
                            @error('directorate_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary">اسم الإدارة</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-diagram-3"></i></span>
                                <input type="text" wire:model="administration_name" class="form-control bg-light @error('administration_name') is-invalid @enderror" placeholder="مثال: إدارة التدريب والدراسات العليا">
                            </div>
                            @error('administration_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary">اسم القسم (اختياري)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-bookmark"></i></span>
                                <input type="text" wire:model="department_name" class="form-control bg-light @error('department_name') is-invalid @enderror" placeholder="مثال: قسم التعليم الطبي">
                            </div>
                            @error('department_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary">لوجو/شعار الإدارة</label>
                            <input type="file" wire:model="logo" class="form-control bg-light @error('logo') is-invalid @enderror" accept="image/*">
                            @error('logo') <span class="text-danger small">{{ $message }}</span> @enderror

                            <div class="mt-3">
                                @if ($logo)
                                    <span class="small text-muted d-block mb-1">المعاينة الحالية:</span>
                                    <img src="{{ $logo->temporaryUrl() }}" class="rounded-3 border p-1 shadow-sm" style="max-height: 90px;">
                                @elseif ($existing_logo)
                                    <span class="small text-muted d-block mb-1">اللوجو الحالي:</span>
                                    <img src="{{ asset('storage/' . $existing_logo) }}" class="rounded-3 border p-1 shadow-sm" style="max-height: 90px;">
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 2. تبويب العام المالي -->
                @if($activeTab === 'fiscal')
                    <div class="p-3 bg-light rounded-4 mb-4 border">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold text-primary mb-0">
                                <i class="bi bi-calendar3 me-2"></i>إعدادات العام المالي الحالي
                            </h6>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 small">
                                <i class="bi bi-cpu me-1"></i> يُحسب تلقائياً (1/7 - 30/6)
                            </span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary small">العام المالي الحالي</label>
                                <input type="text" wire:model="fiscal_year" class="form-control bg-white fw-bold text-primary" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary small">تاريخ بداية العام المالي</label>
                                <input type="date" wire:model="fiscal_year_start" class="form-control bg-white text-muted" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary small">تاريخ نهاية العام المالي</label>
                                <input type="date" wire:model="fiscal_year_end" class="form-control bg-white text-muted" readonly>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 3. تبويب الترويسة والطباعة -->
                @if($activeTab === 'print')
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold text-secondary small">القسم التابع له</label>
                            <select wire:model="department_id" class="form-select bg-light @error('department_id') is-invalid @enderror">
                                <option value="">-- اختر القسم --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold text-secondary small">اسم المدير</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-person-badge"></i></span>
                                <input type="text" wire:model="manager_name" class="form-control bg-light @error('manager_name') is-invalid @enderror" placeholder="د/ اسم المدير">
                            </div>
                            @error('manager_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold text-secondary small">اسم وكيل وزارة الصحة</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-person-fill-gear"></i></span>
                                <input type="text" wire:model="undersecretary_name" class="form-control bg-light @error('undersecretary_name') is-invalid @enderror" placeholder="د/ اسم وكيل الوزارة">
                            </div>
                            @error('undersecretary_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary small">رقم الهاتف / الفاكس</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
                                <input type="text" wire:model="phone_fax" class="form-control bg-light @error('phone_fax') is-invalid @enderror" placeholder="مثال: 093XXXXXXX">
                            </div>
                            @error('phone_fax') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary small">البريد الإلكتروني الرسمي</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" wire:model="official_email" class="form-control bg-light @error('official_email') is-invalid @enderror" placeholder="info@health-dir.gov.eg">
                            </div>
                            @error('official_email') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small">العنوان التفصيلي (ليظهر في ذيل الصفحات Footer)</label>
                            <textarea wire:model="detailed_address" class="form-control bg-light @error('detailed_address') is-invalid @enderror" rows="2" placeholder="مثال: شارع الجمهورية - بجوار مستشفى الحميات - سوهاج"></textarea>
                            @error('detailed_address') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endif

                <hr class="my-4">

                <!-- زر الحفظ -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold rounded-3 shadow-sm">
                        <span wire:loading.remove wire:target="save"><i class="bi bi-check2-circle me-1"></i> حفظ التغييرات</span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            جاري الحفظ...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
