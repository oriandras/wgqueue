<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sys_user_settings', function (Blueprint $table) {
            $table->id();
            // Kapcsolat a sys_users táblával
            $table->foreignId('user_id')->unique()->constrained('sys_users')->onDelete('cascade');
            // Ide jöhetnek a perszonalizációs oszlopok
            $table->string('datatable_per_page', 10)->default('25');
            $table->string('calendar_default_view', 50)->default('timeGridWeek');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_user_settings');
    }
};
