<div class="container-fluid px-4 py-4" dir="rtl">

    <!-- هيدر الصفحة العصري -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h4 class="fw-bold text-dark mb-1 fs-5 d-flex align-items-center">
                <i class="fas fa-file-excel text-success me-2 ms-2"></i> استيراد وتصدير بيانات الدراسات العليا
            </h4>
            <p class="text-muted small mb-0">نظام فحص ذكي للتحقق من فريدية الأرقام القومية، منع التعارض الزمني، وعرض كافة أعمدة الشيت للتأكد من صحتها</p>
        </div>
    </div>

    <!-- التنبيهات -->
    @if (session()->has('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 py-2 px-3 d-flex align-items-center mb-3 small" role="alert">
            <i class="fas fa-check-circle me-2 ms-2 fs-5"></i>
            <div class="flex-grow-1 fw-semibold">{{ session('success') }}</div>
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 px-3 d-flex align-items-center mb-3 small" role="alert">
            <i class="fas fa-exclamation-triangle me-2 ms-2 fs-5"></i>
            <div class="flex-grow-1 fw-semibold">{{ session('error') }}</div>
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (!$showPreview)

    <div class="row g-4">
            <!-- التنزيل -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 text-success p-3 rounded-3 me-2 ms-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-file-excel fs-4"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-0 fs-6">تنزيل نموذج شيت الإكسيل</h5>
                        </div>
                        <p class="text-muted small mb-4" style="line-height: 1.7;">
                            احصل على شيت الإكسيل الرسمي المصمم بالهيكل والحدود المعتمدة لتسجيل وتعديل بيانات الدراسات العليا للممارسين بكل سهولة.
                        </p>
                    </div>
                    <button wire:click="export" class="btn btn-outline-success py-2.5 rounded-3 fw-semibold btn-sm d-flex align-items-center justify-content-center">
                        <i class="fas fa-download me-2"></i> تنزيل النموذج المعتمد
                    </button>
                </div>
            </div>

            <!-- الرفع والفحص -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 d-flex flex-column justify-content-between">
                    <form wire:submit.prevent="previewImport" class="h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 me-2 ms-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="fas fa-cloud-upload-alt fs-4"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-0 fs-6">رفع وفحص بيانات الشيت</h5>
                            </div>
                            <p class="text-muted small mb-3" style="line-height: 1.7;">
                                اختر ملف الإكسيل لفحصه آلياً للتحقق من فريدية الأرقام القومية وعدم وجود تعارض زمني في القيد.
                            </p>

                            <div class="mb-3">
                                <input type="file" wire:model="file" class="form-control form-control-sm rounded-3 py-2.5">
                                @error('file') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary py-2.5 rounded-3 fw-semibold btn-sm text-white d-flex align-items-center justify-content-center shadow-sm" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fas fa-search me-2"></i> فحص التعارض والتكرار للبيانات</span>
                            <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span> جاري فحص الملف...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <!-- شاشة تقرير المعاينة الشاملة للأعمدة -->
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1 fs-6">تقرير المعاينة الشاملة لأعمدة الشيت والتحكم في الاعتماد</h5>
                    <p class="text-muted small mb-0">عرض كافة بيانات الممارس والدراسة للتأكد من صحتها قبل التنفيذ النهائي.</p>
                </div>

                <div class="d-flex gap-2">
                    <button wire:click="toggleAll(true)" class="btn btn-outline-success btn-sm rounded-3 px-3">
                        <i class="fas fa-check-double me-1"></i> تحديد الكل للرفع
                    </button>
                    <button wire:click="toggleAll(false)" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                        <i class="fas fa-ban me-1"></i> إلغاء التحديد
                    </button>
                    <button wire:click="cancelPreview" class="btn btn-light border btn-sm rounded-3 px-3">
                        إلغاء المعاينة
                    </button>
                </div>
            </div>

            <!-- بطاقات الإحصائيات -->
            <div class="row text-center mb-4 g-3">
                <div class="col-md-4">
                    <div class="p-3 border-0 shadow-sm rounded-4 bg-light bg-opacity-70">
                        <span class="text-muted small d-block mb-1 fw-semibold">السجلات التي بها أخطاء / تعارض</span>
                        <h3 class="fw-bold text-danger mb-0">{{ $previewData['errors'] ?? 0 }}</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border-0 shadow-sm rounded-4 bg-light bg-opacity-70">
                        <span class="text-muted small d-block mb-1 fw-semibold">دارسون مسجلون مسبقاً</span>
                        <h3 class="fw-bold text-primary mb-0">{{ $previewData['existing_trainees'] ?? 0 }}</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border-0 shadow-sm rounded-4 bg-light bg-opacity-70">
                        <span class="text-muted small d-block mb-1 fw-semibold">دارسون جدد (لأول مرة)</span>
                        <h3 class="fw-bold text-success mb-0">{{ $previewData['brand_new_trainees'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <!-- جدول المعاينة الموسع لعرض كافة الأعمدة الهامة -->
            <div class="table-responsive mb-4 border-0 shadow-sm rounded-4" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-hover align-middle text-center small mb-0 text-nowrap">
                    <thead class="table-light text-muted sticky-top" style="z-index: 2;">
                        <tr>
                            <th class="py-3">الصف</th>
                            <th class="py-3 text-start px-3">اسم المرشح</th>
                            <th class="py-3">الرقم القومي</th>
                            <th class="py-3">رقم التواصل</th>
                            <th class="py-3">التخصص / المهنة</th>
                            <th class="py-3">جهة العمل</th>
                            <th class="py-3">نوع الدراسة المطلوبة</th>
                            <th class="py-3">التخصص المطلوب</th>
                            <th class="py-3">جهة الترشيح / الجامعة</th>
                            <th class="py-3">حالة الدراسة</th>
                            <th class="py-3 text-start px-3">حالة الفحص والتعارض</th>
                            <th class="py-3">قرار الرفع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previewData['details'] as $index => $item)
                            <tr class="{{ $item['status'] == 'conflicted' ? 'table-warning' : '' }}">
                                <td class="fw-semibold text-muted">{{ $item['row_num'] }}</td>
                                <td class="text-start px-3 fw-bold text-dark">{{ $item['name'] }}</td>
                                <td><span class="font-monospace text-secondary fw-semibold">{{ $item['national_id'] }}</span></td>
                                <td>
                                    @if(!empty($item['phone']))
                                        <a href="tel:{{ $item['phone'] }}" class="text-decoration-none fw-bold text-primary bg-primary bg-opacity-10 px-2 py-1 rounded-2 d-inline-flex align-items-center">
                                            <i class="fas fa-phone-alt fa-xs me-1"></i> {{ $item['phone'] }}
                                        </a>
                                    @else
                                        <span class="text-muted small">غير متوفر</span>
                                    @endif
                                </td>
                                <td>{{ $item['profession'] ?? '-' }}</td>
                                <td>{{ $item['facility'] ?? '-' }}</td>
                                <td><span class="badge bg-light text-dark border px-2 py-1">{{ $item['degree'] }}</span></td>
                                <td>{{ $item['specialty'] ?? '-' }}</td>
                                <td>{{ $item['university'] ?? '-' }}</td>
                                <td><span class="badge bg-info bg-opacity-15 text-dark px-2 py-1">{{ $item['study_status'] ?? 'جاري فحص الطلب' }}</span></td>
                                <td class="text-start px-3">
                                    <div class="fw-semibold text-dark">{{ $item['notes'] }}</div>
                                    @if($item['conflict_details'])
                                        <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i> {{ $item['conflict_details'] }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               wire:model.live="previewData.details.{{ $index }}.approved"
                                               style="width: 2.8em; height: 1.4em; cursor: pointer;">
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- زر الاعتماد النهائي -->
            <div class="d-flex justify-content-end">
                <button wire:click="confirmSelectedImport" class="btn btn-primary py-2.5 px-5 fw-bold btn-sm text-white d-flex align-items-center rounded-3 shadow-sm">
                    <i class="fas fa-cloud-upload-alt me-2 fs-5"></i> تنفيذ رفع السجلات المحددة فقط ({{ collect($previewData['details'])->where('approved', true)->count() }})
                </button>
            </div>

        </div>
    @endif

</div>
