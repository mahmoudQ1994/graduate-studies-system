<?php

namespace App\Livewire\UserManagement;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Department;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.app')]
class UserManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    // حقول النموذج الأساسية
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public ?int $department_id = null;
    public string $role = 'Department Employee';
    public bool $is_active = true;
    public ?int $selected_user_id = null;

    // التحكم بالصلاحيات التفصيلية الفردية
    public array $user_permissions = [];

    // التحكم بالواجهة والبحث
    public string $search = '';
    public string $filterRole = '';
    public ?int $filterDepartment = null;
    public bool $isEdit = false;
    public bool $showForm = false;

    // إعادة تعيين كلمة المرور
    public string $new_password = '';

    public function mount(): void
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'غير مصرح لك بالوصول لإدارة المستخدمين.');
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:150',
            'email' => 'required|email|unique:users,email,' . $this->selected_user_id,
            'password' => $this->isEdit ? 'nullable|min:6' : 'required|min:6',
            'department_id' => 'required_if:role,Department Admin,Department Employee|nullable|exists:departments,id',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
            'user_permissions' => 'nullable|array',
        ];
    }

    protected array $messages = [
        'name.required' => 'اسم المستخدم مطلوب',
        'email.required' => 'البريد الإلكتروني مطلوب',
        'email.unique' => 'هذا البريد الإلكتروني مُسجل بالفعل',
        'password.required' => 'كلمة السر مطلوبة عند إنشاء حساب جديد',
        'department_id.required_if' => 'يرجى تحديد القسم التابع له المستخدم',
        'role.required' => 'يرجى تحديد دور المستخدم بالنظام',
    ];

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetFields();
        }
    }

    public function resetFields(): void
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->department_id = null;
        $this->role = 'Department Employee';
        $this->is_active = true;
        $this->selected_user_id = null;
        $this->user_permissions = [];
        $this->isEdit = false;
        $this->showForm = false;
        $this->new_password = '';
        $this->resetValidation();
    }

    /**
     * حفظ مستخدم جديد وتعيين دوره وصلاحياته الفردية
     */
    public function store(): void
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'department_id' => $this->role === 'Super Admin' ? null : $this->department_id,
            'is_active' => $this->is_active,
        ]);

        // تعيين الدور
        $user->assignRole($this->role);

        // تعيين الصلاحيات المباشرة (إن وجدت)
        if (!empty($this->user_permissions)) {
            $user->givePermissionTo($this->user_permissions);
        }

        session()->flash('message', 'تم إضافة المستخدم وتحديد صلاحياته بنجاح');
        $this->resetFields();
    }

    /**
     * جلب بيانات مستخدم وتعبئة الصلاحيات الخاصة به
     */
    public function edit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->selected_user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->department_id = $user->department_id;
        $this->is_active = (bool) $user->is_active;
        $this->role = $user->roles->first()?->name ?? 'Department Employee';

        // جلب الصلاحيات المباشرة المسندة للمستخدم فقط دون صلاحيات الدور
        $this->user_permissions = $user->getDirectPermissions()->pluck('name')->toArray();

        $this->isEdit = true;
        $this->showForm = true;
    }

    /**
     * تحديث بيانات المستخدم وتعديل صلاحياته الاستثنائية
     */
    public function update(): void
    {
        $this->validate();

        $user = User::findOrFail($this->selected_user_id);
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'department_id' => $this->role === 'Super Admin' ? null : $this->department_id,
            'is_active' => $this->is_active,
        ]);

        // مزامنة الدور
        $user->syncRoles([$this->role]);

        // مزامنة الصلاحيات الاستثنائية (إضافة الجديد وسحب الملحغى)
        $user->syncPermissions($this->user_permissions);

        session()->flash('message', 'تم تحديث بيانات المستخدم وصلاحياته بنجاح');
        $this->resetFields();
    }

    public function resetPassword(): void
    {
        $this->validate([
            'new_password' => 'required|min:6'
        ], [
            'new_password.required' => 'يرجى إدخال كلمة السر الجديدة',
            'new_password.min' => 'يجب ألا تقل كلمة السر عن 6 أحرف'
        ]);

        $user = User::findOrFail($this->selected_user_id);
        $user->update([
            'password' => Hash::make($this->new_password)
        ]);

        session()->flash('message', 'تم إعادة تعيين كلمة السر للمستخدم بنجاح');
        $this->resetFields();
    }

    public function delete(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'لا يمكنك حذف حسابك الحالي');
            return;
        }

        User::findOrFail($id)->delete();
        session()->flash('message', 'تم حذف حساب المستخدم بنجاح');
    }

    public function toggleStatus(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return;
        }
        $user->update(['is_active' => !$user->is_active]);
    }


    public function render()
    {
        $users = User::with(['department', 'roles', 'permissions'])
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%')->orWhere('email', 'like', '%' . $this->search . '%'))
            ->when($this->filterDepartment, fn($q) => $q->where('department_id', $this->filterDepartment))
            ->when($this->filterRole, fn($q) => $q->role($this->filterRole))
            ->latest()
            ->paginate(8);

        return view('livewire.user-management.user-manager', [
            'users' => $users,
            'departments' => Department::where('is_active', true)->get(),
            'roles' => Role::all(),
            'allPermissions' => Permission::all(),
        ]);
    }
}
