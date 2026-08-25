<div class="container-fluid py-3">
    <!-- عنوان الصفحة وزر إضافة مركز -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-geo-alt-fill text-primary me-2"></i>إدارة وتكويد المراكز والمدن</h4>
            <p class="text-muted small mb-0">تكويد المراكز الرئيسية للمحافظة (مثل: سوهاج، أخميم، طهطا، طما...)</p>
        </div>
        <div>
            <button wire:click="toggleForm" class="btn btn-primary fw-bold btn-sm px-3 shadow-sm">
                <i class="bi {{ $showForm ? 'bi-dash-lg' : 'bi-plus-lg' }} me-1"></i>
                {{ $showForm ? 'إخفاء النموذج' : 'إضافة مركز جديد' }}
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
        <!-- 1. نموذج الإدخال والظهور بالشرط -->
        @if($showForm)
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-light-subtle">
                    <div class="card-header bg-white fw-bold py-2 border-bottom d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-pencil-square text-primary me-2"></i>{{ $isEdit ? 'تعديل بيانات مركز' : 'إضافة مركز جديد' }}</span>
                        <button type="button" wire:click="resetFields" class="btn-close" aria-label="إغلاق"></button>
                    </div>
                    <div class="card-body p-3">
                        <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                            <div class="row align-items-end g-3">
                                <div class="col-12 col-md-6 col-lg-5">
                                    <label class="form-label fw-semibold small mb-1">اسم المركز / المدينة <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="name" class="form-control form-control-sm @error('name') is-invalid @enderror" placeholder="مثال: مركز طهطا">
                                    @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 col-md-3 col-lg-3">
                                    <div class="form-check form-switch pt-2">
                                        <input class="form-check-input" type="checkbox" wire:model="is_active" id="isActiveSwitch">
                                        <label class="form-check-label fw-semibold small" for="isActiveSwitch">تفعيل المركز بالنظام</label>
                                    </div>
                                </div>

                                <div class="col-12 col-md-3 col-lg-4 text-end d-flex gap-2 justify-content-end">
                                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                                        <i class="bi bi-save me-1"></i> {{ $isEdit ? 'تحديث البيانات' : 'حفظ المركز' }}
                                    </button>
                                    <button type="button" wire:click="resetFields" class="btn btn-outline-secondary btn-sm">إلغاء</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- 2. جدول المراكز المسجلة -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-2 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 small"><i class="bi bi-list-task text-primary me-2"></i>قائمة المراكز المسجلة</h6>
                    <div style="width: 250px;">
                        <input type="text" wire:model.live="search" class="form-control form-control-sm" placeholder="بحث باسم المركز...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th class="ps-4" style="width: 80px;">#</th>
                                    <th>اسم المركز / المدينة</th>
                                    <th style="width: 150px;">الحالة</th>
                                    <th class="text-center" style="width: 150px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                @forelse($districts as $index => $district)
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted">{{ $districts->firstItem() + $index }}</td>
                                        <td class="fw-semibold">{{ $district->name }}</td>
                                        <td>
                                            <button wire:click="toggleStatus({{ $district->id }})" class="btn btn-sm border-0 bg-transparent p-0">
                                                @if($district->is_active)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">مفعل</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">معطل</span>
                                                @endif
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <button wire:click="edit({{ $district->id }})" class="btn btn-sm btn-outline-primary me-1 py-0 px-2" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button onclick="confirm('هل أنت تأكد من حذف هذا المركز؟') || event.stopImmediatePropagation()" wire:click="delete({{ $district->id }})" class="btn btn-sm btn-outline-danger py-0 px-2" title="حذف">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">لا توجد مراكز مسجلة حالياً</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($districts->hasPages())
                    <div class="card-footer bg-white border-0 py-2">
                        {{ $districts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
