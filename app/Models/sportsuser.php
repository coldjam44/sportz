<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sportsuser extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $fillable = ['name_ar', 'name_en', 'image'];

  
  public function users()
{
    return $this->belongsToMany(userauth::class, 'user_sports');
}

}
