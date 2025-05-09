<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSection extends Model
{
    protected $table = 'store_section'; // اسم الجدول في قاعدة البيانات

    protected $fillable = [
        'store_id',
        'section_id',
    ];

    // العلاقات (اختياري حسب الحاجة)
    public function store()
    {
        return $this->belongsTo(Createstore::class, 'store_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}
