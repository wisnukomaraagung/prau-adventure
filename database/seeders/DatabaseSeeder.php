<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin default
        User::firstOrCreate(
            ['email' => 'admin@prauadventure.com'],
            [
                'name'     => 'Admin Prau Adventure',
                'password' => bcrypt('admin123'),
                'role'     => 'admin',
                'no_hp'    => '081234567890',
            ]
        );

        // Sample pelanggan
        User::firstOrCreate(
            ['email' => 'pelanggan@example.com'],
            [
                'name'     => 'Budi Santoso',
                'password' => bcrypt('password'),
                'role'     => 'pelanggan',
                'no_hp'    => '082345678901',
            ]
        );

        // Kategori awal
        $kategoriList = [
            'Tenda',
            'Carrier',
            'Sleeping Bag',
            'Kompor & Alat Masak',
            'Matras',
            'Jas Hujan',
            'Headlamp',
            'Trekking Pole',
            'Webbing',
            'Lainnya',
        ];

        foreach ($kategoriList as $nama) {
            Kategori::firstOrCreate(
                ['slug' => Str::slug($nama)],
                ['nama' => $nama]
            );
        }
    }
}
