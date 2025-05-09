<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class userstore extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $fillable = ['name_ar', 'name_en', 'image', 'section_id', 'rate'];

    public function section()
    {
        return $this->belongsTo(section::class);
    }

    public function orders()
{
    return $this->hasMany(Order::class, 'userstore_id');
}


}
