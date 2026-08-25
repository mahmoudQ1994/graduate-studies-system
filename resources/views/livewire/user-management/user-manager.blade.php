<div class="container-fluid py-3">
    <!-- الهيدر والزر الرئيسي -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-people-fill text-primary me-2">
                </i>إدارة المستخدمين وصلاحيات الأقسام</h4>
            <p class="text-muted small mb-0"
            >إضافة الموظفين ورؤساء الأقسام، تحديد الصلاحيات وتعيين الأقسام التابعة لهم</p>
        </div>
        <div>
            <button wire:click="toggleForm" class="btn btn-primary fw-bold btn-sm px-3 shadow-sm">
                <i class="bi {{ $showForm ? 'bi-dash-lg' : 'bi-plus-lg' }} me-1"></i>
                {{ $showForm ? 'إخفاء النموذج' : 'إضافة مستخدم جديد' }}
            </button>
        </div>
    </div>

    <!-- رسائل النظام -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- 1. نموذج إضافة / تعديل مستخدم -->
    @if($showForm)
        <div class="card border-0 shadow-sm rounded-4 bg-light-subtle mb-3">
            <div class="card-header bg-white fw-bold py-2 border-bottom d-flex
            align-items-center justify-content-between">
                <span><i class="bi bi-person-plus text-primary me-2">
                    </i>{{ $isEdit ? 'تعديل بيانات الحساب والصلاحيات' : 'إنشاء حساب جديد' }}</span>
                <button type="button" wire:click="resetFields" class="btn-close"></button>
            </div>
            <div class="card-body p-3">
                <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small mb-1">الاسم بالكامل <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name"
                             class="form-control form-control-sm @error('name') is-invalid @enderror" placeholder="أدخل اسم الموظف">
                            @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small mb-1">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" wire:model="email"
                            class="form-control form-control-sm @error('email') is-invalid @enderror" placeholder="name@domain.com">
                            @error('email') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                        </div>

                        @if(!$isEdit)
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold small mb-1">كلمة السر <span class="text-danger">*</span></label>
                                <input type="password" wire:model="password"
                                class="form-control form-control-sm
                                 @error('password') is-invalid
                                  @enderror
                                  " placeholder="******">
                                  @error('password')
                                <div class="invalid-feedback small">{{ $message }}</div>
                                 @enderror
                            </div>
                        @endif

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small mb-1">دور المستخدم الأساسي <span class="text-danger">*</span></label>
                            <select wire:model.live="role"
                             class="form-select form-select-sm
                             @error('role') is-invalid @enderror">
                                <option value="Super Admin">سوبر أدمن (Super Admin)</option>
                                <option value="Department Admin">مسئول قسم (Department Admin)</option>
                                <option value="Department Employee">موظف قسم (Department Employee)</option>
                            </select>
                            @error('role') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                        </div>

                        @if($role !== 'Super Admin')
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold small mb-1">القسم التابع له <span class="text-danger">*</span></label>
                                <select wire:model="department_id" class="form-select form-select-sm @error('department_id') is-invalid @enderror">
                                    <option value="">-- اختر القسم --</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                <div class="invalid-feedback small">{{ $message }}</div>
                                 @enderror
                            </div>
                        @endif

                        <!-- قسم الصلاحيات التفصيلية الاستثنائية -->
                        @if($allPermissions->count() > 0)
                            <div class="col-12 border-top pt-3 mt-3">
                                <label class="form-label fw-bold text-dark small mb-2">
                                    <i class="bi bi-shield-lock text-warning me-1"></i>
                                    صلاحيات استثنائية (مباشرة لهذا المستخدم):
                                </label>
                                <div class="row g-2 bg-white p-2 border rounded-3 ms-0 me-0">
                                    @foreach($allPermissions as $perm)
                                        <div class="col-6 col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="{{ $perm->name }}" wire:model="user_permissions" id="perm_{{ $perm->id }}">
                                                <label class="form-check-label small" for="perm_{{ $perm->id }}">
                                                    {{ $perm->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <span
                                class="text-muted small fs-7 mt-1 d-block">ملاحظة: الصلاحيات المختارة هنا تُضاف للمستخدم بشكل مباشر بالإضافة لصلاحيات دوره الأساسي.</span>
                            </div>
                        @endif

                        <div class="col-12 d-flex align-items-center justify-content-between pt-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                wire:model="is_active" id="userActive">
                                <label class="form-check-label fw-semibold small"
                                 for="userActive">حساب مفعل</label>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                                    <i class="bi bi-save me-1">
                                        </i> {{ $isEdit ? 'تحديث' : 'حفظ' }}
                                </button>
                                <button type="button" wire:click="resetFields"
                                 class="btn btn-outline-secondary btn-sm">إلغاء</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- 2. جدول المستخدمين -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-2 border-bottom
        d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h6 class="fw-bold mb-0 small">
                <i class="bi bi-list-task text-primary me-2">
                </i>قائمة مستخدمي النظام</h6>

            <div class="d-flex align-items-center gap-2">
                <select wire:model.live="filterRole" class="form-select form-select-sm"
                style="width: 160px;">
                    <option value="">جميع الصلاحيات</option>
                    <option value="Super Admin">سوبر أدمن</option>
                    <option value="Department Admin">مسئول قسم</option>
                    <option value="Department Employee">موظف قسم</option>
                </select>

                <select wire:model.live="filterDepartment"
                 class="form-select form-select-sm" style="width: 160px;">
                    <option value="">جميع الأقسام</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>

                <input type="text" wire:model.live="search" autocomplete="off"
                    name="search_query_no_autofill" id="search_query_no_autofill"
                    class="form-control form-control-sm"
                    style="width: 180px;"placeholder="بحث بالاسم أو البريد...">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th class="ps-4" style="width: 50px;">#</th>
                            <th>المستخدم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الصلاحية / الدور</th>
                            <th>القسم التابع له</th>
                            <th>الحالة</th>
                            <th class="text-center" style="width: 150px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse($users as $index => $user)
                            <tr>
                                <td class="ps-4 fw-bold text-muted">{{ $users->firstItem() + $index }}</td>
                                <td class="fw-semibold">
                                    {{ $user->name }}
                                    @if($user->getDirectPermissions()->count() > 0)
                                        <i class="bi bi-star-fill text-warning ms-1" title="يملك صلاحيات خاصة استثنائية"></i>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $r)
                                        @if($r->name === 'Super Admin')
                                            <span class="badge bg-danger-subtle text-danger border rounded-pill px-2 py-1">سوبر أدمن</span>
                                        @elseif($r->name === 'Department Admin')
                                            <span class="badge bg-primary-subtle text-primary border rounded-pill px-2 py-1">مسئول قسم</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-dark border rounded-pill px-2 py-1">موظف قسم</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td>{{ $user->department->name ?? '— (عام / كافة الأقسام)' }}</td>
                                <td>
                                    <button wire:click="toggleStatus({{ $user->id }})" class="btn btn-sm border-0 bg-transparent p-0" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                        @if($user->is_active)
                                            <span class="badge bg-success-subtle text-success border rounded-pill px-3 py-1">مفعل</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border rounded-pill px-3 py-1">معطل</span>
                                        @endif
                                    </button>
                                </td>
                                <td class="text-center">
                                    <button wire:click="edit({{ $user->id }})" class="btn btn-sm btn-outline-primary me-1 py-0 px-2" title="تعديل البيانات والصلاحيات">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button wire:click="$set('selected_user_id', {{ $user->id }})" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" class="btn btn-sm btn-outline-warning me-1 py-0 px-2" title="تغيير كلمة السر">
                                        <i class="bi bi-key"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <button onclick="confirm('هل أنت تأكد من حذف هذا الحساب؟') || event.stopImmediatePropagation()" wire:click="delete({{ $user->id }})" class="btn btn-sm btn-outline-danger py-0 px-2" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">لا يوجد مستخدمين مطابقين للبحث</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
            <div class="card-footer bg-white border-0 py-2">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- نافذة تعيين كلمة السر Modal -->
    <div wire:ignore.self class="modal fade" id="resetPasswordModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header py-2">
                    <h6 class="modal-header-title fw-bold mb-0">تغيير كلمة السر</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">كلمة السر الجديدة</label>
                        <input type="password" wire:model="new_password" class="form-control form-control-sm @error('new_password') is-invalid @enderror" placeholder="******">
                        @error('new_password') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer py-1">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" wire:click="resetPassword" data-bs-dismiss="modal" class="btn btn-sm btn-primary">تحديث كلمة السر</button>
                </div>
            </div>
        </div>
    </div>
</div>
