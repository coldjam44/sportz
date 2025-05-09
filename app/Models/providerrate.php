<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class providerrate extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $fillable = ['name', 'rate', 'description', 'stadium_id','store_id'];

    public function stadium()
    {
        return $this->belongsTo(createstadium::class, 'stadium_id');
    }
  
  public function store()
    {
        return $this->belongsTo(createstore::class, 'store_id');
    }

}
