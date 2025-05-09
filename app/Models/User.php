<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable ,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password','phone_number', 'otp_code', 'is_verified'
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

     /**
     * Get the identifier that will be stored in the token.
     *
     * @return mixed
     */
    public function userauth()
    {
        return $this->hasOne(Userauth::class, 'phone', 'phone_number'); // تأكد أن العلاقة صحيحة
    }

    public function getJWTIdentifier()
    {
        return $this->id; // المعرف الفريد للمستخدم
    }

    public function getJWTCustomClaims()
    {
        return ['phone_number' => $this->phone_number]; // إضافة الـ phone_number إلى التوكن
    }
}
