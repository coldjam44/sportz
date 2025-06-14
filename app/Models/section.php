<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class section extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $fillable = ['name_ar', 'name_en','providerauth_id'];

    public function addProducts()
    {
        return $this->hasMany(addproduct::class);
    }
  
   public function stores()
    {
        return $this->belongsToMany(createstore::class, 'store_section', 'section_id', 'store_id');
    }
  
   public function provider()
    {
        return $this->belongsTo(providerauth::class, 'providerauth_id');
    }

public function storeSection()
{
    return $this->hasOne(StoreSection::class, 'section_id');
}

}
