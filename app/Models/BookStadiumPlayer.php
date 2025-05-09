<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookStadiumPlayer extends Model
{
    use HasFactory;
      protected $table = 'bookstadium_players'; // 👈 لو ده اسم الجدول الفعلي

    protected $fillable = ['bookstadium_id', 'userauth_id'];

    public function reservation()
    {
        return $this->belongsTo(bookstadium::class, 'bookstadium_id');
    }

    public function user()
    {
        return $this->belongsTo(userauth::class, 'userauth_id');
    }
}
