<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * A rendszerhibák naplózásáért felelős modell.
 */
class ErrorLog extends Model
{
    /**
     * A modellhez tartozó tábla neve.
     *
     * @var string
     */
    protected $table = 'sys_errors';

    /**
     * A tömegesen kitölthető mezők listája.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'message',
        'stack_trace',
        'url'
    ];

    /**
     * A hibához kapcsolódó felhasználó lekérése.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
