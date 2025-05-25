<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bookstadium extends Model
{
    use HasFactory;
    //protected $table = 'bookstadia'; // في موديل BookStadium

    protected $fillable = [
        'createstadium_id', 'userauth_id', 'start_time', 'end_time', 'booking_type','date','player_price',
        'players_count', 'teams_count', 'min_players_per_team', 'total_price'  ,  'remaining_players', 'remaining_teams', // أضفه لضمان حفظ القيم الصحيحة

    ];

    public function stadium()
    {
        return $this->belongsTo(CreateStadium::class, 'createstadium_id');
    }

    public function user()
    {
        return $this->belongsTo(userauth::class, 'userauth_id'); // ✅ تأكد من أن العلاقة صحيحة
    }

    public function players()
{
    return $this->hasMany(UserAuth::class, 'userauth_id');
}
  
//public function participants()
//{
   // return $this->hasMany(BookStadiumPlayer::class, 'bookstadium_id');
//}
  
  public function participants()
{
    return $this->belongsToMany(UserAuth::class, 'bookstadium_players')
        ->withPivot('players_count')
        ->withTimestamps();
}



  
  public function sport()
{
    return $this->hasOneThrough(
        Sportsuser::class,
        CreateStadium::class,
        'id',              // المفتاح الأساسي في جدول createstadium
        'id',              // المفتاح الأساسي في جدول sportsuser
        'createstadium_id',// المفتاح الأجنبي في bookstadium
        'sportsuser_id'    // المفتاح الأجنبي في createstadium
    );
}


}
