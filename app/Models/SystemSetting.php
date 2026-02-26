<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A rendszerbeállításokat kezelő modell.
 */
class SystemSetting extends Model
{
    /**
     * A modellhez tartozó tábla neve.
     *
     * @var string
     */
    protected $table = 'sys_settings';

    /**
     * A tömegesen kitölthető mezők listája.
     *
     * @var array<int, string>
     */
    protected $fillable = ['key', 'value'];

    /**
     * Egy beállítás értékének lekérése kulcs alapján.
     *
     * @param string $key A beállítás kulcsa
     * @param mixed $default Alapértelmezett érték, ha a kulcs nem található
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Egy beállítás értékének mentése vagy frissítése.
     *
     * @param string $key A beállítás kulcsa
     * @param mixed $value A mentendő érték
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function set($key, $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
