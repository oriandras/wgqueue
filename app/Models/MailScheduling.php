<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Az e-mailek ütemezéséért felelős modell.
 */
class MailScheduling extends Model
{
    use SoftDeletes, LogsActivity;

    /**
     * A modellhez tartozó tábla neve.
     *
     * @var string
     */
    protected $table = 'sch_mail_schedulings';

    /**
     * A tömegesen kitölthető mezők listája.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'start_time',
        'calculated_end_time',
        'mail_count',
        'subject',
        'group_name'
    ];

    /**
     * Az Activity Log beállításai.
     *
     * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['start_time', 'mail_count', 'subject', 'calculated_end_time'])
            ->logOnlyDirty()
            ->useLogName('scheduling');
    }

    /**
     * A modell eseményeinek regisztrálása.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($scheduling) {
            // TODO: A percenkénti limitet érdemes lenne cache-elni vagy konstansba tenni a sűrű lekérdezések elkerülése érdekében.
            // A percenkénti limit lekérése a beállításokból (alapértelmezett: 100)
            $limitPerMinute = (int) (DB::table('sys_settings')
                ->where('key', 'mails_per_minute')
                ->value('value') ?? 100);

            // Az időtartam kiszámítása (levélszám / limit)
            // A ceil() felfelé kerekít, hogy minden megkezdett perc le legyen foglalva
            $durationMinutes = ceil($scheduling->mail_count / $limitPerMinute);

            // A számított végidőpont beállítása
            $scheduling->calculated_end_time = Carbon::parse($scheduling->start_time)
                ->addMinutes($durationMinutes);
        });
    }

    /**
     * Az ütemezéshez kapcsolódó felhasználó lekérése.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
