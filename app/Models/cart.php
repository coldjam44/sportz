<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cart extends Model
{
    use HasFactory;
    protected $fillable = ['userauth_id', 'product_id', 'quantity'];

    public function userauth()
    {
        return $this->belongsTo(userauth::class, 'userauth_id');
    }

    public function product()
    {
        return $this->belongsTo(addproduct::class, 'product_id');
    }

   


}
