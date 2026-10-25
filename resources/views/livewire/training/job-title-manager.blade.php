<div>
    <div class="container-fluid py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-primary mb-0">
                <i class="bi bi-briefcase-fill me-1"></i> تكويد وإدارة الوظائف المتاحة
            </h5>
            <div class="d-flex gap-2">
                <!-- زر تنزيل الوظائف -->
                <button type="button" wire:click="exportExcel" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-download me-1"></i> تنزيل الوظائف (Excel)
                </button>
                <!-- زر فتح نافذة الاستيراد -->
                <button type="button" data-bs-toggle="modal" data-bs-target="#importModal" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-file-earmark-arrow-up me-1"></i> استيراد شيت إكسيل
                </button>
                <!-- زر إضافة وظيفة -->
                <button type="button" wire:click="openModal" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle-fill me-1"></i> إضافة وظيفة جديدة
                </button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="بحث عن وظيفة...">
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 text-center align-middle">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th class="py-3">م</th>
                                <th class="py-3 text-start">اسم الوظيفة</th>
                                <th class="py-3">الحالة</th>
                                <th class="py-3">تمت الإضافة بواسطة</th>
                                <th class="py-3">تاريخ الإضافة</th>
                                <th class="py-3">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobs as $index => $job)
                                <tr>
                                    <td class="text-muted">{{ $jobs->firstItem() + $index }}</td>
                                    <td class="fw-bold text-start text-dark">{{ $job->name }}</td>
                                    <td>
                                        <span class="badge {{ $job->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $job->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td class="text-secondary small">{{ $job->creator_name ?? '---' }}</td>
                                    <td class="text-muted small font-monospace">
                                        {{ $job->created_at ? date('Y-m-d H:i', strtotime($job->created_at)) : '---' }}
                                    </td>
                                    <td>
                                        <button type="button" wire:click="openModal({{ $job->id }})" class="btn btn-outline-primary btn-sm px-2 py-1" title="تعديل">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" wire:click="deleteJob({{ $job->id }})" wire:confirm="هل أنت متأكد من الحذف؟" class="btn btn-outline-danger btn-sm px-2 py-1" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">لا توجد وظائف مسجلة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($jobs->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $jobs->links() }}
                </div>
            @endif
        </div>

        <!-- Modal إضافة وتعديل وظيفة -->
        <div wire:ignore.self class="modal fade" id="jobModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white py-2">
                        <h6 class="modal-title fw-bold">
                            {{ $jobTitleId ? 'تعديل وظيفة' : 'إضافة وظيفة جديدة' }}
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="mb-3">
                            <label class="form-label fw-bold">اسم الوظيفة: *</label>
                            <input type="text" class="form-control form-control-sm" wire:model="name" placeholder="أدخل اسم الوظيفة">
                            @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="is_active" id="isActiveCheck">
                            <label class="form-check-label fw-bold" for="isActiveCheck">نشط ومتاح للاستخدام</label>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">إغلاق</button>
                        <button type="button" wire:click="saveJob" class="btn btn-success btn-sm px-3 fw-bold">حفظ</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal استيراد شيت إكسيل (رفع وطلب معاينة) -->
        <div wire:ignore.self class="modal fade" id="importModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white py-2">
                        <h6 class="modal-title fw-bold">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i> استيراد الوظائف من ملف إكسيل / CSV
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="mb-3">
                            <label class="form-label fw-bold">اختر الملف (Excel أو CSV): *</label>
                            <input type="file" class="form-control form-control-sm" wire:model="excelFile">
                            @error('excelFile') <span class="text-danger small">{{ $message }}</span> @enderror
                            <small class="text-muted d-block mt-2">
                                ملاحظة: سيتم فحص الملف واستبعاد الوظائف المتكررة أو الموجودة مسبقاً قبل الحفظ النهائي.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">إغلاق</button>
                        <button type="button" wire:click="previewExcel" class="btn btn-success btn-sm px-3 fw-bold">
                            <i class="bi bi-eye me-1"></i> مراجعة وفحص الملف
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal نافذة مراجعة بيانات الإكسيل قبل الحفظ -->
        <div wire:ignore.self class="modal fade" id="previewModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark py-2">
                        <h6 class="modal-title fw-bold">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> مراجعة الوظائف المستخرجة من شيت الإكسيل
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body bg-light">
                        @if($duplicatesCount > 0)
                            <div class="alert alert-danger py-2 small mb-3">
                                <i class="bi bi-info-circle-fill me-1"></i> تم استبعاد <strong>{{ $duplicatesCount }}</strong> وظيفة لأنها متكررة أو موجودة مسبقاً في النظام أو داخل الملف.
                            </div>
                        @endif

                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-bordered bg-white text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>م</th>
                                        <th class="text-start">اسم الوظيفة</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($previewData as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="text-start fw-bold">{{ $item['name'] }}</td>
                                            <td><span class="badge bg-success">{{ $item['status'] }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-muted py-3">لا توجد وظائف جديدة صالحة للاستيراد.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">إلغاء</button>
                        @if(count($previewData) > 0)
                            <button type="button" wire:click="confirmImport" class="btn btn-success btn-sm px-3 fw-bold">
                                <i class="bi bi-check-circle-fill me-1"></i> تأكيد وحفظ البيانات بقاعدة البيانات
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('show-job-modal', () => {
            var myModal = new bootstrap.Modal(document.getElementById('jobModal'));
            myModal.show();
        });
        $wire.on('close-job-modal', () => {
            var myModalEl = document.getElementById('jobModal');
            var modal = bootstrap.Modal.getInstance(myModalEl);
            if (modal) modal.hide();
        });
        $wire.on('close-import-modal', () => {
            var myModalEl = document.getElementById('importModal');
            var modal = bootstrap.Modal.getInstance(myModalEl);
            if (modal) modal.hide();
        });
        $wire.on('show-preview-modal', () => {
            var myModal = new bootstrap.Modal(document.getElementById('previewModal'));
            myModal.show();
        });
        $wire.on('close-preview-modal', () => {
            var myModalEl = document.getElementById('previewModal');
            var modal = bootstrap.Modal.getInstance(myModalEl);
            if (modal) modal.hide();
        });
    </script>
    @endscript
</div>
