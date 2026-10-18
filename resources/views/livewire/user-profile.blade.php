<div>
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            <!-- رسالة النجاح عند تغيير كلمة المرور -->
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- كارت البيانات الشخصية (قراءة فقط في نفس السطر) -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white py-3 px-4 border-0">
                    <h6 class="fw-bold m-0 text-dark">
                        <i class="bi bi-person-badge-fill text-primary me-2"></i> البيانات الشخصية (للقراءة فقط)
                    </h6>
                </div>
                <div class="card-body px-4 pb-4 pt-0">
                    <div class="row g-3">
                        <!-- الاسم -->
                        <div class="col-12 col-md-4">
                            <label class="form-label text-muted small fw-semibold">الاسم الكامل</label>
                            <input type="text" class="form-control bg-light text-dark fw-bold" value="{{ $name }}" disabled readonly>
                        </div>
                        <!-- القسم التابع لها -->
                        <div class="col-12 col-md-4">
                            <label class="form-label text-muted small fw-semibold">القسم التابع لها</label>
                            <input type="text" class="form-control bg-light text-dark fw-bold" value="{{ auth()->user()->department->name ?? '---' }}" disabled readonly>
                        </div>
                        <!-- البريد الإلكتروني -->
                        <div class="col-12 col-md-4">
                            <label class="form-label text-muted small fw-semibold">البريد الإلكتروني</label>
                            <input type="email" class="form-control bg-light text-dark fw-bold" value="{{ $email }}" disabled readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- كارت تغيير كلمة المرور (الثلاثة حقول في نفس السطر) -->
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-white py-3 px-4 border-0">
                    <h6 class="fw-bold m-0 text-dark">
                        <i class="bi bi-shield-lock-fill text-warning me-2"></i> إعادة تعيين كلمة المرور
                    </h6>
                </div>
                <div class="card-body px-4 pb-4 pt-0">
                    <form wire:submit.prevent="updatePassword">
                        <div class="row g-3 align-items-end">
                            <!-- كلمة المرور الحالية -->
                            <div class="col-12 col-md-4">
                                <label class="form-label text-muted small fw-semibold">كلمة المرور الحالية</label>
                                <input type="password" wire:model="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="الحالية">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- كلمة المرور الجديدة -->
                            <div class="col-12 col-md-4">
                                <label class="form-label text-muted small fw-semibold">كلمة المرور الجديدة</label>
                                <input type="password" wire:model="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="الجديدة">
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- تأكيد كلمة المرور الجديدة -->
                            <div class="col-12 col-md-4">
                                <label class="form-label text-muted small fw-semibold">تأكيد كلمة المرور</label>
                                <input type="password" wire:model="new_password_confirmation" class="form-control" placeholder="تأكيد الجديدة">
                            </div>
                        </div>

                        <!-- زر الحفظ في الأسفل بشكل أنيق -->
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold">
                                <i class="bi bi-save-fill me-1"></i> حفظ كلمة المرور الجديدة
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
