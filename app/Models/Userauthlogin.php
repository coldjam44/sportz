<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // علشان يستخدم JWTAuth بشكل صحيح
use Tymon\JWTAuth\Contracts\JWTSubject;

class Userauthlogin extends Authenticatable implements JWTSubject
{
    // تفعيل الفاكتوريز لو حابب
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    // اسم الجدول إذا مختلف
    protected $table = 'userauths';

    // إذا في أعمدة يمكن تعبئتها بالجماعة (fillable)
    protected $fillable = [
        'phone',
        'prv_otp_code',
        // أي أعمدة ثانية
    ];

    // دوال JWT لازم تتطبق

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
