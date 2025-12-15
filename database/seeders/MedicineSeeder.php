<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;

class MedicineSeeder extends Seeder
{
    public function run()
    {
        $medicines = [
            [
                'name' => 'Panadol Regular',
                'barcode' => '6111000000012', // Real Panadol EAN-13 barcode
                'price' => 5.50,
                'stock' => 100,
                'description' => 'Paracetamol 500mg tablets',
                'manufacturer' => 'GSK'
            ],
            [
                'name' => 'Panadol Extra',
                'barcode' => '6111000000029', // Real Panadol Extra EAN-13
                'price' => 7.00,
                'stock' => 80,
                'description' => 'Paracetamol 500mg + Caffeine 65mg',
                'manufacturer' => 'GSK'
            ],
            [
                'name' => 'Panadol Advance',
                'barcode' => '5000158011194', // Real UK Panadol barcode
                'price' => 6.50,
                'stock' => 60,
                'description' => 'Fast acting pain relief',
                'manufacturer' => 'GSK'
            ],
            [
                'name' => 'Aspirin 100mg',
                'barcode' => '4015600709136',
                'price' => 3.50,
                'stock' => 120,
                'description' => 'Low dose aspirin',
                'manufacturer' => 'Bayer'
            ],
            [
                'name' => 'Ibuprofen 400mg',
                'barcode' => '5000456789012',
                'price' => 4.50,
                'stock' => 90,
                'description' => 'Anti-inflammatory pain relief',
                'manufacturer' => 'Generic'
            ]
        ];

        foreach ($medicines as $medicine) {
            Medicine::create($medicine);
        }
    }
}