<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    // Megadjuk a tábla pontos nevét (mivel sys_ előtagot használtunk)
    protected $table = 'sys_activity_logs';

    // Engedélyezzük a mezők tömeges feltöltését
    protected $fillable = [
        'user_id',
        'action',
        'description',
    ];

    // Kapcsolat a felhasználóval (opcionális, de hasznos)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
