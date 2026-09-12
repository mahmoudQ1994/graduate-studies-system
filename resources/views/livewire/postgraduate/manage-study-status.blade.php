<div>
    <!-- رأس الصفحة أو العنوان إن وجد -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark m-0">متابعة مواقف دراسات الدراسات العليا</h4>
        <button wire:click="exportExcel" class="btn btn-success btn-sm shadow-sm">
            <i class="fa fa-file-excel me-1"></i> تحميل إكسيل
        </button>
    </div>

    <!-- رسالة النجاح -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- منطقة البحث والتصفية -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-3 bg-light rounded-4">
            <div class="row g-2">
                <!-- 1. حقل البحث بالاسم -->
                <div class="col-md">
                    <label class="form-label small fw-bold text-secondary mb-1">البحث بالاسم</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                        <input type="text" wire:model.live="search_name" class="form-control" placeholder="اسم الطبيب...">
                    </div>
                </div>

                <!-- 2. حقل البحث بالرقم القومي -->
                <div class="col-md">
                    <label class="form-label small fw-bold text-secondary mb-1">الرقم القومي</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-card-text"></i></span>
                        <input type="text" wire:model.live="search_national_id" class="form-control" placeholder="الرقم القومي...">
                    </div>
                </div>

                <!-- 3. حقل البحث بنوع الدراسة -->
                <div class="col-md">
                    <label class="form-label small fw-bold text-secondary mb-1">نوع الدراسة</label>
                    <select wire:model.live="filter_required_degree" class="form-select form-select-sm">
                        <option value="">كل الأنواع</option>
                        <option value="دبلوم">دبلوم</option>
                        <option value="ماجستير">ماجستير</option>
                        <option value="دكتوراة">دكتوراة</option>
                    </select>
                </div>

                <!-- 4. حقل البحث بموقف الدراسة -->
                <div class="col-md">
                    <label class="form-label small fw-bold text-secondary mb-1">موقف الدراسة</label>
                    <select wire:model.live="filter_study_status" class="form-select form-select-sm">
                        <option value="">كل الحالات</option>
                        <option value="جاري فحص الطلب">جاري فحص الطلب</option>
                        <option value="مستمر">مستمر بالدراسة</option>
                        <option value="حصل على الدرجة">حصل على الدرجة</option>
                        <option value="اعتذار">اعتذار عن الدراسة</option>
                        <option value="عدم القبول بالدراسة">عدم القبول بالدراسة</option>
                    </select>
                </div>

                <!-- 5. حقل البحث بتاريخ تسجيل الطلب -->
                <div class="col-md">
                    <label class="form-label small fw-bold text-secondary mb-1">تاريخ تسجيل الطلب</label>
                    <input type="date" wire:model.live="filter_application_date" class="form-control form-control-sm">
                </div>
            </div>
        </div>
    </div>

    <!-- جدول عرض البيانات -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0" style="font-size: 0.875rem; white-space: nowrap;">
                    <thead class="table-dark text-white text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="py-3 px-3 text-start">الاسم</th>
                            <th class="py-3 px-2">الرقم القومي</th>
                            <th class="py-3 px-2">الوظيفة</th>
                            <th class="py-3 px-2">الدراسة المسجل بها</th>
                            <th class="py-3 px-2 text-start">تخصص الدراسة المطلوبة</th>
                            <th class="py-3 px-2">الجامعة المطلوب الدراسة بها</th>
                            <th class="py-3 px-2">تاريخ تسجيل الطلب</th>
                            <th class="py-3 px-2">موقف تنفيذ الدراسة</th>
                            <th class="py-3 px-2">تاريخ القيد بالدراسة</th>
                            <th class="py-3 px-2">موقف الحصول على الدرجة</th>
                            <th class="py-3 px-2">مدة الدراسة</th>
                            <th class="py-3 px-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrations as $reg)
                            <tr>
                                <!-- 1. الاسم -->
                                <td class="fw-bold text-dark text-start px-3 py-3">
                                    {{ $reg->healthProfessional->name ?? '-' }}
                                </td>

                                <!-- 2. الرقم القومي -->
                                <td class="text-muted small">
                                    {{ $reg->healthProfessional->national_id ?? '-' }}
                                </td>

                                <!-- 3. الوظيفة -->
                                <td class="small fw-semibold text-secondary">
                                    {{ $reg->healthProfessional->profession ?? '-' }}
                                </td>

                                <!-- 4. الدراسة المسجل بها -->
                                <td>
                                    <span class="badge bg-light text-primary border fw-bold px-2 py-1">
                                        {{ $reg->required_degree ?? '-' }}
                                    </span>
                                </td>

                                <!-- 5. تخصص الدراسة المطلوبة -->
                                <td class="text-start text-muted">
                                    {{ $reg->required_specialty ?? '-' }}
                                </td>

                                <!-- 6. الجامعة المطلوب الدراسة بها -->
                                <td class="small">
                                    {{ $reg->required_university ?? '-' }}
                                </td>

                                <!-- 7. تاريخ تسجيل الطلب -->
                                <td class="small text-muted">
                                    {{ $reg->application_date ? \Carbon\Carbon::parse($reg->application_date)->format('Y-m-d') : '-' }}
                                </td>

                                <!-- 8. موقف تنفيذ الدراسة -->
                                <td>
                                    @php
                                        $badgeColor = match($reg->study_status) {
                                            'مستمر' => 'bg-info-subtle text-info-emphasis',
                                            'حصل على الدرجة' => 'bg-success-subtle text-success-emphasis',
                                            'اعتذار' => 'bg-danger-subtle text-danger-emphasis',
                                            'عدم القبول بالدراسة' => 'bg-warning-subtle text-warning-emphasis',
                                            default => 'bg-secondary-subtle text-secondary-emphasis'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeColor }} px-2 py-1 fw-semibold">
                                        {{ $reg->study_status ?? 'جاري فحص الطلب' }}
                                    </span>
                                    @if($reg->study_status == 'اعتذار' && $reg->apology_reason)
                                        <div class="text-danger small mt-1" title="{{ $reg->apology_reason }}">
                                            {{ Str::limit($reg->apology_reason, 20) }}
                                        </div>
                                    @elseif($reg->study_status == 'عدم القبول بالدراسة' && $reg->rejection_reason)
                                        <div class="text-warning small mt-1" title="{{ $reg->rejection_reason }}">
                                            {{ Str::limit($reg->rejection_reason, 20) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- 9. تاريخ القيد بالدراسة -->
                                <td class="small fw-semibold">
                                    {{ $reg->registration_date ? \Carbon\Carbon::parse($reg->registration_date)->format('Y-m-d') : '-' }}
                                </td>

                                <!-- 10. موقف الحصول على الدرجة المرشح لها -->
                                <td class="small text-muted">
                                    @if($reg->nominated_degree_status)
                                        <span class="fw-bold text-dark">{{ $reg->nominated_degree_status }}</span>
                                        @if($reg->degree_grade)
                                            <div class="text-primary small">التقدير: {{ $reg->degree_grade }}</div>
                                        @endif
                                    @elseif($reg->study_status == 'حصل على الدرجة')
                                        <span class="text-success fw-bold">حصل عليها</span>
                                        @if($reg->degree_grade)
                                            <div class="text-primary small">التقدير: {{ $reg->degree_grade }}</div>
                                        @endif
                                    @elseif($reg->study_status == 'اعتذار')
                                        <span class="text-danger fw-bold">اعتذر ولم يحصل على الدرجة</span>
                                    @elseif($reg->study_status == 'عدم القبول بالدراسة')
                                        <span class="text-warning fw-bold">مرفوض
                                            (في: {{ $reg->rejection_date  ? \Carbon\Carbon::parse($reg->rejection_date)->format('Y-m-d') : '-' }})</span>
                                    @else
                                        -
                                    @endif

                                    @if($reg->nominated_degree_date)
                                        <div class="text-success small">في: {{ $reg->nominated_degree_date  ? \Carbon\Carbon::parse($reg->nominated_degree_date)->format('Y-m-d') : '-' }}</div>
                                    @endif
                                </td>

                                <!-- 11. مدة الدراسة -->
                                <td>
                                    <span class="badge bg-light text-success border px-2 py-1">
                                        {{ $reg->years_from_registration ?? '-' }}
                                    </span>
                                </td>

                                <!-- 12. إجراء تحديث موقف الدراسة -->
                                <td class="px-3">
                                    <button wire:click="editStatus({{ $reg->id }})" class="btn btn-sm btn-light text-primary border px-2 py-1 rounded-pill" title="تحديث الموقف">
                                        <i class="bi bi-pencil-square"></i> تحديث
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-muted py-5 text-center">لا توجد سجلات دراسية مطابقة للبحث.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- الترقيم (Pagination) -->
        @if($registrations->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>

    <!-- نموذج تعديل الموقف الدراسي -->
    <div wire:ignore.self class="modal fade" id="studyStatusModal" tabindex="-1" aria-labelledby="studyStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-3">

                <div class="modal-header bg-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-bold m-0" id="studyStatusModalLabel">
                        <i class="bi bi-pencil-square me-2"></i> تحديث الموقف الدراسي
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form wire:submit.prevent="updateStatus">
                    <div class="modal-body p-4 bg-light">
                        <div class="row g-3">

                            <!-- اختيار موقف الدراسة -->
                            <!-- قائمة اختيار حالة الدراسة -->
                            <div class="mb-3">
                                <label class="form-label">حالة الدراسة</label>
                                <select wire:model.live="study_status" class="form-control">
                                    <option value="جاري فحص الطلب">جاري فحص الطلب</option>
                                    <option value="تنفيذ دراسة">تنفيذ دراسة</option>
                                    <option value="حصل على الدرجة">حصل على الدرجة</option>
                                    <option value="اعتذار">اعتذار</option>
                                    <option value="عدم القبول بالدراسة">عدم القبول بالدراسة</option>
                                </select>
                            </div>

                            <!-- 1. حقول حالة: تنفيذ دراسة -->
                            @if($study_status == 'تنفيذ دراسة')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">تاريخ تنفيذ الدراسة</label>
                                        <input type="date" wire:model.live="execution_date" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">تاريخ القيد بالدراسة</label>
                                        <input type="date" wire:model.live="registration_date" class="form-control">
                                    </div>
                                </div>
                            @endif

                            <!-- 2. حقول حالة: حصل على الدرجة -->
                            @if($study_status == 'حصل على الدرجة')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">تاريخ القيد بالدراسة</label>
                                        <input type="date" wire:model.live="registration_date" class="form-control">
                                    </div>
                                    @if($study_status == 'تنفيذ دراسة')
                                        <div class="form-group">
                                            <label>تاريخ تنفيذ الدراسة</label>
                                            <input type="date" wire:model="execution_date" class="form-control">
                                            @error('execution_date') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    @endif
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">تاريخ الحصول على الدرجة</label>
                                        <input type="date" wire:model.live="nominated_degree_date" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">تقدير الحصول على الدرجة</label>
                                        <input type="text" wire:model="degree_grade" class="form-control" placeholder="مثال: امتياز، جيد جداً">
                                    </div>
                                </div>
                            @endif

                            <!-- 3. حقول حالة: اعتذار (تمت الإضافة بناءً على المراجعة) -->
                            @if($study_status == 'اعتذار')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">تاريخ الاعتذار</label>
                                        <input type="date" wire:model.live="apology_date" class="form-control">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">سبب الاعتذار</label>
                                        <textarea wire:model="apology_reason" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            @endif

                            <!-- 4. حقول حالة: عدم القبول بالدراسة -->
                            @if($study_status == 'عدم القبول بالدراسة')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">تاريخ عدم القبول بالدراسة</label>
                                        <input type="date" wire:model="rejection_date" class="form-control">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">سبب عدم القبول بالدراسة</label>
                                        <textarea wire:model="rejection_reason" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            @endif

                            <!-- حقل مدة الدراسة (يظهر في جميع الحالات ويتحدث تلقائياً) -->
                            <div class="mb-3 alert alert-info">
                                <label class="form-label fw-bold">مدة الدراسة المحسوبة:</label>
                                <div class="fs-5 text-dark">{{ $years_from_registration }}</div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer bg-white px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">إغلاق</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-save me-1"></i> حفظ التغييرات
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Script لإدارة فتح وإغلاق الـ Modal عبر Livewire Events -->
    @script
    <script>
        $wire.on('open-modal', () => {
            var myModal = new bootstrap.Modal(document.getElementById('studyStatusModal'));
            myModal.show();
        });

        $wire.on('close-modal', () => {
            var myModalEl = document.getElementById('studyStatusModal');
            var modal = bootstrap.Modal.getInstance(myModalEl);
            if (modal) {
                modal.hide();
            }
        });
    </script>
    @endscript
</div>
