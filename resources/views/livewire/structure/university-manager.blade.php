<div class="container-fluid py-3">
    <!-- عنوان الصفحة وزر إضافة جامعة وزر الرفع -->
    <!-- عنوان الصفحة وأزرار التنقل والإجراءات -->
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-mortarboard-fill text-primary me-2"></i>إدارة الجامعات</h4>
            <p class="text-muted small mb-0">إضافة وتكويد الجامعات المختلفة وتحديد أنواعها   </p>
        </div>

        <!-- أزرار التنقل السريع وإجراءات الصفحة -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- أزرار التبديل بين الجامعات والكليات (مع جعل الجامعات هي النشطة هنا) -->
            <div class="btn-group btn-group-sm shadow-sm" role="group">
                <a href="{{ route('universities.index') }}" class="btn btn-primary fw-bold active">
                    <i class="bi bi-mortarboard me-1"></i> الجامعات
                </a>
                <a href="{{ route('colleges.index') }}" class="btn btn-outline-primary fw-bold">
                    <i class="bi bi-buildings me-1"></i> الكليات
                </a>
            </div>

            <div class="vr mx-1"></div>

            <!-- زر تصدير إكسل -->
            <button wire:click="export" class="btn btn-outline-success fw-bold btn-sm px-3 shadow-sm">
                <i class="bi bi-file-earmark-arrow-down me-1"></i> تصدير
            </button>

            <!-- زر رفع ملف الجامعات -->
            <label class="btn btn-outline-primary fw-bold btn-sm px-3 shadow-sm mb-0" style="cursor: pointer;">
                <i class="bi bi-file-earmark-arrow-up me-1"></i> استيراد
                <input type="file" wire:model="importFile" class="d-none" accept=".xlsx,.xls,.csv">
            </label>

            <!-- زر إضافة جامعة -->
            <button wire:click="toggleForm" class="btn btn-primary fw-bold btn-sm px-3 shadow-sm">
                <i class="bi {{ $showForm ? 'bi-dash-lg' : 'bi-plus-lg' }} me-1"></i>
                {{ $showForm ? 'إخفاء' : 'إضافة جامعة' }}
            </button>
        </div>
    </div>

    <!-- رسائل التنبيه والنجاح/الخطأ -->
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
        <!-- نموذج الإدخال (قابل للطي) -->
        @if($showForm)
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-light-subtle">
                    <div class="card-header bg-white fw-bold py-2 border-bottom d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-pencil-square text-primary me-2"></i>{{ $isEdit ? 'تعديل بيانات الجامعة' : 'إضافة جامعة جديدة' }}</span>
                        <button type="button" wire:click="resetFields" class="btn-close" aria-label="إغلاق"></button>
                    </div>
                    <div class="card-body p-3">
                        <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                            <div class="row g-3 align-items-end">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold small mb-1">اسم الجامعة <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="name" class="form-control form-control-sm @error('name') is-invalid @enderror" placeholder="مثال: جامعة سوهاج">
                                    @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-semibold small mb-1">نوع الجامعة <span class="text-danger">*</span></label>
                                    <select wire:model="type" class="form-select form-select-sm @error('type') is-invalid @enderror">
                                        <option value="حكومية">حكومية</option>
                                        <option value="خاصة">خاصة</option>
                                        <option value="أهلية">أهلية</option>
                                        <option value="أخرى">أخرى</option>
                                    </select>
                                    @error('type') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 col-md-2">
                                    <div class="form-check form-switch pt-2">
                                        <input class="form-check-input" type="checkbox" wire:model="is_active" id="isActiveSwitchUni">
                                        <label class="form-check-label fw-semibold small" for="isActiveSwitchUni">مفعلة بالنظام</label>
                                    </div>
                                </div>

                                <div class="col-12 col-md-3 d-flex gap-2">
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

        <!-- جدول الجامعات المسجلة -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-2 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h6 class="fw-bold mb-0 small"><i class="bi bi-list-task text-primary me-2"></i>قائمة الجامعات المسجلة</h6>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" wire:model.live="search" class="form-control form-control-sm" style="width: 250px;" placeholder="بحث باسم أو نوع الجامعة...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th class="ps-4" style="width: 60px;">#</th>
                                    <th>اسم الجامعة</th>
                                    <th>النوع</th>
                                    <th>عدد الكليات</th>
                                    <th>بواسطة المستخدم</th>
                                    <th style="width: 100px;">الحالة</th>
                                    <th class="text-center" style="width: 150px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                @forelse($universities as $index => $university)
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted">{{ $universities->firstItem() + $index }}</td>
                                        <td class="fw-semibold">{{ $university->name }}</td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-dark border rounded-pill px-2 py-1">
                                                {{ $university->type }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info border rounded-pill px-2 py-1">
                                                {{ $university->colleges_count }} كلية
                                            </span>
                                        </td>
                                        <td class="text-muted"><i class="bi bi-person-circle me-1"></i> {{ $university->user->name ?? 'غير معروف' }}</td>
                                        <td>
                                            @if($university->is_active)
                                                <span class="badge bg-success-subtle text-success border rounded-pill px-3 py-1">مفعل</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border rounded-pill px-3 py-1">معطل</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button wire:click="edit({{ $university->id }})" class="btn btn-sm btn-outline-primary me-1 py-0 px-2" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </l>
                                            <button onclick="confirm('هل أنت متأكد من حذف هذه الجامعة؟') || event.stopImmediatePropagation()" wire:click="delete({{ $university->id }})" class="btn btn-sm btn-outline-danger py-0 px-2" title="حذف">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">لا توجد جامعات مسجلة حالياً</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($universities->hasPages())
                    <div class="card-footer bg-white border-0 py-2">
                        {{ $universities->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- نافذة معاينة الاستيراد وفحص التكرار -->
    @if($showPreviewModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1050;">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-eye-fill me-2"></i>معاينة جامعات الاستيراد وفحص التكرار
                        </h5>
                        <button type="button" wire:click="cancelImport" class="btn-close btn-close-white" aria-label="إغلاق"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-warning d-flex align-items-center justify-content-between mb-3 py-2">
                            <div>
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <span>الجامعات باللون الأحمر (مكررة وموجودة مسبقاً بالنظام).</span>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" wire:model="skipDuplicates" id="skipDupCheckUni">
                                <label class="form-check-label small fw-bold" for="skipDupCheckUni">تخطي الجامعات المكررة تلقائياً</label>
                            </div>
                        </div>

                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>#</th>
                                        <th>اسم الجامعة</th>
                                        <th>النوع</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($previewData as $index => $row)
                                        <tr class="{{ $row['is_duplicate'] ? 'table-danger' : 'table-success' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $row['name'] }}</td>
                                            <td>{{ $row['type'] }}</td>
                                            <td>
                                                @if($row['is_duplicate'])
                                                    <span class="badge bg-danger">مكرر (موجودة مسبقاً)</span>
                                                @else
                                                    <span class="badge bg-success">جديدة</span>
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
                            <i class="bi bi-check-circle me-1"></i> تأكيد وحفظ الجامعات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
