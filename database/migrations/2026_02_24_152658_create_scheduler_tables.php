<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Levélkiküldések ütemezése (US 1, US 6)
        Schema::create('sch_mail_schedulings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('sys_users'); // Ki foglalta le?
            $table->datetime('start_time'); // Mikor indul?
            $table->datetime('calculated_end_time'); // Meddig tart a kapacitás szerint?
            $table->integer('mail_count'); // Hány levél?
            $table->string('subject'); // Levél tárgya
            $table->string('group_name'); // Kiknek megy?
            $table->softDeletes(); // US 6: Csak jelöljük töröltnek, hogy látszódjon a felszabadult hely
            $table->timestamps();
        });

        // Karbantartási időszakok (US 4)
        Schema::create('sch_maintenance_windows', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Miért van lezárva?
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('sch_maintenance_windows');
        Schema::dropIfExists('sch_mail_schedulings');
    }
};
