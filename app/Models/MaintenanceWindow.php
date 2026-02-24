<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceWindow extends Model
{
    protected $table = 'sch_maintenance_windows';

    protected $fillable = ['title', 'start_time', 'end_time'];
}
