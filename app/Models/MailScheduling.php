<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon; // <--- Ez hiányzik a Carbon-hoz
use Illuminate\Support\Facades\DB; // <--- Ez hiányzik a DB facade-hoz

class MailScheduling extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'sch_mail_schedulings';

    protected $fillable = [
        'user_id',
        'start_time',
        'calculated_end_time',
        'mail_count',
        'subject',
        'group_name'
    ];

    /**
     * Activity Log beállítása (US 2)
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['start_time', 'mail_count', 'subject', 'calculated_end_time'])
            ->logOnlyDirty()
            ->useLogName('scheduling');
    }

    /**
     * US 1: Logika a kiküldés végének kiszámításához.
     */
    protected static function booted()
    {
        static::creating(function ($scheduling) {
            // 1. Lekérjük a percenkénti limitet az adatbázisból (vagy alapértelmezett 100)
            $limitPerMinute = (int) (DB::table('sys_settings')
                ->where('key', 'mails_per_minute')
                ->value('value') ?? 100);

            // 2. Kiszámoljuk az időtartamot (mail_count / limit)
            // A ceil() felfelé kerekít, hogy minden megkezdett perc le legyen foglalva
            $durationMinutes = ceil($scheduling->mail_count / $limitPerMinute);

            // 3. Beállítjuk a számított végidőpontot a Carbon segítségével
            $scheduling->calculated_end_time = Carbon::parse($scheduling->start_time)
                ->addMinutes($durationMinutes);
        });
    }

    /**
     * Kapcsolat a felhasználóhoz
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
