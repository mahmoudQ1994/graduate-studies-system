<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>إدارة إيقاف قيد الدراسات العليا</h4>
        <button wire:click="openAddModal" class="btn btn-success btn-sm">
            <i class="fas fa-plus"></i> إيقاف قيد جديد
        </button>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif

    <!-- صندوق البحث -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" wire:model.live="search_national_id" class="form-control form-control-sm" placeholder="بحث بالرقم القومي...">
                </div>
                <div class="col-md-6">
                    <input type="text" wire:model.live="search_name" class="form-control form-control-sm" placeholder="بحث باسم الطبيب...">
                </div>
            </div>
        </div>
    </div>

    <!-- الجدول -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped table-sm mb-0 text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>اسم المقيد بالدراسة </th>
                        <th>الرقم القومي</th>
                        <th>بداية الإيقاف</th>
                        <th>نهاية الإيقاف</th>
                        <th>مدة الإيقاف</th>
                        <th>تاريخ استلام العمل</th>
                        <th>سبب الإيقاف</th>
                        <th>التحكم</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pauses as $pause)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-start px-2">{{ $pause->registration->healthProfessional->name ?? '-' }}</td>
                            <td>{{ $pause->registration->healthProfessional->national_id ?? '-' }}</td>
                            <td>{{ $pause->pause_start_date }}</td>
                            <td>{{ $pause->pause_end_date ?? '-' }}</td>
                            <td><span class="badge bg-info text-dark">{{ $pause->pause_duration }}</span></td>
                            <td>{{ $pause->resume_date ?? 'لم يستلم' }}</td>
                            <td>{{ $pause->pause_reason }}</td>
                            <td>
                                <button wire:click="openAddModal(null, {{ $pause->id }})" class="btn btn-sm btn-primary py-0 px-1" title="تعديل الإيقاف">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="deletePause({{ $pause->id }})" class="btn btn-sm btn-danger py-0 px-1" onclick="return confirm('تأكيد الحذف؟')" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-3 text-muted">لا توجد بيانات مسجلة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer py-2">
            {{ $pauses->links() }}
        </div>
    </div>

    <!-- Modal الإضافة أو التعديل للإيقاف -->
    <div wire:ignore.self class="modal fade" id="pauseModal" tabindex="-1">
        <div class="modal-dialog modal-lg"> <!-- تم إرجاعها إلى modal-lg أو استخدام modal-xl لتصبح أوسع بالعرض -->
            <div class="modal-content">
                <div class="modal-header bg-success text-white py-1 px-3">
                    <h6 class="modal-title fs-6">{{ $selectedPauseId ? 'تعديل فترة إيقاف القيد' : 'تسجيل إيقاف قيد جديد' }}</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-2 px-3">
                    <form wire:submit.prevent="savePause">
                        <div class="mb-1">
                            <label class="form-label small fw-bold mb-1">الرقم القومي (14 رقم)</label>
                            <input type="text" wire:model.live.debounce.500ms="modal_national_id" class="form-control form-control-sm @error('registration_id') is-invalid @enderror" maxlength="14" placeholder="أدخل الرقم القومي...">
                            @error('registration_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- تفاصيل المرشح -->
                        @if($selectedRegistration)
                            <div class="card bg-light border-success mb-2 shadow-sm">
                                <div class="card-body py-1 px-2 small">
                                    <div class="row g-1">
                                        <div class="col-md-6">
                                            <strong>اسم المقيد بالدراسة :</strong> {{ $selectedRegistration->healthProfessional->name ?? '-' }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>الوظيفة:</strong> {{ $selectedRegistration->healthProfessional->profession ?? '-' }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>جهة العمل:</strong> {{ $selectedRegistration->healthProfessional->secondment_facility ?? '-' }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>الدراسة:</strong> {{ $selectedRegistration->required_degree ?? '-' }} - {{ $selectedRegistration->required_specialty ?? '-' }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>تاريخ القيد:</strong> {{ $selectedRegistration->created_at?->format('Y-m-d') ?? ($selectedRegistration->registration_date ?? '-') }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>موقف الدراسة:</strong>
                                            <span class="badge bg-secondary">{{ $selectedRegistration->study_status ?? 'ساري' }}</span>
                                        </div>
                                        @if(!empty($selectedRegistration->apology_date) || !empty($selectedRegistration->nominated_degree_date))
                                            <div class="col-12 text-danger border-top pt-1 mt-1">
                                                @if(!empty($selectedRegistration->apology_date))
                                                    <strong>تاريخ  الاعتذار عن الدراسة  (اعتذار):</strong> {{ $selectedRegistration->apology_date }}
                                                @endif
                                                @if(!empty($selectedRegistration->nominated_degree_date))
                                                    <strong>حصل على الدرجة بتاريخ  :</strong> {{ $selectedRegistration->nominated_degree_date }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row g-2 mb-1">
                            <div class="col-6">
                                <label class="form-label small fw-bold mb-1">بداية الإيقاف</label>
                                <input type="date" wire:model="pause_start_date" class="form-control form-control-sm @error('pause_start_date') is-invalid @enderror">
                                @error('pause_start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold mb-1">نهاية الإيقاف</label>
                                <input type="date" wire:model="pause_end_date" class="form-control form-control-sm">
                            </div>
                        </div>

                        <div class="mb-1">
                            <label class="form-label small fw-bold mb-1">تاريخ استلام العمل</label>
                            <input type="date" wire:model="resume_date" class="form-control form-control-sm @error('resume_date') is-invalid @enderror">
                            @error('resume_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-2">
                            <label class="form-label small fw-bold mb-1">سبب الإيقاف</label>
                            <input type="text" wire:model="pause_reason" class="form-control form-control-sm @error('pause_reason') is-invalid @enderror">
                            @error('pause_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="text-end mt-2">
                            <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-success btn-sm px-3" @if(!$selectedRegistration) disabled @endif>حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('open-modal', (event) => {
            new bootstrap.Modal(document.getElementById(event.name)).show();
        });
        $wire.on('close-modal', (event) => {
            let el = document.getElementById(event.name);
            let modal = bootstrap.Modal.getInstance(el);
            if (modal) modal.hide();
        });
    </script>
    @endscript
</div>
