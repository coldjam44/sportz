<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class userauth extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name', 'last_name', 'phone', 'email', 'gender', 'province', 'city', 'area',
    ];

    public function carts()
    {
        return $this->hasMany(Cart::class, 'userauth_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'phone', 'phone_number');
    }
  
  public function favoriteSports()
{
    return $this->belongsToMany(sportsuser::class, 'user_sports');
}
  
  public function teamBookings()
{
    return $this->belongsToMany(BookStadium::class, 'bookstadium_players')
        ->withPivot('players_count')
        ->withTimestamps();
}



}
