<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class createstadium extends Model
{
    use HasFactory;
    protected $table='createstadium';
    protected $fillable=['name','image','sportsuser_id','tax_record','location',
    'morning_start_time','morning_end_time','evening_start_time',
    'evening_end_time','booking_price',
    'evening_extra_price_per_hour','team_members_count','providerauth_id'];
    public function sportsuser()
    {
        return $this->belongsTo(sportsuser::class);
    }

    public function rates()
    {
        return $this->hasMany(providerrate::class,'stadium_id');
    }

    public function avilableservice()
    {
        return $this->belongsToMany(avilableservice::class, 'avilableservice_createstadium');
    }
  
  public function provider()
    {
        return $this->belongsTo(providerauth::class, 'providerauth_id');
    }
}
