<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('Tempus123%');

        // 1. Speciális fejlesztői és admin felhasználók
        $users = [
            ['name' => 'Fejlesztő', 'email' => 'dev@tpf.hu'],
            ['name' => 'Adminisztrátor', 'email' => 'admin@wgqueue.test'],
        ];

        // 2. Kollégák hozzáadása a beküldött lista alapján
        $colleagues = [
            ['name' => 'Antal Nóra', 'email' => 'nora.antal@tpf.hu'],
            ['name' => 'Bajzát Attila Péter', 'email' => 'attila.bajzat@tpf.hu'],
            ['name' => 'Gaál Boglárka', 'email' => 'boglarka.gaal@tpf.hu'],
            ['name' => 'Gregor Máté', 'email' => 'mate.gregor@tpf.hu'],
            ['name' => 'Kark Tamás József', 'email' => 'tamas.kark@tpf.hu'],
            ['name' => 'Őri András', 'email' => 'andras.ori@tpf.hu'],
            ['name' => 'Rogán Balázs', 'email' => 'balazs.rogan@tpf.hu'],
            ['name' => 'Szénai Zsolt', 'email' => 'zsolt.szenai@tpf.hu'],
            ['name' => 'Kovács Norbert Péter', 'email' => 'norbert.kovacs@tpf.hu'],
            ['name' => 'Gubó Luca', 'email' => 'luca.gubo@tpf.hu'],
        ];

        // Felhasználók létrehozása
        foreach (array_merge($users, $colleagues) as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $defaultPassword,
                    'is_admin' => true, // Mindenki admin rangot kap
                    'is_active' => true,
                ]
            );
        }

        // 3. Rendszerbeállítások (UserStory 4)
        $settings = [
            ['key' => 'mails_per_minute', 'value' => '100', 'description' => 'Percenként kiküldhető levelek száma.'],
            ['key' => 'office_hours_start', 'value' => '08:00', 'description' => 'Irodai időszak kezdete.'],
            ['key' => 'office_hours_end', 'value' => '17:00', 'description' => 'Irodai időszak vége.'],
        ];

        foreach ($settings as $setting) {
            DB::table('sys_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
