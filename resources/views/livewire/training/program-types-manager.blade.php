<div class="container-fluid py-3">
    <!-- عنوان الصفحة وزر إظهار/إخفاء نموذج الإضافة -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-primary mb-0">
            <i class="bi bi-list-task me-1"></i> إدارة أنواع البرامج التدريبية
        </h5>
        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#formCollapse" aria-expanded="{{ $isEditMode ? 'true' : 'false' }}" aria-controls="formCollapse">
            <i class="bi bi-plus-lg me-1"></i> {{ $isEditMode ? 'تعديل البيانات الحالية' : 'إضافة نوع برنامج جديد' }}
        </button>
    </div>

    <!-- رسائل النجاح والخطأ -->
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

    <!-- نموذج الإضافة / التعديل (قابل للطي ويفتح تلقائياً عند التعديل) -->
    <div class="collapse {{ $isEditMode ? 'show' : '' }} mb-4" id="formCollapse">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-primary text-white py-2 fw-bold d-flex justify-content-between align-items-center">
                <span>{{ $isEditMode ? 'تعديل نوع برنامج' : 'إضافة نوع برنامج جديد' }}</span>
                @if($isEditMode)
                    <button type="button" wire:click="resetFields" class="btn btn-light btn-sm py-0 px-2" style="font-size: 0.75rem;">إلغاء التعديل</button>
                @endif
            </div>
            <div class="card-body bg-light">
                <form wire:submit.prevent="save">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">اسم نوع البرنامج <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" wire:model="name" placeholder="مثال: ورشة عمل، مؤتمر، دبلومة">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold">الكود (تلقائي)</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control @error('code') is-invalid @enderror" wire:model="code" placeholder="مثال: TP0001">
                                <button type="button" wire:click="generateCode" class="btn btn-outline-secondary" title="توليد كود جديد">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold">الوصف</label>
                            <input type="text" class="form-control form-control-sm" wire:model="description" placeholder="ملاحظات أو وصف مختصر">
                        </div>

                        <div class="col-md-2 d-flex align-items-center pt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="isActive" wire:model="is_active">
                                <label class="form-check-label small fw-bold" for="isActive">مفعل</label>
                            </div>
                        </div>

                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-success btn-sm px-4">
                                <i class="bi bi-check-circle me-1"></i> {{ $isEditMode ? 'تحديث البيانات' : 'حفظ البيانات' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- جدول العرض والبحث (بعرض الشاشة بالكامل) -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <span class="fw-bold text-secondary">قائمة أنواع البرامج المسجلة</span>
            <div style="width: 300px;">
                <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="بحث باسم النوع أو الكود...">
            </div>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped mb-0 text-center align-middle small">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 25%;">اسم نوع البرنامج</th>
                        <th style="width: 15%;">الكود</th>
                        <th style="width: 35%;">الوصف</th>
                        <th style="width: 10%;">الحالة</th>
                        <th style="width: 10%;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($types as $type)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold text-start ps-4">{{ $type->name }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $type->code ?? '-' }}</span></td>
                            <td class="text-muted text-start">{{ $type->description ?? '-' }}</td>
                            <td>
                                <button wire:click="toggleStatus({{ $type->id }})" class="btn btn-sm border-0 p-0">
                                    @if($type->is_active)
                                        <span class="badge bg-success-subtle text-success px-2 py-1">مفعل</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1">معطل</span>
                                    @endif
                                </button>
                            </td>
                            <td>
                                <button wire:click="edit({{ $type->id }})" class="btn btn-outline-primary btn-sm py-0 px-1 me-1" title="تعديل">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button wire:confirm="هل أنت تأكد من رغبتك في حذف هذا النوع؟" wire:click="delete({{ $type->id }})" class="btn btn-outline-danger btn-sm py-0 px-1" title="حذف">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">لا توجد أنواع برامج تدريبية مسجلة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($types->hasPages())
            <div class="card-footer bg-white py-2">
                {{ $types->links() }}
            </div>
        @endif
    </div>
</div>
