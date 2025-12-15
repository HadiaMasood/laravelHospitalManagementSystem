<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Medicine;
use App\Models\Stock;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user (password will be hashed by Laravel automatically)
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@medicalstore.com',
            'password' => 'password123', // Plain password
            'role' => 'admin',
        ]);

        // Create cashier user
        User::create([
            'name' => 'Cashier',
            'email' => 'cashier@medicalstore.com',
            'password' => 'password123', // Plain password
            'role' => 'cashier',
        ]);

        // Create suppliers
        $suppliers = [
            [
                'name' => 'PharmaCorp Inc.',
                'contact_person' => 'John Smith',
                'phone' => '+1234567890',
                'email' => 'contact@pharmacorp.com',
                'address' => '123 Medical Ave, City, Country',
            ],
            [
                'name' => 'MediSupply Ltd.',
                'contact_person' => 'Jane Doe',
                'phone' => '+9876543210',
                'email' => 'sales@medisupply.com',
                'address' => '456 Healthcare Blvd, Town, Country',
            ],
            [
                'name' => 'HealthPlus Distributors',
                'contact_person' => 'Mike Johnson',
                'phone' => '+1122334455',
                'email' => 'info@healthplus.com',
                'address' => '789 Wellness St, Village, Country',
            ],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }

        // Create medicines
        $medicines = [
            [
                'name' => 'Paracetamol 500mg',
                'generic_name' => 'Acetaminophen',
                'category' => 'Pain Relief',
                'description' => 'For fever and mild to moderate pain',
                'unit_price' => 2.50,
                'barcode' => 'MED001',
                'reorder_level' => 50,
                'supplier_id' => 1,
            ],
            [
                'name' => 'Amoxicillin 250mg',
                'generic_name' => 'Amoxicillin',
                'category' => 'Antibiotic',
                'description' => 'Broad-spectrum antibiotic',
                'unit_price' => 5.00,
                'barcode' => 'MED002',
                'reorder_level' => 30,
                'supplier_id' => 1,
            ],
            [
                'name' => 'Ibuprofen 400mg',
                'generic_name' => 'Ibuprofen',
                'category' => 'Anti-inflammatory',
                'description' => 'NSAID for pain and inflammation',
                'unit_price' => 3.75,
                'barcode' => 'MED003',
                'reorder_level' => 40,
                'supplier_id' => 2,
            ],
            [
                'name' => 'Omeprazole 20mg',
                'generic_name' => 'Omeprazole',
                'category' => 'Gastric',
                'description' => 'Proton pump inhibitor for acid reflux',
                'unit_price' => 4.50,
                'barcode' => 'MED004',
                'reorder_level' => 25,
                'supplier_id' => 2,
            ],
            [
                'name' => 'Cetirizine 10mg',
                'generic_name' => 'Cetirizine',
                'category' => 'Antihistamine',
                'description' => 'For allergic conditions',
                'unit_price' => 2.00,
                'barcode' => 'MED005',
                'reorder_level' => 60,
                'supplier_id' => 3,
            ],
        ];

        foreach ($medicines as $medicineData) {
            Medicine::create($medicineData);
        }

        // Create stock entries
        $stocks = [
            [
                'medicine_id' => 1,
                'batch_number' => 'BATCH001',
                'quantity' => 100,
                'purchase_price' => 2.00,
                'selling_price' => 2.50,
                'expiry_date' => now()->addMonths(18),
                'supplier_id' => 1,
                'purchase_date' => now()->subDays(30),
            ],
            [
                'medicine_id' => 2,
                'batch_number' => 'BATCH002',
                'quantity' => 75,
                'purchase_price' => 4.00,
                'selling_price' => 5.00,
                'expiry_date' => now()->addMonths(24),
                'supplier_id' => 1,
                'purchase_date' => now()->subDays(25),
            ],
            [
                'medicine_id' => 3,
                'batch_number' => 'BATCH003',
                'quantity' => 80,
                'purchase_price' => 3.00,
                'selling_price' => 3.75,
                'expiry_date' => now()->addMonths(20),
                'supplier_id' => 2,
                'purchase_date' => now()->subDays(20),
            ],
            [
                'medicine_id' => 4,
                'batch_number' => 'BATCH004',
                'quantity' => 50,
                'purchase_price' => 3.50,
                'selling_price' => 4.50,
                'expiry_date' => now()->addDays(45), // Expiring soon
                'supplier_id' => 2,
                'purchase_date' => now()->subDays(15),
            ],
            [
                'medicine_id' => 5,
                'batch_number' => 'BATCH005',
                'quantity' => 120,
                'purchase_price' => 1.50,
                'selling_price' => 2.00,
                'expiry_date' => now()->addMonths(12),
                'supplier_id' => 3,
                'purchase_date' => now()->subDays(10),
            ],
        ];

        foreach ($stocks as $stockData) {
            Stock::create($stockData);
        }
    }
}