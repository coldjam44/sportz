<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class createstore extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $fillable=['name','image','section_id','tax_record','location','store_type_id','providerauth_id'];

    public function section()
    {
        return $this->belongsToMany(section::class, 'store_section', 'store_id', 'section_id');
    }

    public function orders()
{
    return $this->hasMany(Order::class, 'createstore_id');
}

public function products()
    {
        return $this->hasMany(addproduct::class, 'store_id');
    }
  
   public function rates()
    {
        return $this->hasMany(providerrate::class,'store_id');
    }
  
  public function storetype()
{
    return $this->belongsTo(storetype::class, 'store_type_id');
}
  
   public function provider()
    {
        return $this->belongsTo(providerauth::class, 'providerauth_id');
    }

}
