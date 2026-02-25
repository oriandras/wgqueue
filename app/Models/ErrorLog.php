<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model {
    protected $table = 'sys_errors';
    protected $fillable = ['user_id', 'message', 'stack_trace', 'url'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
