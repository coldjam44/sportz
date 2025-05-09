<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'userauth_id', // تأكد من استخدام 'userauth_id'
        'total_price',
        'status',
      'createstore_id','cart_id','invoice_id'
    ];

    public function user()
    {
        return $this->belongsTo(userauth::class, 'userauth_id'); // استخدام 'userauth_id' هنا أيضًا
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function createstore()
{
    return $this->belongsTo(createstore::class, 'createstore_id');
}

public function userstore()
{
    return $this->belongsTo(Userstore::class, 'userstore_id');
}
  
 // public function invoice()
//{
  //  return $this->hasOne(invoice::class, 'cart_id', 'cart_id'); 
//}

public function invoice()
{
    return $this->hasOne(invoice::class, 'order_id', 'id');
}



}
