<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'address',
        'notes',
        'total_amount',
      'order_id',
    ];

    // العلاقة مع السلة
    public function cart()
    {
        return $this->belongsTo(cart::class);
    }

    public function orderItems()
{
    return $this->hasMany(OrderItem::class); // تأكد من أن `OrderItem` هو النموذج الصحيح
}

 public function order()
{
    return $this->belongsTo(Order::class);
}

}
