<div class="container-fluid py-3">
    <!-- عنوان الصفحة وأزرار الإجراءات -->
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-briefcase-fill text-primary me-2"></i>إدارة المؤهلات الوظيفية</h4>
            <p class="text-muted small mb-0">إضافة وتكويد المؤهلات الوظيفية الخاصة بالعاملين بالنظام</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button wire:click="export" class="btn btn-outline-success fw-bold btn-sm px-3 shadow-sm">
                <i class="bi bi-file-earmark-arrow-down me-1"></i> تصدير
            </button>

            <label class="btn btn-outline-primary fw-bold btn-sm px-3 shadow-sm mb-0" style="cursor: pointer;">
                <i class="bi bi-file-earmark-arrow-up me-1"></i> استيراد
                <input type="file" wire:model="importFile" class="d-none" accept=".xlsx,.xls,.csv">
            </label>

            <button wire:click="toggleForm" class="btn btn-primary fw-bold btn-sm px-3 shadow-sm">
                <i class="bi {{ $showForm ? 'bi-dash-lg' : 'bi-plus-lg' }} me-1"></i>
                {{ $showForm ? 'إخفاء' : 'إضافة مؤهل' }}
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3">
        @if($showForm)
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-light-subtle">
                    <div class="card-header bg-white fw-bold py-2 border-bottom d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-pencil-square text-primary me-2"></i>{{ $isEdit ? 'تعديل المؤهل الوظيفي' : 'إضافة مؤهل جديد' }}</span>
                        <button type="button" wire:click="resetFields" class="btn-close" aria-label="إغلاق"></button>
                    </div>
                    <div class="card-body p-3">
                        <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                            <div class="row g-3 align-items-end">
                                <div class="col-12 col-md-5">
                                    <label class="form-label fw-semibold small mb-1">اسم المؤهل الوظيفي <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="name" class="form-control form-control-sm @error('name') is-invalid @enderror" placeholder="مثال: بكالوريوس / ماجستير / دكتوراه">
                                    @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 col-md-3">
                                    <div class="form-check form-switch pt-2">
                                        <input class="form-check-input" type="checkbox" wire:model="is_active" id="isActiveSwitchSpec">
                                        <label class="form-check-label fw-semibold small" for="isActiveSwitchSpec">مفعل بالنظام</label>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold w-100">
                                        <i class="bi bi-save me-1"></i> {{ $isEdit ? 'تحديث' : 'حفظ' }}
                                    </button>
                                    <button type="button" wire:click="resetFields" class="btn btn-outline-secondary btn-sm w-100">إلغاء</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-2 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h6 class="fw-bold mb-0 small"><i class="bi bi-list-task text-primary me-2"></i>قائمة المؤهلات الوظيفية المسجلة</h6>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" wire:model.live="search" class="form-control form-control-sm" style="width: 250px;" placeholder="بحث باسم المؤهل...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th class="ps-4" style="width: 60px;">#</th>
                                    <th>اسم المؤهل الوظيفي</th>
                                    <th>بواسطة المستخدم</th>
                                    <th style="width: 100px;">الحالة</th>
                                    <th class="text-center" style="width: 150px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                @forelse($specialties as $index => $specialty)
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted">{{ $specialties->firstItem() + $index }}</td>
                                        <td class="fw-semibold">{{ $specialty->name }}</td>
                                        <td class="text-muted"><i class="bi bi-person-circle me-1"></i> {{ $specialty->user->name ?? 'غير معروف' }}</td>
                                        <td>
                                            @if($specialty->is_active)
                                                <span class="badge bg-success-subtle text-success border rounded-pill px-3 py-1">مفعل</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border rounded-pill px-3 py-1">معطل</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button wire:click="edit({{ $specialty->id }})" class="btn btn-sm btn-outline-primary me-1 py-0 px-2" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button onclick="confirm('هل أنت متأكد من حذف هذا المؤهل؟') || event.stopImmediatePropagation()" wire:click="delete({{ $specialty->id }})" class="btn btn-sm btn-outline-danger py-0 px-2" title="حذف">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">لا توجد مؤهلات مسجلة حالياً</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($specialties->hasPages())
                    <div class="card-footer bg-white border-0 py-2">
                        {{ $specialties->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($showPreviewModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1050;">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-eye-fill me-2"></i>معاينة استيراد المؤهلات وفحص التكرار
                        </h5>
                        <button type="button" wire:click="cancelImport" class="btn-close btn-close-white" aria-label="إغلاق"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-warning d-flex align-items-center justify-content-between mb-3 py-2">
                            <div>
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <span>المؤهلات باللون الأحمر مكررة وموجودة مسبقاً بالنظام.</span>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" wire:model="skipDuplicates" id="skipDupCheckSpec">
                                <label class="form-check-label small fw-bold" for="skipDupCheckSpec">تخطي المكرر تلقائياً</label>
                            </div>
                        </div>

                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>#</th>
                                        <th>اسم المؤهل الوظيفي</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($previewData as $index => $row)
                                        <tr class="{{ $row['is_duplicate'] ? 'table-danger' : 'table-success' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $row['name'] }}</td>
                                            <td>
                                                @if($row['is_duplicate'])
                                                    <span class="badge bg-danger">مكرر مسبقاً</span>
                                                @else
                                                    <span class="badge bg-success">جديد</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" wire:click="cancelImport" class="btn btn-outline-secondary btn-sm px-3">إلغاء الأمر</button>
                        <button type="button" wire:click="confirmImport" class="btn btn-primary btn-sm px-4 fw-bold">
                            <i class="bi bi-check-circle me-1"></i> تأكيد وحفظ المؤهلات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
