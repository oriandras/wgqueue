<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventColumnToActivityLogTable extends Migration
{
    public function up()
    {
        // activity_log helyett sys_activity_log
        Schema::table('sys_activity_log', function (Blueprint $table) {
            $table->string('event')->nullable()->after('subject_type');
        });
    }

    public function down()
    {
        Schema::table('sys_activity_log', function (Blueprint $table) {
            $table->dropColumn('event');
        });
    }
}
