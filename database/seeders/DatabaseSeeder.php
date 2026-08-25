<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء دور Super Admin
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);

        // 2. إنشاء مستخدم Super Admin
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('admin123'), // تأكد من تغيير كلمة المرور بعد التثبيت
            ]
        );
        $superAdminUser->assignRole($superAdminRole);
        

    }
}

