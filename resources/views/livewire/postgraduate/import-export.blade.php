<div class="container py-3" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

                <!-- رأس البطاقة -->
                <div class="card-header bg-primary text-white py-3 px-4 d-flex align-items-center">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-2 ms-2" style="width: 40px; height: 40px;">
                        <i class="bi bi-arrow-left-right fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 fs-6">إدارة استيراد وتصدير بيانات الدراسات العليا</h5>
                        <p class="text-white-50 small mb-0" style="font-size: 0.75rem;">تنزيل النماذج ورفع الملفات المعبأة بسهولة</p>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4 bg-light">

                    <!-- التنبيهات -->
                    @if (session()->has('success'))
                        <div class="alert alert-success border-0 shadow-sm rounded-3 py-2 px-3 d-flex align-items-center mb-3" role="alert">
                            <i class="bi bi-check-circle-fill text-success me-2 ms-2"></i>
                            <div class="flex-grow-1 text-success small fw-semibold">{{ session('success') }}</div>
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 px-3 d-flex align-items-center mb-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill text-danger me-2 ms-2"></i>
                            <div class="flex-grow-1 text-danger small fw-semibold">{{ session('error') }}</div>
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- تقسيم الشاشة إلى نصفين متساويين -->
                    <div class="row g-3 align-items-stretch">

                        <!-- القسم الأول: تنزيل / تصدير البيانات (النصف الأيمن) -->
                        <div class="col-md-6">
                            <div class="h-100 p-3 bg-white rounded-3 border shadow-sm d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-success bg-opacity-10 text-success p-2 rounded-2 me-2 ms-2">
                                            <i class="bi bi-file-earmark-excel-fill fs-5"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-0">تنزيل البيانات / النموذج</h6>
                                    </div>
                                    <p class="text-muted mb-3" style="font-size: 0.8rem;">احصل على شيت الإكسيل المعتمد للنموذج أو مراجعة البيانات الحالية.</p>
                                </div>

                                <button wire:click="export" class="btn btn-outline-success w-100 py-2 rounded-2 fw-bold btn-sm d-flex align-items-center justify-content-center">
                                    <i class="bi bi-download me-2 ms-2"></i>
                                    <span>تنزيل شيت الإكسيل</span>
                                </button>
                            </div>
                        </div>

                        <!-- القسم الثاني: رفع / استيراد البيانات (النصف الأيسر) -->
                        <div class="col-md-6">
                            <div class="h-100 p-3 bg-white rounded-3 border shadow-sm d-flex flex-column justify-content-between">
                                <form wire:submit.prevent="import" class="h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-2 me-2 ms-2">
                                                <i class="bi bi-cloud-arrow-up-fill fs-5"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-0">رفع واستيراد البيانات</h6>
                                        </div>

                                        <!-- حقل اختيار الملف -->
                                        <div class="mb-2">
                                            <input type="file" id="excelFile" wire:model="file" class="form-control form-control-sm rounded-2">
                                            @error('file')
                                                <div class="text-danger mt-1 fw-semibold" style="font-size: 0.75rem;"><i class="bi bi-x-circle me-1"></i> {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100 py-2 rounded-2 fw-bold btn-sm text-white d-flex align-items-center justify-content-center mt-2" wire:loading.attr="disabled">
                                        <span wire:loading.remove class="d-flex align-items-center justify-content-center">
                                            <i class="bi bi-upload me-2 ms-2"></i> بدء استيراد البيانات
                                        </span>
                                        <span wire:loading class="d-flex align-items-center justify-content-center">
                                            <span class="spinner-border spinner-border-sm me-2 ms-2" role="status" aria-hidden="true"></span>
                                            جاري المعالجة...
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
