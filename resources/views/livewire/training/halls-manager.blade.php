<div class="container-fluid py-3">
    <!-- عنوان الصفحة وزر الإضافة -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-primary mb-0">
            <i class="bi bi-building me-1"></i> تكويد قاعات التدريب
        </h5>
        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#hallFormCollapse" aria-expanded="{{ $isEditMode ? 'true' : 'false' }}">
            <i class="bi bi-plus-lg me-1"></i> {{ $isEditMode ? 'تعديل بيانات القاعة' : 'إضافة قاعة جديدة' }}
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
    <div class="collapse {{ $isEditMode ? 'show' : '' }} mb-4" id="hallFormCollapse">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-primary text-white py-2 fw-bold d-flex justify-content-between align-items-center">
                <span>{{ $isEditMode ? 'تعديل قاعة تدريب' : 'إضافة قاعة تدريب جديدة' }}</span>
                @if($isEditMode)
                    <button type="button" wire:click="resetFields" class="btn btn-light btn-sm py-0 px-2" style="font-size: 0.75rem;">إلغاء</button>
                @endif
            </div>
            <div class="card-body bg-light">
                <form wire:submit.prevent="save">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">كود القاعة (تلقائي)</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control @error('code') is-invalid @enderror" wire:model="code">
                                <button type="button" wire:click="generateCode" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></button>
                            </div>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold">اسم القاعة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" wire:model="name" placeholder="مثال: قاعة ابن سينا">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold">مكان القاعة</label>
                            <input type="text" class="form-control form-control-sm" wire:model="location" placeholder="مثال: الدور الثاني - المبنى الرئيسي">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold">عدد الكراسي</label>
                            <input type="number" class="form-control form-control-sm @error('chairs_count') is-invalid @enderror" wire:model="chairs_count" min="0">
                            @error('chairs_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- التجهيزات -->
                        <div class="col-12"><hr class="my-1"></div>
                        <div class="col-12 fw-bold text-secondary small">تجهيزات وإمكانيات القاعة:</div>

                        <div class="col-md-2 form-check">
                            <input class="form-check-input" type="checkbox" id="comp" wire:model="has_computer">
                            <label class="form-check-label small" for="comp">كمبيوتر</label>
                        </div>
                        <div class="col-md-2 form-check">
                            <input class="form-check-input" type="checkbox" id="sound" wire:model="has_sound_system">
                            <label class="form-check-label small" for="sound">سوند سيستم</label>
                        </div>
                        <div class="col-md-2 form-check">
                            <input class="form-check-input" type="checkbox" id="ac" wire:model="has_ac">
                            <label class="form-check-label small" for="ac">تكييف</label>
                        </div>
                        <div class="col-md-2 form-check">
                            <input class="form-check-input" type="checkbox" id="board" wire:model="has_whiteboard">
                            <label class="form-check-label small" for="board">سبورة بيضاء</label>
                        </div>
                        <div class="col-md-2 form-check">
                            <input class="form-check-input" type="checkbox" id="proj" wire:model="has_projector">
                            <label class="form-check-label small" for="proj">شاشة عرض</label>
                        </div>
                        <div class="col-md-2 form-check">
                            <input class="form-check-input" type="checkbox" id="light" wire:model="is_lighting_sufficient">
                            <label class="form-check-label small text-success fw-bold" for="light">الإضاءة كافية</label>
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

    <!-- جدول عرض القاعات -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <span class="fw-bold text-secondary">قائمة قاعات التدريب المسجلة</span>
            <div style="width: 300px;">
                <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="بحث بالاسم، الكود، أو المكان...">
            </div>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped mb-0 text-center align-middle small">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الكود</th>
                        <th>اسم القاعة</th>
                        <th>المكان</th>
                        <th>الكراسي</th>
                        <th>التجهيزات</th>
                        <th>أُنشئت بواسطة</th>
                        <th>آخر تعديل</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($halls as $hall)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $hall->code }}</span></td>
                            <td class="fw-bold">{{ $hall->name }}</td>
                            <td>{{ $hall->location ?? '-' }}</td>
                            <td><span class="badge bg-info-subtle text-info">{{ $hall->chairs_count }} كرسي</span></td>
                            <td class="text-start" style="font-size: 0.75rem;">
                                @if($hall->has_computer)<span class="badge bg-secondary-subtle text-dark me-1">كمبيوتر</span>@endif
                                @if($hall->has_sound_system)<span class="badge bg-secondary-subtle text-dark me-1">صوتيات</span>@endif
                                @if($hall->has_ac)<span class="badge bg-secondary-subtle text-dark me-1">تكييف</span>@endif
                                @if($hall->has_whiteboard)<span class="badge bg-secondary-subtle text-dark me-1">سبورة</span>@endif
                                @if($hall->has_projector)<span class="badge bg-secondary-subtle text-dark me-1">شاشة</span>@endif
                                @if($hall->is_lighting_sufficient)<span class="badge bg-success-subtle text-success">إضاءة جيدة</span>@else<span class="badge bg-danger-subtle text-danger">إضاءة ضعيفة</span>@endif
                            </td>
                            <td class="text-muted" style="font-size: 0.75rem;">{{ $hall->creator->name ?? '-' }}</td>
                            <td class="text-muted" style="font-size: 0.75rem;">
                                {{ $hall->editor->name ?? '-' }} <br>
                                <span class="text-secondary" style="font-size: 0.65rem;">{{ $hall->updated_at->format('Y-m-d H:i') }}</span>
                            </td>
                            <td>
                                <button wire:click="edit({{ $hall->id }})" class="btn btn-outline-primary btn-sm py-0 px-1 me-1" title="تعديل">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button wire:confirm="هل أنت متأكد من رغبتك في حذف هذه القاعة؟" wire:click="delete({{ $hall->id }})" class="btn btn-outline-danger btn-sm py-0 px-1" title="حذف">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">لا توجد قاعات تدريب مسجلة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($halls->hasPages())
            <div class="card-footer bg-white py-2">
                {{ $halls->links() }}
            </div>
        @endif
    </div>
</div>
