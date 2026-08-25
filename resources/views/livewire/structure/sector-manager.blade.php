<div class="container-fluid py-3">
    <!-- عنوان الصفحة وزر إضافة قطاع -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-diagram-3-fill text-primary me-2"></i>إدارة وتكويد القطاعات</h4>
            <p class="text-muted small mb-0">تكويد القطاعات الجغرافية أو التنظيمية (مثل: قطاع الطب العلاجى او ....  ...)</p>
        </div>
        <div>
            <button wire:click="toggleForm" class="btn btn-primary fw-bold btn-sm px-3 shadow-sm">
                <i class="bi {{ $showForm ? 'bi-dash-lg' : 'bi-plus-lg' }} me-1"></i>
                {{ $showForm ? 'إخفاء النموذج' : 'إضافة قطاع جديد' }}
            </button>
        </div>
    </div>

    <!-- رسالة النجاح -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3">
        <!-- 1. نموذج الإدخال (يظهر عند الضغط) -->
        @if($showForm)
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-light-subtle">
                    <div class="card-header bg-white fw-bold py-2 border-bottom d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-pencil-square text-primary me-2"></i>{{ $isEdit ? 'تعديل بيانات قطاع' : 'إضافة قطاع جديد' }}</span>
                        <button type="button" wire:click="resetFields" class="btn-close" aria-label="إغلاق"></button>
                    </div>
                    <div class="card-body p-3">
                        <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                            <div class="row align-items-end g-3">
                                <div class="col-12 col-md-5">
                                    <label class="form-label fw-semibold small mb-1">اسم القطاع <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="name" class="form-control form-control-sm @error('name') is-invalid @enderror" placeholder="مثال: قطاع ..."> ">
                                    @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold small mb-1">الوصف / ملاحظات</label>
                                    <input type="text" wire:model="description" class="form-control form-control-sm" placeholder="وصف مختصر للقطاع...">
                                </div>

                                <div class="col-12 col-md-3">
                                    <div class="form-check form-switch pt-2">
                                        <input class="form-check-input" type="checkbox" wire:model="is_active" id="isActiveSwitchSector">
                                        <label class="form-check-label fw-semibold small" for="isActiveSwitchSector">تفعيل القطاع بالنظام</label>
                                    </div>
                                </div>

                                <div class="col-12 text-end d-flex gap-2 justify-content-end pt-2">
                                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                                        <i class="bi bi-save me-1"></i> {{ $isEdit ? 'تحديث البيانات' : 'حفظ القطاع' }}
                                    </button>
                                    <button type="button" wire:click="resetFields" class="btn btn-outline-secondary btn-sm">إلغاء</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- 2. جدول القطاعات المسجلة -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-2 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 small"><i class="bi bi-list-task text-primary me-2"></i>قائمة القطاعات المسجلة</h6>
                    <div style="width: 250px;">
                        <input type="text" wire:model.live="search" class="form-control form-control-sm" placeholder="بحث باسم القطاع...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th class="ps-4" style="width: 80px;">#</th>
                                    <th>اسم القطاع</th>
                                    <th>الوصف</th>
                                    <th style="width: 150px;">الحالة</th>
                                    <th class="text-center" style="width: 150px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                @forelse($sectors as $index => $sector)
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted">{{ $sectors->firstItem() + $index }}</td>
                                        <td class="fw-semibold">{{ $sector->name }}</td>
                                        <td class="text-muted">{{ $sector->description ?: '—' }}</td>
                                        <td>
                                            <button wire:click="toggleStatus({{ $sector->id }})" class="btn btn-sm border-0 bg-transparent p-0">
                                                @if($sector->is_active)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">مفعل</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">معطل</span>
                                                @endif
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <button wire:click="edit({{ $sector->id }})" class="btn btn-sm btn-outline-primary me-1 py-0 px-2" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button onclick="confirm('هل أنت تأكد من حذف هذا القطاع؟') || event.stopImmediatePropagation()" wire:click="delete({{ $sector->id }})" class="btn btn-sm btn-outline-danger py-0 px-2" title="حذف">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">لا توجد قطاعات مسجلة حالياً</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($sectors->hasPages())
                    <div class="card-footer bg-white border-0 py-2">
                        {{ $sectors->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
