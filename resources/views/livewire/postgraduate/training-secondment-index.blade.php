<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3 text-center py-2 shadow-sm rounded-3" role="alert" style="font-size: 0.9rem;">
            <i class="bi bi-check-circle-fill ms-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-gradient bg-primary text-white py-3 px-4 d-flex justify-content-between align-items-center" dir="rtl">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-journal-medical fs-4"></i>
                <h5 class="mb-0 fw-bold">سجلات الإفاد والمد للتدريب</h5>
            </div>
            <div>
                <button type="button" wire:click="export" class="btn btn-light text-success btn-sm px-3 fw-bold shadow-sm rounded-pill ms-2">
                    <i class="bi bi-file-earmark-excel ms-1"></i> تحميل إكسيل
                </button>
                <a href="{{ route('postgraduate.training-secondment') }}" class="btn btn-light text-primary btn-sm px-3 fw-bold shadow-sm rounded-pill">
                    <i class="bi bi-plus-lg ms-1"></i> تسجيل إفاد جديد
                </a>
            </div>
        </div>

        <div class="card-body bg-light bg-opacity-10 p-4" dir="rtl">
            <!-- قسم البحث المتقدم المنظم -->
            <div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary">اسم الموظف</label>
                        <input type="text" wire:model.live.debounce.300ms="search_name" class="form-control form-control-sm" placeholder="بحث بالاسم...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary">الرقم القومي</label>
                        <input type="text" wire:model.live.debounce.300ms="search_national_id" class="form-control form-control-sm" placeholder="الرقم القومي...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary">جهة العمل الأصلية</label>
                        <input type="text" wire:model.live.debounce.300ms="search_facility" class="form-control form-control-sm" placeholder="جهة العمل...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary">جهة الإفاد للتدريب</label>
                        <input type="text" wire:model.live.debounce.300ms="search_training_entity" class="form-control form-control-sm" placeholder="جهة التدريب...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary">من تاريخ</label>
                        <input type="date" wire:model.live="search_from_date" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary">إلى تاريخ</label>
                        <input type="date" wire:model.live="search_to_date" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6 d-flex align-items-end justify-content-end gap-2">
                        <button type="button" wire:click="resetSearch" class="btn btn-outline-secondary btn-sm px-4 rounded-pill">
                            <i class="bi bi-arrow-counterclockwise ms-1"></i> إعادة ضبط
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive bg-white rounded-4 shadow-sm border p-2">
                <table class="table table-hover align-middle text-center mb-0" style="font-size: 0.875rem; min-width: 1500px;">
                    <thead class="table-light text-secondary text-uppercase fs-7 text-nowrap" style="letter-spacing: 0.5px;">
                        <tr>
                            <th class="py-3 rounded-start-3">#</th>
                            <th class="py-3 text-start">الاسم</th>
                            <th class="py-3">الرقم القومي</th>
                            <th class="py-3">الوظيفة</th>
                            <th class="py-3">جهة العمل الأصلية</th>
                            <th class="py-3">نوع الإجراء</th>
                            <th class="py-3">جهة الإفاد للتدريب</th>
                            <th class="py-3">تاريخ بداية الإفاد</th>
                            <th class="py-3">تاريخ نهاية الإفاد</th>
                            <th class="py-3">مدة الإفاد</th>
                            <th class="py-3">أيام الإفاد</th>
                            <th class="py-3 rounded-end-3">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trainings as $index => $item)
                            <tr class="text-nowrap">
                                <td class="text-muted fw-semibold">{{ $trainings->firstItem() + $index }}</td>
                                <td class="fw-bold text-dark text-start">{{ $item->healthProfessional->name ?? '-' }}</td>
                                <td class="font-monospace text-secondary">{{ $item->healthProfessional->national_id ?? '-' }}</td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">{{ $item->healthProfessional->profession ?? '-' }}</span></td>
                                <td class="text-muted">{{ $item->healthProfessional->facility->name ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $item->action_type == 'إفاد' ? 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25' : 'bg-info bg-opacity-10 text-info border border-info border-opacity-25' }} px-2 py-1 rounded-pill">
                                        {{ $item->action_type }}
                                    </span>
                                </td>
                                <td class="fw-semibold text-dark">{{ $item->training_entity }}</td>
                                <td class="font-monospace text-success fw-semibold">{{ $item->start_date }}</td>
                                <td class="font-monospace text-danger fw-semibold">{{ $item->end_date }}</td>
                                <td class="text-muted">{{ $item->duration_months }} شهر</td>
                                <td>
                                    <div class="d-inline-flex flex-wrap justify-content-center gap-1">
                                        @if(is_array($item->training_days))
                                            @foreach($item->training_days as $day)
                                                <span class="badge bg-light text-dark border px-1" style="font-size: 0.7rem;">{{ $day }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" wire:click="edit({{ $item->id }})" class="btn btn-outline-primary btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;" title="تعديل">
                                            <i class="bi bi-pencil-square" style="font-size: 0.85rem;"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;" wire:click="delete({{ $item->id }})" wire:confirm="هل أنت متأكد من حذف هذا السجل؟" title="حذف">
                                            <i class="bi bi-trash" style="font-size: 0.85rem;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-muted py-5 text-center">
                                    <i class="bi bi-folder2-open fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                    لا توجد سجلات مطابقة للبحث الحالي.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $trainings->links() }}
            </div>
        </div>
    </div>

    <!-- Modal التعديل -->
    <div class="modal fade" id="editTrainingModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered" dir="rtl">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold fs-6"><i class="bi bi-pencil-square ms-1"></i> تعديل بيانات الإفاد للتدريب</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit.prevent="update">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">نوع الإجراء</label>
                                <select wire:model="action_type" class="form-select form-select-sm">
                                    <option value="إيفاد">إيفاد</option>
                                    <option value="مد إيفاد">مد إيفاد</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">جهة التدريب</label>
                                <input type="text" wire:model="training_entity" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">القسم / الإدارة التدريبية</label>
                                <input type="text" wire:model="training_department" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">عدد الشهور</label>
                                <input type="number" wire:model.live="duration_months" class="form-control form-control-sm" min="1">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">تاريخ البداية</label>
                                <input type="date" wire:model.live="start_date" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">تاريخ النهاية (تلقائي)</label>
                                <input type="date" wire:model="end_date" class="form-control form-control-sm bg-light" readonly>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="all_days"
                                    @if(is_array($training_days) && count($training_days) == 7) checked @endif
                                    wire:click="toggleAllDays">
                                <label class="form-check-label small fw-bold text-primary" for="all_days">
                                    انتداب كلي (جميع أيام الأسبوع)
                                </label>
                            </div>

                                <!-- أيام الأسبوع الفردية -->
                            <div class="d-flex flex-wrap gap-3">
                                @php
                                    $days = ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];
                                @endphp
                                @foreach($days as $day)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{ $day }}" wire:model.live="training_days" id="day_{{ $loop->index }}">
                                        <label class="form-check-label small" for="day_{{ $loop->index }}">
                                            {{ $day }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        const editModal = new bootstrap.Modal(document.getElementById('editTrainingModal'));

        $wire.on('show-edit-modal', () => {
            editModal.show();
        });

        $wire.on('hide-edit-modal', () => {
            editModal.hide();
        });
    </script>
    @endscript
</div>
