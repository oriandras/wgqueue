<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    // Megmondjuk a modellnek, hogy a sys_ prefixes táblát használja
    protected $table = 'sys_user_settings';

    protected $fillable = ['user_id', 'datatable_per_page', 'calendar_default_view'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
