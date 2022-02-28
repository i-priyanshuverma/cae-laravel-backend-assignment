<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class   Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'roster_id',
        'date',
        'day',
        'rev',
        'dc',
        'c/i(l)',
        'c/i(z)',
        'c/o(l)',
        'c/o(z)',
        'activity',
        'remark',
        'from',
        'std(z)',
        'std(l)',
        'to',
        'sta(z)',
        'sta(l)',
        'ac/hotel',
        'blh',
        'flight_time',
        'night_time',
        'dur',
        'ext',
        'pax_booked',
        'acreg',
        'crewmeal',
        'resources',
        'cc',
        'name',
        'pos',
        'work_phone',
        'dh_crew',
        'dh_name',
        'dh_seat',
        'remarks',
        'fdp_time',
        'max_fdp',
        'rest_compl',
    ];
}
