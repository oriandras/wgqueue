<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_sys_settings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sys_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // pl. mails_per_minute [cite: 20]
            $table->string('value');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('sys_settings');
    }
};
