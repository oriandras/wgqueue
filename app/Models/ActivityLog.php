<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A rendszertevékenységek naplózásáért felelős modell.
 */
class ActivityLog extends Model
{
    /**
     * A modellhez tartozó tábla neve.
     *
     * @var string
     */
    protected $table = 'sys_activity_logs';

    /**
     * A tömegesen kitölthető mezők listája.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'description',
    ];

    /**
     * A tevékenységhez kapcsolódó felhasználó lekérése.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
