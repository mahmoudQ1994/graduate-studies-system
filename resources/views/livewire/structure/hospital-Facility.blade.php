<div class="container-fluid py-3">
    <!-- عنوان الصفحة وزر إضافة منشأة -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-hospital-fill text-primary me-2"></i>إدارة المستشفيات والجهات الصحية</h4>
            <p class="text-muted small mb-0">تكويد المستشفيات، المراكز الطبية، والوحدات الصحية وربطها بالمراكز والقطاعات</p>
        </div>
        <div>
            <button wire:click="toggleForm" class="btn btn-primary fw-bold btn-sm px-3 shadow-sm">
                <i class="bi {{ $showForm ? 'bi-dash-lg' : 'bi-plus-lg' }} me-1"></i>
                {{ $showForm ? 'إخفاء النموذج' : 'إضافة منشأة صحية' }}
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
        <!-- 1. نموذج الإدخال (قابل للطي) -->
        @if($showForm)
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-light-subtle">
                    <div class="card-header bg-white fw-bold py-2 border-bottom d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-pencil-square text-primary me-2"></i>{{ $isEdit ? 'تعديل بيانات المنشأة' : 'إضافة منشأة صحية جديدة' }}</span>
                        <button type="button" wire:click="resetFields" class="btn-close" aria-label="إغلاق"></button>
                    </div>
                    <div class="card-body p-3">
                        <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                            <div class="row g-3">
                                <div class="col-12 col-md-5">
                                    <label class="form-label fw-semibold small mb-1">اسم المستشفى / الجهة <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="name" class="form-control form-control-sm @error('name') is-invalid @enderror" placeholder="مثال: مستشفى سوهاج العام">
                                    @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-semibold small mb-1">نوع المنشأة <span class="text-danger">*</span></label>
                                   <select wire:model="type" class="form-select">
                                        <option value="">-- اختر نوع الجهة --</option>
                                        <option value="مستشفى عام">ديوان المديرية  </option>
                                        <option value="مستشفى عام">مستشفى عام</option>
                                        <option value="مستشفى مركزى">مستشفى مركزى</option>
                                        <option value="مستشفى نوعى">مستشفى نوعى</option>
                                        <option value="إدارة صحية">إدارة صحية</option>
                                        <option value="وحدة طب أسرة">وحدة طب أسرة</option>
                                    </select>
                                    @error('type') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 col-md-2">
                                    <label class="form-label fw-semibold small mb-1">المركز / المدينة <span class="text-danger">*</span></label>
                                    <select wire:model="district_id" class="form-select form-select-sm @error('district_id') is-invalid @enderror">
                                        <option value="">-- اختر المركز --</option>
                                        @foreach($districts as $district)
                                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('district_id') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 col-md-2">
                                    <label class="form-label fw-semibold small mb-1">القطاع التابع له</label>
                                    <select wire:model="sector_id" class="form-select form-select-sm">
                                        <option value="">-- اختياري --</option>
                                        @foreach($sectors as $sector)
                                            <option value="{{ $sector->id }}">{{ $sector->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 d-flex align-items-center justify-content-between pt-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="is_active" id="isActiveSwitchHosp">
                                        <label class="form-check-label fw-semibold small" for="isActiveSwitchHosp">مفعلة بالنظام</label>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                                            <i class="bi bi-save me-1"></i> {{ $isEdit ? 'تحديث' : 'حفظ' }}
                                        </button>
                                        <button type="button" wire:click="resetFields" class="btn btn-outline-secondary btn-sm">إلغاء</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- 2. جدول الجهات المسجلة -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-2 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h6 class="fw-bold mb-0 small"><i class="bi bi-list-task text-primary me-2"></i>قائمة الجهات والمستشفيات</h6>

                    <!-- الفلاتر والبحث -->
                    <div class="d-flex align-items-center gap-2">
                        <select wire:model.live="filterType" class="form-select form-select-sm" style="width: 170px;">
                            <option value="">جميع الأنواع</option>
                            <option value="مستشفى">مستشفى</option>
                            <option value="مركز طب أسرة">مركز طب أسرة</option>
                            <option value="وحدة صحية">وحدة صحية</option>
                            <option value="إدارة صحية">إدارة صحية</option>
                        </select>
                        <input type="text" wire:model.live="search" class="form-control form-control-sm" style="width: 200px;" placeholder="بحث باسم الجهة...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th class="ps-4" style="width: 60px;">#</th>
                                    <th>اسم المنشأة</th>
                                    <th>النوع</th>
                                    <th>المركز / المدينة</th>
                                    <th>القطاع</th>
                                    <th style="width: 120px;">الحالة</th>
                                    <th class="text-center" style="width: 130px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                @forelse($hospitals as $index => $hospital)
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted">{{ $hospitals->firstItem() + $index }}</td>
                                        <td class="fw-semibold">{{ $hospital->name }}</td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary border rounded-pill px-2 py-1">
                                                {{ $hospital->type }}
                                            </span>
                                        </td>
                                        <td>{{ $hospital->district->name ?? '—' }}</td>
                                        <td>{{ $hospital->sector->name ?? 'غير محدد' }}</td>
                                        <td>
                                            <button wire:click="toggleStatus({{ $hospital->id }})" class="btn btn-sm border-0 bg-transparent p-0">
                                                @if($hospital->is_active)
                                                    <span class="badge bg-success-subtle text-success border rounded-pill px-3 py-1">مفعل</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border rounded-pill px-3 py-1">معطل</span>
                                                @endif
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <button wire:click="edit({{ $hospital->id }})" class="btn btn-sm btn-outline-primary me-1 py-0 px-2" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button onclick="confirm('هل أنت تأكد من حذف هذه المنشأة؟') || event.stopImmediatePropagation()" wire:click="delete({{ $hospital->id }})" class="btn btn-sm btn-outline-danger py-0 px-2" title="حذف">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">لا توجد منشآت صحية مسجلة حالياً</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($hospitals->hasPages())
                    <div class="card-footer bg-white border-0 py-2">
                        {{ $hospitals->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
