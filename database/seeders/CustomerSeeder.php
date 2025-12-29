<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'nama_customer' => 'PT Sinar Terang',
                'no_hp' => '081234567890',
                'email' => 'sinar.terang@example.com',
                'alamat' => 'Jl. Merdeka No. 10, Jakarta',
                'point' => 0,
            ],
            [
                'nama_customer' => 'CV Maju Jaya',
                'no_hp' => '081298765432',
                'email' => 'maju.jaya@example.com',
                'alamat' => 'Jl. Sudirman No. 25, Bandung',
                'point' => 0,
            ],
            [
                'nama_customer' => 'UD Sejahtera',
                'no_hp' => '082112223333',
                'email' => 'ud.sejahtera@example.com',
                'alamat' => 'Jl. Diponegoro No. 5, Surabaya',
                'point' => 0,
            ],
            [
                'nama_customer' => 'Toko Berkah',
                'no_hp' => '083344556677',
                'email' => 'toko.berkah@example.com',
                'alamat' => 'Jl. Malioboro No. 3, Yogyakarta',
                'point' => 0,
            ],
            [
                'nama_customer' => 'Bumi Nusantara',
                'no_hp' => '084455667788',
                'email' => 'bumi.nusantara@example.com',
                'alamat' => 'Jl. Gatot Subroto No. 12, Medan',
                'point' => 0,
            ],
        ];

        foreach ($customers as $data) {
            Customer::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
