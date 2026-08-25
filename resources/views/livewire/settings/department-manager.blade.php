<div class="container-fluid p-0">
    <!-- هيدر الصفحة -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-diagram-3-fill text-primary me-2"></i>إدارة أقسام الإدارة</h4>
            <p class="text-muted small mb-0">تسجيل وتعديل الأقسام والوحدات التابعة للتعليم الطبي والتدريب</p>
        </div>
        <button wire:click="resetFields" class="btn btn-primary px-3 rounded-3 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#departmentModal">
            <i class="bi bi-plus-lg me-1"></i> إضافة قسم جديد
        </button>
    </div>

    <!-- رسالة النجاح -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- جدول الأقسام -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 border-0 rounded-top-4">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" wire:model.live="search" class="form-control bg-light border-start-0 ps-0" placeholder="بحث باسم أو كود القسم...">
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>كود القسم</th>
                            <th>اسم القسم</th>
                            <th>الوصف</th>
                            <th>الحالة</th>
                            <th class="text-end pe-4">التحكم</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $department)
                            <tr>
                                <td class="ps-4 fw-bold text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold px-2 py-1">
                                        {{ $department->code ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="fw-bold text-dark">{{ $department->name }}</td>
                                <td class="text-muted small">{{ Str::limit($department->description, 50) ?? 'لا يوجد وصف' }}</td>
                                <!-- زر تغيير الحالة بنص واضح وتباين ممتاز -->
                                <td>
                                    <button wire:click="toggleStatus({{ $department->id }})"
                                            class="btn btn-sm px-3 py-1 rounded-pill fw-bold border-0 d-inline-flex align-items-center gap-1 shadow-sm {{ $department->is_active ? 'btn-success text-white' : 'btn-danger text-white' }}">
                                        <i class="bi {{ $department->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                        <span>{{ $department->is_active ? 'نشط ومفعل' : 'معطل وموقف' }}</span>
                                    </button>
                                </td>
                                <td class="text-end pe-4">
                                    <button wire:click="edit({{ $department->id }})" class="btn btn-light btn-sm rounded-3 text-primary me-1" data-bs-toggle="modal" data-bs-target="#departmentModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button wire:confirm="هل أنت تأكد من حذف هذا القسم؟" wire:click="delete({{ $department->id }})" class="btn btn-light btn-sm rounded-3 text-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">لا توجد أقسام مسجلة حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-0 py-3 rounded-bottom-4">
            {{ $departments->links() }}
        </div>
    </div>

    <!-- نافذة الإضافة والتعديل Modal -->
    <div wire:ignore.self class="modal fade" id="departmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        {{ $isEditMode ? 'تعديل بيانات القسم' : 'إضافة قسم جديد' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body py-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">اسم القسم <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" class="form-control bg-light @error('name') is-invalid @enderror" placeholder="مثال: قسم الدراسات العليا">
                            @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                         <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">كود القسم (توليد تلقائي)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-qr-code"></i></span>
                                <input type="text" wire:model="code" class="form-control bg-light @error('code') is-invalid @enderror" readonly>
                            </div>
                            @error('code') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">الوصف (اختياري)</label>
                            <textarea wire:model="description" class="form-control bg-light" rows="3" placeholder="ملاحظات أو مهام القسم..."></textarea>
                        </div>

                        <div class="form-check form-switch">
                            <input wire:model="is_active" class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                            <label class="form-check-label fw-semibold text-secondary small" for="flexSwitchCheckDefault">تفعيل القسم في النظام</label>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- سكريبت إغلاق الـ Modal عند الحفظ -->
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('close-modal', () => {
            const modalElement = document.getElementById('departmentModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) modal.hide();
        });
    });
</script>
