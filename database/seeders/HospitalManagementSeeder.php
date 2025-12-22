<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Medicine;
use App\Models\Stock;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class HospitalManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@hospital.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $cashier = User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@hospital.com',
            'password' => Hash::make('password123'),
            'role' => 'cashier',
        ]);

        // Create Suppliers
        $suppliers = [
            [
                'name' => 'ABC Pharmaceuticals',
                'contact_person' => 'John Smith',
                'phone' => '+1234567890',
                'email' => 'contact@abcpharma.com',
                'address' => '123 Medical Street, Health City',
                'license_number' => 'LIC123456',
            ],
            [
                'name' => 'MediCorp Ltd',
                'contact_person' => 'Sarah Johnson',
                'phone' => '+1234567891',
                'email' => 'info@medicorp.com',
                'address' => '456 Pharma Avenue, Medicine Town',
                'license_number' => 'LIC789012',
            ],
            [
                'name' => 'HealthSupply Co',
                'contact_person' => 'Mike Wilson',
                'phone' => '+1234567892',
                'email' => 'sales@healthsupply.com',
                'address' => '789 Supply Road, Wellness City',
                'license_number' => 'LIC345678',
            ],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }

        // Create Medicines with realistic barcodes
        $medicines = [
            [
                'name' => 'Paracetamol 500mg',
                'generic_name' => 'Acetaminophen',
                'category' => 'Tablet',
                'description' => 'Pain reliever and fever reducer',
                'unit_price' => 2.50,
                'barcode' => '8901030895067', // Real format EAN-13 barcode
                'reorder_level' => 50,
                'supplier_id' => 1,
            ],
            [
                'name' => 'Amoxicillin 250mg',
                'generic_name' => 'Amoxicillin',
                'category' => 'Capsule',
                'description' => 'Antibiotic for bacterial infections',
                'unit_price' => 5.00,
                'barcode' => '8901030895074', // Real format EAN-13 barcode
                'reorder_level' => 30,
                'supplier_id' => 1,
            ],
            [
                'name' => 'Ibuprofen 400mg',
                'generic_name' => 'Ibuprofen',
                'category' => 'Tablet',
                'description' => 'Anti-inflammatory and pain reliever',
                'unit_price' => 3.75,
                'barcode' => '8901030895081', // Real format EAN-13 barcode
                'reorder_level' => 40,
                'supplier_id' => 2,
            ],
            [
                'name' => 'Cough Syrup 100ml',
                'generic_name' => 'Dextromethorphan',
                'category' => 'Syrup',
                'description' => 'Cough suppressant',
                'unit_price' => 8.50,
                'barcode' => '8901030895098', // Real format EAN-13 barcode
                'reorder_level' => 25,
                'supplier_id' => 2,
            ],
            [
                'name' => 'Vitamin C 1000mg',
                'generic_name' => 'Ascorbic Acid',
                'category' => 'Tablet',
                'description' => 'Vitamin C supplement',
                'unit_price' => 12.00,
                'barcode' => '8901030895104', // Real format EAN-13 barcode
                'reorder_level' => 20,
                'supplier_id' => 3,
            ],
            [
                'name' => 'Aspirin 75mg',
                'generic_name' => 'Acetylsalicylic Acid',
                'category' => 'Tablet',
                'description' => 'Blood thinner and pain reliever',
                'unit_price' => 1.50,
                'barcode' => '0123456789012', // UPC-A format
                'reorder_level' => 100,
                'supplier_id' => 1,
            ],
            [
                'name' => 'Cetirizine 10mg',
                'generic_name' => 'Cetirizine Hydrochloride',
                'category' => 'Tablet',
                'description' => 'Antihistamine for allergies',
                'unit_price' => 3.25,
                'barcode' => '0987654321098', // UPC-A format
                'reorder_level' => 60,
                'supplier_id' => 3,
            ],
        ];

        foreach ($medicines as $medicineData) {
            Medicine::create($medicineData);
        }

        // Create Stock Entries
        $stocks = [
            // Current stock
            [
                'medicine_id' => 1,
                'supplier_id' => 1,
                'batch_number' => 'BATCH001',
                'quantity' => 100,
                'purchase_price' => 2.00,
                'selling_price' => 2.50,
                'expiry_date' => Carbon::now()->addMonths(18),
                'manufacture_date' => Carbon::now()->subMonths(6),
            ],
            [
                'medicine_id' => 2,
                'supplier_id' => 1,
                'batch_number' => 'BATCH002',
                'quantity' => 75,
                'purchase_price' => 4.00,
                'selling_price' => 5.00,
                'expiry_date' => Carbon::now()->addMonths(12),
                'manufacture_date' => Carbon::now()->subMonths(3),
            ],
            [
                'medicine_id' => 3,
                'supplier_id' => 2,
                'batch_number' => 'BATCH003',
                'quantity' => 60,
                'purchase_price' => 3.00,
                'selling_price' => 3.75,
                'expiry_date' => Carbon::now()->addMonths(24),
                'manufacture_date' => Carbon::now()->subMonths(2),
            ],
            // Expiring soon (for testing alerts)
            [
                'medicine_id' => 4,
                'supplier_id' => 2,
                'batch_number' => 'BATCH004',
                'quantity' => 15,
                'purchase_price' => 7.00,
                'selling_price' => 8.50,
                'expiry_date' => Carbon::now()->addDays(15), // Expiring in 15 days
                'manufacture_date' => Carbon::now()->subMonths(18),
            ],
            // Expired (for testing alerts)
            [
                'medicine_id' => 5,
                'supplier_id' => 3,
                'batch_number' => 'BATCH005',
                'quantity' => 10,
                'purchase_price' => 10.00,
                'selling_price' => 12.00,
                'expiry_date' => Carbon::now()->subDays(5), // Expired 5 days ago
                'manufacture_date' => Carbon::now()->subMonths(24),
            ],
            // Low stock (below reorder level)
            [
                'medicine_id' => 1,
                'supplier_id' => 1,
                'batch_number' => 'BATCH006',
                'quantity' => 20, // Below reorder level of 50
                'purchase_price' => 2.10,
                'selling_price' => 2.60,
                'expiry_date' => Carbon::now()->addMonths(6),
                'manufacture_date' => Carbon::now()->subMonths(12),
            ],
        ];

        foreach ($stocks as $stockData) {
            Stock::create($stockData);
        }

        // Create Sample Sales
        $sale1 = Sale::create([
            'user_id' => $cashier->id,
            'customer_name' => 'John Doe',
            'customer_phone' => '+1234567890',
            'subtotal' => 10.00,
            'discount' => 0.50,
            'tax' => 0.95,
            'total' => 10.45,
            'payment_method' => 'cash',
            'created_at' => Carbon::now()->subDays(1),
        ]);

        SaleItem::create([
            'sale_id' => $sale1->id,
            'medicine_id' => 1,
            'stock_id' => 1,
            'quantity' => 2,
            'unit_price' => 2.50,
            'subtotal' => 5.00,
        ]);

        SaleItem::create([
            'sale_id' => $sale1->id,
            'medicine_id' => 2,
            'stock_id' => 2,
            'quantity' => 1,
            'unit_price' => 5.00,
            'subtotal' => 5.00,
        ]);

        $sale2 = Sale::create([
            'user_id' => $cashier->id,
            'customer_name' => 'Jane Smith',
            'customer_phone' => '+1234567891',
            'subtotal' => 15.00,
            'discount' => 1.50,
            'tax' => 1.35,
            'total' => 14.85,
            'payment_method' => 'card',
            'created_at' => Carbon::now(),
        ]);

        SaleItem::create([
            'sale_id' => $sale2->id,
            'medicine_id' => 3,
            'stock_id' => 3,
            'quantity' => 4,
            'unit_price' => 3.75,
            'subtotal' => 15.00,
        ]);

        $this->command->info('Hospital Management System seeded successfully!');
        $this->command->info('Admin Login: admin@hospital.com / password123');
        $this->command->info('Cashier Login: cashier@hospital.com / password123');
    }
}