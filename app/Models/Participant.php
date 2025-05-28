<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $table = 'bookstadium_players';

    protected $fillable = [
        'bookstadium_id',  // بدل booking_id
        'userauth_id',
        'players_count',
    ];

    public function booking()
    {
        return $this->belongsTo(BookStadium::class, 'bookstadium_id');
    }

    public function user()
    {
        return $this->belongsTo(UserAuth::class, 'userauth_id');
    }
}
