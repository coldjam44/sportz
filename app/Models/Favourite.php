<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use HasFactory;
   protected $fillable = ['userauth_id', 'product_id'];

    public function userauth()
    {
        return $this->belongsTo(Userauth::class);
    }

    public function product()
    {
        return $this->belongsTo(addproduct::class, 'product_id');
    }
}
