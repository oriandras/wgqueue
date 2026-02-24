<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBatchUuidColumnToActivityLogTable extends Migration
{
    public function up()
    {
        // Itt is írd át sys_activity_log-ra
        Schema::table('sys_activity_log', function (Blueprint $table) {
            $table->uuid('batch_uuid')->nullable()->after('properties');
        });
    }

    public function down()
    {
        Schema::table('sys_activity_log', function (Blueprint $table) {
            $table->dropColumn('batch_uuid');
        });
    }
}
