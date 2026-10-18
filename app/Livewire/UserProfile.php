<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserProfile extends Component
{
    public $name;
    public $email;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function mount()
    {
        // تعبئة البيانات الخاصة بالمستخدم الحالي
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function updatePassword()
    {
        // التحقق من صحة القواعد لكلمة السر
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => 'يرجى إدخال كلمة المرور الحالية.',
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة.',
            'new_password.required' => 'يرجى إدخال كلمة المرور الجديدة.',
            'new_password.confirmed' => 'تأكيد كلمة المرور الجديدة غير متطابق.',
        ]);

        // تحديث كلمة السر في قاعدة البيانات
        auth()->user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        // إعادة تفريغ حقول كلمة المرور وإظهار رسالة نجاح
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('message', 'تم تغيير كلمة المرور بنجاح!');
    }

    public function render()
    {
        return view('livewire.user-profile')->layout('layouts.app');
    }
}
