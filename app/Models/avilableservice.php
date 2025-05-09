<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class avilableservice extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $fillable = ['name_ar', 'name_en', 'image'];

    public function createstadium ()
{
    return $this->belongsToMany(createstadium::class, 'avilableservice_createstadium');
}

}
