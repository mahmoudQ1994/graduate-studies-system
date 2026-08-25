<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // إعادة إرسال كاش الصلاحيات
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. إنشاء الصلاحيات التفصيلية
        $permissions = [
            // إدارة المستخدمين (خاصة بالسوبر أدمن فقط)
            'manage-users',

            // صلاحيات البيانات والأقسام
            'view-department-data',
            'create-department-data',
            'edit-department-data',
            'delete-department-data',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. إنشاء الأدوار وتعيين الصلاحيات

        // سوبر أدمن
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // أدمن قسم
        $deptAdmin = Role::firstOrCreate(['name' => 'Department Admin']);
        $deptAdmin->givePermissionTo([
            'view-department-data',
            'create-department-data',
            'edit-department-data',
            'delete-department-data',
        ]);

        // موظف قسم
        $deptEmployee = Role::firstOrCreate(['name' => 'Department Employee']);
        $deptEmployee->givePermissionTo([
            'view-department-data',
            'create-department-data',
        ]);
    }
}
