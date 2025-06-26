<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CanceledReservationTable extends Model
{
    protected $table = 'book_stadium_canceled_reservations';

    protected $fillable = [
        'userauth_id',
        'createstadium_id',
        'start_time',
        'end_time',
        'booking_type',
        'players_count',
        'remaining_players',
        'teams_count',
        'min_players_per_team',
        'total_price',
        'remaining_teams',
        'status',
        'cancellation_reason',
        'date',
        'player_price',
    ];

    public function stadium()
    {
        return $this->belongsTo(CreateStadium::class, 'createstadium_id');
    }
}
