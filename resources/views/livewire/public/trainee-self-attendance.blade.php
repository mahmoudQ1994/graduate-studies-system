<div class="container-fluid d-flex justify-content-center align-items-center py-3 px-3" style="min-height: 100vh; background-color: #f0f2f5;" dir="rtl">
    <div class="col-md-7 col-lg-5 col-xl-4">

        <!-- كارت موحد وعصري -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center">

            <!-- الترويسة الرسمية (الشعار في الأعلى وتحته بيانات الوزارة) -->
            <div class="pb-3 border-bottom mb-3">
                <div class="d-flex flex-column align-items-center justify-content-center gap-2">

                    <div class="mb-1">
                        @if($settings && $settings->logo_path)
                            <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Logo" class="rounded-3 shadow-sm border p-1 bg-white" style="width: 65px; height: 65px; object-fit: contain;">
                        @else
                            <div class="bg-success-subtle text-success rounded-3 d-flex align-items-center justify-content-center shadow-sm border" style="width: 65px; height: 65px;">
                                <i class="bi bi-hospital fs-4"></i>
                            </div>
                        @endif
                    </div>

                    <div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.88rem; line-height: 1.3;">
                            {{ $settings->ministry_name ?? 'وزارة الصحة والسكان' }}
                        </h6>
                        <h6 class="fw-bold text-secondary mb-1" style="font-size: 0.82rem; line-height: 1.3;">
                            {{ $settings->governorate_name ?? 'محافظة سوهاج' }}
                        </h6>
                        <span class="text-muted d-block" style="font-size: 0.75rem; line-height: 1.3;">
                            {{ $settings->directorate_name ?? 'مديرية الشئون الصحية' }} - {{ $settings->administration_name ?? 'ادارة التدريب' }}
                        </span>
                    </div>

                </div>
            </div>

            <!-- معلومات الدورة واليوم التدريبي -->
            <div class="text-center mb-4">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-semibold mb-2" style="font-size: 0.78rem;">
                    <i class="bi bi-calendar-check me-1"></i> {{ $dayLabel ?? 'بوابة التسجيل الذاتي' }}
                </span>

                @if($scheduleDetails)
                    <h5 class="fw-bold text-primary mb-1 mt-1" style="font-size: 0.95rem; line-height: 1.4;">{{ $scheduleDetails->program_name }}</h5>
                    <span class="text-muted small" style="font-size: 0.75rem;">
                        الفترة: <strong>{{ $scheduleDetails->start_date }}</strong> إلى <strong>{{ $scheduleDetails->end_date }}</strong>
                    </span>
                @endif
            </div>

            <!-- تنبيهات النجاح والخطأ العامة -->
            @if ($successMessage)
                <div class="alert alert-success rounded-3 border-0 text-center py-2.5 px-3 mb-3 shadow-sm" style="font-size: 0.85rem;">
                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                    <span class="fw-bold text-dark">{{ $successMessage }}</span>
                </div>
            @endif

            @if ($errorMessage)
                <div class="alert alert-danger rounded-3 border-0 text-center py-2.5 px-3 mb-3 shadow-sm" style="font-size: 0.85rem;">
                    <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i>
                    <span class="fw-semibold">{{ $errorMessage }}</span>
                </div>
            @endif

            <!-- التحقق مما إذا كانت الدورة قد انتهت -->
            @if($isExpired)
                <div class="alert alert-warning rounded-3 border-0 text-center py-4 px-3 mb-2 shadow-sm bg-warning-subtle text-warning-emphasis">
                    <i class="bi bi-clock-history fs-2 d-block mb-1 text-warning"></i>
                    <h6 class="fw-bold mb-1">عفواً، انتهت فترة البرنامج</h6>
                    <p class="mb-0 small" style="font-size: 0.82rem;">الرابط لا يعمل حالياً بسبب انتهاء فترة تنفيذ البرنامج التدريبى المقررة  .</p>
                </div>
            @elseif($scheduleDetails)
                <!-- نموذج الإدخال يعمل فقط إذا كانت الدورة جارية -->
                <form wire:submit.prevent="registerAttendance">
                    <div class="mb-3 text-end">
                        <label class="form-label fw-bold text-secondary mb-1" style="font-size: 0.8rem;">الرقم القومي (14 رقماً):</label>
                        <input type="text" class="form-control rounded-pill bg-light border-light py-2.5 px-3 font-monospace text-center fs-5 shadow-sm" wire:model="national_id" placeholder="أدخل الرقم القومي..." maxlength="14" autofocus>
                    </div>

                    <button type="submit" class="btn btn-success w-100 rounded-pill py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 mt-3" style="font-size: 0.9rem;">
                        <i class="bi bi-check2-circle fs-5"></i>
                        <span>تأكيد الحضور الآن</span>
                    </button>
                </form>
            @else
                <div class="text-center py-3 text-danger fw-bold" style="font-size: 0.9rem;">
                    رابط الجلسة غير صحيح أو انتهى العمل به.
                </div>
            @endif
        </div>

        <!-- حقوق النشر المبسطة -->
        <div class="text-center mt-3 text-muted" style="font-size: 0.75rem;">
            نظام إدارة التدريب والتعليم الطبي © {{ date('Y') }}
        </div>
    </div>
</div>
