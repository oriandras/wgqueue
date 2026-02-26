<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A karbantartási időszakokat kezelő modell.
 */
class MaintenanceWindow extends Model
{
    /**
     * A modellhez tartozó tábla neve.
     *
     * @var string
     */
    protected $table = 'sch_maintenance_windows';

    /**
     * A tömegesen kitölthető mezők listája.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'start_time',
        'end_time',
    ];

    /**
     * Az attribútumok típusátalakítása.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
}
