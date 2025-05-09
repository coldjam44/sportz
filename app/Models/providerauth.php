<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class providerauth extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name', 'last_name', 'email','phone_number'
    ];
  
  public function stadiums()
    {
        return $this->hasMany(CreateStadium::class, 'providerauth_id');
    }
  
   public function stores()
    {
        return $this->hasMany(Createstore::class, 'providerauth_id');
    }
  
   public function sections()
    {
        return $this->hasMany(section::class, 'providerauth_id');
    }
  
   public function addproduct()
    {
        return $this->hasMany(addproduct::class, 'providerauth_id');
    }
}
