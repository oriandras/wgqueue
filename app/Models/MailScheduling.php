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
        static::saving(function ($scheduling) {
            // Ha manuálisan már beállítottuk a végidőpontot a formban,
            // és az nagyobb, mint amit a perces limit adna, akkor ne bántsuk.
            // Vagy egyszerűen tegyük ide is bele a teljes logikát:

            $settings = DB::table('sys_settings')->pluck('value', 'key');

            $limitPerMinute = (int)($settings['mails_per_minute'] ?? 100);
            $limitPerHour = (int)($settings['hourly_limit'] ?? 1000);

            // Kiszámoljuk mindkét limit alapján
            $minByMinute = $scheduling->mail_count / $limitPerMinute;
            $minByHour = ($scheduling->mail_count / $limitPerHour) * 60;

            // A szigorúbbat alkalmazzuk
            $durationMinutes = ceil(max($minByMinute, $minByHour));

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
