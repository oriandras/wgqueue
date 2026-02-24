<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            // A specifikáció szerinti kiegészítések [cite: 19]
            $table->boolean('is_admin')->default(false)->after('password');
            $table->boolean('is_active')->default(true)->after('is_admin');
        });

        // A tábla átnevezése a moduláris prefixre [cite: 17]
        Schema::rename('users', 'sys_users');
    }

    public function down(): void {
        Schema::rename('sys_users', 'users');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'is_active']);
        });
    }
};
