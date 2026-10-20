<div class="container-fluid py-3">
    <!-- عنوان الصفحة وزر الإضافة -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-primary mb-0">
            <i class="bi bi-award me-1"></i> قائمة الدرجات الوظيفية
        </h5>
        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#gradeFormCollapse" aria-expanded="{{ $isEditMode ? 'true' : 'false' }}">
            <i class="bi bi-plus-lg me-1"></i> {{ $isEditMode ? 'تعديل البيانات' : 'إضافة درجة وظيفية جديدة' }}
        </button>
    </div>

    <!-- رسائل التنبيه -->
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

    <!-- نموذج الإضافة والتعديل -->
    <div class="collapse {{ $isEditMode ? 'show' : '' }} mb-4" id="gradeFormCollapse">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-primary text-white py-2 fw-bold d-flex justify-content-between align-items-center">
                <span>{{ $isEditMode ? 'تعديل الدرجة الوظيفية' : 'إضافة درجة وظيفية جديدة' }}</span>
                @if($isEditMode)
                    <button type="button" wire:click="resetFields" class="btn btn-light btn-sm py-0 px-2" style="font-size: 0.75rem;">إلغاء</button>
                @endif
            </div>
            <div class="card-body bg-light">
                <form wire:submit.prevent="save">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">كود الدرجة (تلقائي)</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control @error('code') is-invalid @enderror" wire:model="code">
                                <button type="button" wire:click="generateCode" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></button>
                            </div>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">اسم الدرجة الوظيفية <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" wire:model="name" placeholder="مثال: الاولى ، استشاري">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-5">
                            <label class="form-label small fw-bold">الوصف أو الملاحظات</label>
                            <input type="text" class="form-control form-control-sm" wire:model="description" placeholder="ملاحظات تفصيلية">
                        </div>

                        <div class="col-12 text-end mt-3">
                            <button type="submit" class="btn btn-success btn-sm px-4">
                                <i class="bi bi-check-circle me-1"></i> {{ $isEditMode ? 'تحديث البيانات' : 'حفظ البيانات' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- جدول العرض -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <span class="fw-bold text-secondary">قائمة الدرجات الوظيفية المسجلة</span>
            <div style="width: 300px;">
                <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="بحث بالاسم، الكود، أو الوصف...">
            </div>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped mb-0 text-center align-middle small">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الكود</th>
                        <th>اسم الدرجة الوظيفية</th>
                        <th>الوصف</th>
                        <th>أُضيف بواسطة</th>
                        <th>آخر تعديل وتاريخه</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grades as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $item->code }}</span></td>
                            <td class="fw-bold">{{ $item->name }}</td>
                            <td class="text-muted text-start">{{ $item->description ?? '-' }}</td>
                            <td class="text-muted" style="font-size: 0.75rem;">{{ $item->creator->name ?? '-' }}</td>
                            <td class="text-muted" style="font-size: 0.75rem;">
                                {{ $item->editor->name ?? '-' }} <br>
                                <span class="text-secondary" style="font-size: 0.65rem;">{{ $item->updated_at->format('Y-m-d H:i') }}</span>
                            </td>
                            <td>
                                <button wire:click="edit({{ $item->id }})" class="btn btn-outline-primary btn-sm py-0 px-1 me-1" title="تعديل">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button wire:confirm="هل أنت متأكد من رغبتك في حذف هذا السجل؟" wire:click="delete({{ $item->id }})" class="btn btn-outline-danger btn-sm py-0 px-1" title="حذف">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">لا توجد درجات وظيفية مسجلة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($grades->hasPages())
            <div class="card-footer bg-white py-2">
                {{ $grades->links() }}
            </div>
        @endif
    </div>
</div>
