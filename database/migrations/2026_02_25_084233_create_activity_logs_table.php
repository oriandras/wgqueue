<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('sys_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('sys_users');
            $table->string('action'); // pl: "Létrehozás", "Törlés"
            $table->string('description'); // pl: "Új kiküldés: Hírlevél #1"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
