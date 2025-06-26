<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'message_ar',
        'message_en',
        'notification_status',
        'notifiable_id',
        'notifiable_type',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // علاقة polymorphic مع الـ notifiable models (مثل User, Provider, ..)
    public function notifiable()
    {
        return $this->morphTo();
    }
}
