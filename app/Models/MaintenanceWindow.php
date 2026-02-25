<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceWindow extends Model
{
    // A screenshot alapján azonosított táblanév
    protected $table = 'sch_maintenance_windows';

    protected $fillable = [
        'title',
        'start_time',
        'end_time',
    ];

    // A dátumokat automatikusan Carbon objektummá alakítjuk
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
}
