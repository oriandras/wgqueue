<?php

/**
 * A Spatie Activity Log csomag konfigurációja.
 * Meghatározza, hogyan történjen a rendszertevékenységek naplózása.
 */
return [

    /**
     * Naplózás engedélyezése. Ha hamis, nem mentődnek aktivitások.
     */
    'enabled' => env('ACTIVITY_LOGGER_ENABLED', true),

    /**
     * Régi bejegyzések törlése ennyi nap után (clean-command futtatásakor).
     */
    'delete_records_older_than_days' => 365,

    /**
     * Alapértelmezett napló név, ha nincs megadva az activity() helpernél.
     */
    'default_log_name' => 'default',

    /**
     * Hitelesítési driver a felhasználói modellek lekéréséhez.
     * Ha null, az alapértelmezett Laravel auth drivert használja.
     */
    'default_auth_driver' => null,

    /**
     * Ha igaz, a törölt (soft deleted) modellek is visszatérnek alanyként.
     */
    'subject_returns_soft_deleted_models' => false,

    /**
     * Az aktivitások naplózásához használt modell.
     * Implementálnia kell a Spatie\Activitylog\Contracts\Activity interfészt.
     */
    'activity_model' => \Spatie\Activitylog\Models\Activity::class,

    /**
     * Az adatbázis tábla neve, ahol a naplóbejegyzések tárolódnak.
     */
    'table_name' => env('ACTIVITY_LOGGER_TABLE_NAME', 'sys_activity_log'),

    /**
     * Az adatbázis kapcsolat neve a naplózáshoz.
     * Ha nincs megadva, az alapértelmezett kapcsolatot használja.
     */
    'database_connection' => env('ACTIVITY_LOGGER_DB_CONNECTION'),

];
