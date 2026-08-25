<div class="container-fluid py-2">
    <!-- عنوان الشاشة -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="fw-bold mb-1 text-primary"><i class="bi bi-person-gear me-2"></i>متابعة وتحديث موقف المرشح</h5>
            <p class="text-muted small mb-0">إدارة تواريخ التنفيذ، تعديل النيابة، وإيقاف أو إعادة تفعيل القيد الدراسي</p>
        </div>
        <div>
            <a href="{{ route('postgraduate.candidates.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                <i class="bi bi-arrow-right me-1"></i> العودة للقائمة
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success py-2 px-3 rounded-3 shadow-sm mb-3 small">{{ session('success') }}</div>
    @endif

    <form wire:submit.prevent="updateRecord">

        <!-- 1. بيانات تنفيذ الدراسة والتاريخ بالجامعة -->
        <div class="card border-0 shadow-sm rounded-3 mb-3">
            <div class="card-header bg-white py-2 px-3 fw-bold text-success small">
                <i class="bi bi-calendar-check me-1"></i>تاريخ التنفيذ والقيد بالدراسة
            </div>
            <div class="card-body p-3">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small mb-1">تاريخ تنفيذ الدراسة (المباشرة الفعلية)</label>
                        <input type="date" wire:model="execution_date" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small mb-1">تاريخ القيد الفعلي بالجامعة</label>
                        <input type="date" wire:model="study_enrollment_date" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. تعديل جهة العمل وحركة النيابة -->
        <div class="card border-0 shadow-sm rounded-3 mb-3">
            <div class="card-header bg-white py-2 px-3 fw-bold text-primary small">
                <i class="bi bi-building me-1"></i>تعديل جهة العمل وبيانات النيابة
            </div>
            <div class="card-body p-3">
                <div class="row g-2">
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
                        <label class="form-label fw-bold small mb-1">جهة الانتداب / النيابة / الإعارة</label>
                        <input type="text" wire:model="secondment_facility" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small mb-1">تخصص حركة النيابة</label>
                        <input type="text" wire:model="movement_specialty" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small mb-1">تاريخ حركة النيابة</label>
                        <input type="date" wire:model="movement_date" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. إدارة إيقاف القيد أو إعادة تفعيله -->
        <div class="card border-0 shadow-sm rounded-3 mb-3 border-warning">
            <div class="card-header bg-light py-2 px-3 fw-bold text-dark small d-flex justify-content-between align-items-center">
                <span><i class="bi bi-pause-circle me-1 text-warning"></i>إدارة حالة القيد (إيقاف / إعادة قيد وتنشيط)</span>
                <div>
                    @if($is_suspended)
                        <button type="button" wire:click="toggleSuspension(0)" class="btn btn-outline-success btn-xs py-0 px-2 small fw-bold">
                            <i class="bi bi-play-circle me-1"></i>إلغاء الإيقاف (إعادة قيد)
                        </button>
                    @else
                        <button type="button" wire:click="toggleSuspension(1)" class="btn btn-outline-warning btn-xs py-0 px-2 small fw-bold text-dark">
                            <i class="bi bi-pause-circle me-1"></i>تفعيل إيقاف القيد
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body p-3">
                @if($is_suspended)
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small mb-1 text-danger">سبب إيقاف القيد <span class="text-danger">*</span></label>
                            <select wire:model="suspension_reason" class="form-select form-select-sm border-danger">
                                <option value="">-- اختر السبب --</option>
                                <option value="رعاية طفل">رعاية طفل</option>
                                <option value="مرافقة زوج/زوجة">مرافقة زوج/زوجة</option>
                                <option value="إجازة مرضية طويلة">إجازة مرضية طويلة</option>
                                <option value="أسباب أخرى مبررة">أسباب أخرى مبررة</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small mb-1">تاريخ بداية الإيقاف</label>
                            <input type="date" wire:model.live="suspension_start_date" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small mb-1">تاريخ نهاية الإيقاف</label>
                            <input type="date" wire:model.live="suspension_end_date" class="form-control form-control-sm">
                        </div>

                        @if($calculated_suspension_period)
                            <div class="col-12 mt-2">
                                <div class="alert alert-warning py-2 px-3 mb-0 small border-warning">
                                    <i class="bi bi-clock-history me-1"></i> مدة إيقاف القيد المحسوبة تلقائياً: <strong>{{ $calculated_suspension_period }}</strong>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-muted small py-1">
                        <i class="bi bi-check-circle-fill text-success me-1"></i> القيد حالياً في حالة <strong>ساري / نشط</strong>. انقر على زر "تفعيل إيقاف القيد" في الأعلى في حال رغبتك بإيقافه مؤقتاً وتسجيل المدة والسبب.
                    </div>
                @endif
            </div>
        </div>

        <!-- زر الحفظ النهائي -->
        <div class="text-end mb-3">
            <button type="submit" class="btn btn-success btn-sm px-4 rounded-3 fw-bold">
                <i class="bi bi-check-circle me-1"></i>حفظ كافة التحديثات والموقف
            </button>
        </div>
    </form>
</div>
