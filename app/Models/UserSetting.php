<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A felhasználói beállításokat kezelő modell.
 */
class UserSetting extends Model
{
    /**
     * A modellhez tartozó tábla neve.
     *
     * @var string
     */
    protected $table = 'sys_user_settings';

    /**
     * A tömegesen kitölthető mezők listája.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'datatable_per_page', 'calendar_default_view'];

    /**
     * A beállításhoz tartozó felhasználó lekérése.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
