<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class addproduct extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $fillable = [
        'name_ar',
      'providerauth_id',
        'name_en',
        'image',
        'description_en',
        'description_ar',
        'price',
            'store_id',

        'section_id',
        'discount',
        'start_time',  // إضافة start_time
        'end_time'     // إضافة end_time
    ];

    public function section()
    {
        return $this->belongsTo(section::class);
    }

    public function carts(){

        return $this->belongsToMany(Cart::class);
        }
    // في نموذج addproduct
// public function carts()
// {
//     return $this->hasMany(Cart::class, 'product_id');
// }

public function store()
{
    return $this->belongsTo(createstore::class, 'store_id');
}
  public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }
  
  public function provider()
    {
        return $this->belongsTo(providerauth::class, 'providerauth_id');
    }

}
