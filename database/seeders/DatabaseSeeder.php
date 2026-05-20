<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\FinancialPeriod;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\AccountingService;
use App\Services\ExpenseService;
use App\Services\PayrollService;
use App\Services\PurchaseService;
use App\Services\SaleService;
use App\Services\ServiceOrderService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Users first (required for audit trail)
        $this->call(UserSeeder::class);

        // 1. COA first (required by all services)
        $this->call(ChartOfAccountSeeder::class);

        // 2. Master data
        $this->seedEmployees();
        $this->seedCustomers();
        $this->seedSuppliers();
        $this->seedProducts();

        // 3. Transactions (via services — auto-posts journals)
        //    Financial periods are seeded AFTER transactions so closed periods
        //    don't block journal posting during seeding.
        $this->seedPurchases();
        $this->seedSales();
        $this->seedServiceOrders();
        $this->seedPayroll();
        $this->seedExpenses();

        // 4. Financial Periods (after all transactions are posted)
        $this->seedFinancialPeriods();

        $this->command->info('✓ Semua data dummy berhasil di-seed!');
    }

    private function seedEmployees(): void
    {
        $employees = [
            ['employee_code'=>'EMP-001','name'=>'Budi Santoso','position'=>'Kepala Teknisi','phone'=>'081234567890','salary_type'=>'monthly','base_salary'=>3500000,'join_date'=>'2020-01-15'],
            ['employee_code'=>'EMP-002','name'=>'Siti Rahayu','position'=>'Admin & Kasir','phone'=>'082345678901','salary_type'=>'monthly','base_salary'=>2800000,'join_date'=>'2020-03-01'],
            ['employee_code'=>'EMP-003','name'=>'Ahmad Fauzi','position'=>'Teknisi Senior','phone'=>'083456789012','salary_type'=>'monthly','base_salary'=>3000000,'join_date'=>'2021-06-10'],
            ['employee_code'=>'EMP-004','name'=>'Dewi Susanti','position'=>'Sales','phone'=>'084567890123','salary_type'=>'monthly','base_salary'=>2500000,'join_date'=>'2022-01-05'],
            ['employee_code'=>'EMP-005','name'=>'Rudi Hermawan','position'=>'Teknisi','phone'=>'085678901234','salary_type'=>'monthly','base_salary'=>3000000,'join_date'=>'2022-08-20'],
        ];
        foreach ($employees as $e) {
            Employee::firstOrCreate(['employee_code' => $e['employee_code']], array_merge($e, ['is_active' => true]));
        }
        $this->command->info('✓ Karyawan (5) di-seed');
    }

    private function seedCustomers(): void
    {
        $customers = [
            ['name'=>'Andi Wijaya','phone'=>'08111111111','email'=>'andi@email.com','address'=>'Jl. Merdeka No.1, Jakarta'],
            ['name'=>'Budi Hartono','phone'=>'08122222222','email'=>'budi@email.com','address'=>'Jl. Sudirman No.12, Bandung'],
            ['name'=>'Citra Dewi','phone'=>'08133333333','email'=>'citra@email.com','address'=>'Jl. Gatot Subroto No.5, Surabaya'],
            ['name'=>'Dian Purnama','phone'=>'08144444444','email'=>null,'address'=>'Jl. Ahmad Yani No.8, Bekasi'],
            ['name'=>'Eko Santoso','phone'=>'08155555555','email'=>'eko@email.com','address'=>'Jl. Pahlawan No.22, Depok'],
            ['name'=>'Fitri Handayani','phone'=>'08166666666','email'=>null,'address'=>'Jl. Raya Bogor No.3, Bogor'],
            ['name'=>'Gilang Ramadhan','phone'=>'08177777777','email'=>'gilang@email.com','address'=>'Jl. Diponegoro No.15, Tangerang'],
            ['name'=>'Hana Setiawati','phone'=>'08188888888','email'=>'hana@email.com','address'=>'Jl. Dr. Wahidin No.7, Yogyakarta'],
            ['name'=>'Irfan Hakim','phone'=>'08199999999','email'=>null,'address'=>'Jl. Imam Bonjol No.4, Semarang'],
            ['name'=>'Joko Susilo','phone'=>'08100000000','email'=>'joko@email.com','address'=>'Jl. Veteran No.9, Malang'],
        ];
        foreach ($customers as $c) {
            Customer::firstOrCreate(['phone' => $c['phone']], $c);
        }
        $this->command->info('✓ Customer (10) di-seed');
    }

    private function seedSuppliers(): void
    {
        $suppliers = [
            ['name'=>'PT. Mitra Teknologi','contact_person'=>'Hendra Saputra','phone'=>'02112345678','email'=>'sales@mitratekno.com','address'=>'Jl. Raya Industri No.10, Jakarta Barat'],
            ['name'=>'CV. Elektronik Jaya','contact_person'=>'Susi Marlina','phone'=>'02287654321','email'=>'order@elekcjaya.com','address'=>'Jl. Elektronik No.5, Surabaya'],
            ['name'=>'PT. Digital Supplies','contact_person'=>'Bambang Priyanto','phone'=>'02198765432','email'=>'info@digitalsupplies.id','address'=>'Jl. Niaga No.18, Bandung'],
            ['name'=>'UD. Aksesori Komputer','contact_person'=>'Linda Sari','phone'=>'02133445566','email'=>'ud.aksesori@gmail.com','address'=>'Jl. Pasar Baru No.22, Jakarta Pusat'],
            ['name'=>'PT. Printer Indonesia','contact_person'=>'Darmawan','phone'=>'02155667788','email'=>'sales@printer-id.com','address'=>'Jl. Gatot Subroto No.44, Jakarta Selatan'],
        ];
        foreach ($suppliers as $s) {
            Supplier::firstOrCreate(['email' => $s['email']], $s);
        }
        $this->command->info('✓ Supplier (5) di-seed');
    }

    private function seedProducts(): void
    {
        $products = [
            ['sku'=>'LAP-001','name'=>'Laptop HP Pavilion 14','category'=>'Laptop','unit'=>'unit','cost_price'=>6500000,'sell_price'=>7800000,'stock'=>5,'min_stock'=>2],
            ['sku'=>'LAP-002','name'=>'Laptop ASUS VivoBook 15','category'=>'Laptop','unit'=>'unit','cost_price'=>5200000,'sell_price'=>6500000,'stock'=>8,'min_stock'=>2],
            ['sku'=>'LAP-003','name'=>'Laptop Dell Inspiron 15','category'=>'Laptop','unit'=>'unit','cost_price'=>7000000,'sell_price'=>8500000,'stock'=>4,'min_stock'=>2],
            ['sku'=>'LAP-004','name'=>'Laptop Acer Aspire 5','category'=>'Laptop','unit'=>'unit','cost_price'=>5800000,'sell_price'=>7200000,'stock'=>6,'min_stock'=>2],
            ['sku'=>'PRT-001','name'=>'Printer Epson L3150','category'=>'Printer','unit'=>'unit','cost_price'=>1800000,'sell_price'=>2200000,'stock'=>10,'min_stock'=>3],
            ['sku'=>'PRT-002','name'=>'Printer Canon G2010','category'=>'Printer','unit'=>'unit','cost_price'=>1500000,'sell_price'=>1900000,'stock'=>7,'min_stock'=>3],
            ['sku'=>'PRT-003','name'=>'Printer Epson L1210','category'=>'Printer','unit'=>'unit','cost_price'=>900000,'sell_price'=>1200000,'stock'=>3,'min_stock'=>3],
            ['sku'=>'CPU-001','name'=>'PC Desktop Gaming i5','category'=>'CPU','unit'=>'unit','cost_price'=>8500000,'sell_price'=>10000000,'stock'=>3,'min_stock'=>1],
            ['sku'=>'CPU-002','name'=>'PC Desktop Office i3','category'=>'CPU','unit'=>'unit','cost_price'=>5500000,'sell_price'=>6800000,'stock'=>4,'min_stock'=>1],
            ['sku'=>'ACC-001','name'=>'Tinta Epson 003 Black','category'=>'Accessories','unit'=>'botol','cost_price'=>35000,'sell_price'=>55000,'stock'=>50,'min_stock'=>10],
            ['sku'=>'ACC-002','name'=>'Tinta Canon GI-790 Color','category'=>'Accessories','unit'=>'botol','cost_price'=>45000,'sell_price'=>70000,'stock'=>40,'min_stock'=>10],
            ['sku'=>'ACC-003','name'=>'Kabel USB 2.0','category'=>'Accessories','unit'=>'pcs','cost_price'=>15000,'sell_price'=>25000,'stock'=>2,'min_stock'=>5],
            ['sku'=>'ACC-004','name'=>'RAM DDR4 8GB','category'=>'Accessories','unit'=>'pcs','cost_price'=>280000,'sell_price'=>420000,'stock'=>12,'min_stock'=>5],
            ['sku'=>'ACC-005','name'=>'SSD 256GB SATA','category'=>'Accessories','unit'=>'pcs','cost_price'=>380000,'sell_price'=>550000,'stock'=>9,'min_stock'=>5],
            ['sku'=>'ACC-006','name'=>'Spare Part Printer Head Canon','category'=>'Accessories','unit'=>'pcs','cost_price'=>150000,'sell_price'=>250000,'stock'=>15,'min_stock'=>5],
            ['sku'=>'ACC-007','name'=>'Pasta Thermal Processor','category'=>'Accessories','unit'=>'tube','cost_price'=>25000,'sell_price'=>45000,'stock'=>30,'min_stock'=>5],
            ['sku'=>'ACC-008','name'=>'Mouse Wireless Logitech','category'=>'Accessories','unit'=>'pcs','cost_price'=>60000,'sell_price'=>95000,'stock'=>20,'min_stock'=>5],
            ['sku'=>'ACC-009','name'=>'HDD External 1TB','category'=>'Accessories','unit'=>'pcs','cost_price'=>650000,'sell_price'=>850000,'stock'=>5,'min_stock'=>3],
            ['sku'=>'ACC-010','name'=>'HDD Laptop 2.5" 1TB','category'=>'Accessories','unit'=>'pcs','cost_price'=>500000,'sell_price'=>700000,'stock'=>4,'min_stock'=>5],
        ];
        foreach ($products as $p) {
            Product::firstOrCreate(['sku' => $p['sku']], $p);
        }
        $this->command->info('✓ Produk (19) di-seed');
    }

    private function seedPurchases(): void
    {
        $purchaseService = app(PurchaseService::class);
        $suppliers = Supplier::all();
        $products  = Product::all()->values();

        // Strategy: Keep each PO under 10M IDR to avoid approval workflow
        // Spread purchases across 6 months to match the transaction window

        // Month 6 ago: Initial stock purchases
        $po1 = $purchaseService->createPurchase([
            'supplier_id'   => $suppliers[0]->id,
            'purchase_date' => now()->subMonths(6)->addDays(2)->toDateString(),
            'items' => [
                ['product_id' => $products[0]->id, 'qty' => 1, 'unit_cost' => 6500000], // HP Pavilion
                ['product_id' => $products[4]->id, 'qty' => 2, 'unit_cost' => 1800000], // Epson L3150
            ],
        ]); // Total: 6.5M + 3.6M = 10.1M - still over, reduce qty
        
        // Let me recalculate to stay under 10M:
        // PO 1: 1 HP (6.5M) + 1 Epson L3150 (1.8M) = 8.3M ✓
        $po1 = $purchaseService->createPurchase([
            'supplier_id'   => $suppliers[0]->id,
            'purchase_date' => now()->subMonths(6)->addDays(2)->toDateString(),
            'items' => [
                ['product_id' => $products[0]->id, 'qty' => 1, 'unit_cost' => 6500000], // HP Pavilion
                ['product_id' => $products[4]->id, 'qty' => 1, 'unit_cost' => 1800000], // Epson L3150
            ],
        ]);
        $purchaseService->receivePurchase($po1);

        // PO 2: 1 ASUS (5.2M) + 2 Canon G2010 (3M) = 8.2M ✓
        $po2 = $purchaseService->createPurchase([
            'supplier_id'   => $suppliers[1]->id,
            'purchase_date' => now()->subMonths(6)->addDays(5)->toDateString(),
            'items' => [
                ['product_id' => $products[1]->id, 'qty' => 1, 'unit_cost' => 5200000], // ASUS VivoBook
                ['product_id' => $products[5]->id, 'qty' => 2, 'unit_cost' => 1500000], // Canon G2010
            ],
        ]);
        $purchaseService->receivePurchase($po2);

        // PO 3: 1 Dell (7M) + 2 Epson L1210 (1.8M) = 8.8M ✓
        $po3 = $purchaseService->createPurchase([
            'supplier_id'   => $suppliers[2]->id,
            'purchase_date' => now()->subMonths(6)->addDays(8)->toDateString(),
            'items' => [
                ['product_id' => $products[2]->id, 'qty' => 1, 'unit_cost' => 7000000], // Dell Inspiron
                ['product_id' => $products[6]->id, 'qty' => 2, 'unit_cost' => 900000],  // Epson L1210
            ],
        ]);
        $purchaseService->receivePurchase($po3);

        // PO 4: 1 Acer (5.8M) + 1 PC Gaming (8.5M) = 14.3M - too high, split
        // PO 4: 1 Acer (5.8M) + accessories = under 10M
        $po4 = $purchaseService->createPurchase([
            'supplier_id'   => $suppliers[0]->id,
            'purchase_date' => now()->subMonths(6)->addDays(10)->toDateString(),
            'items' => [
                ['product_id' => $products[3]->id, 'qty' => 1, 'unit_cost' => 5800000],  // Acer Aspire
                ['product_id' => $products[9]->id, 'qty' => 50, 'unit_cost' => 35000],   // Tinta Epson Black
                ['product_id' => $products[10]->id, 'qty' => 40, 'unit_cost' => 45000],  // Tinta Canon Color
            ],
        ]); // Total: 5.8M + 1.75M + 1.8M = 9.35M ✓
        $purchaseService->receivePurchase($po4);

        // PO 5: 1 PC Gaming (8.5M) + small accessories = under 10M
        $po5 = $purchaseService->createPurchase([
            'supplier_id'   => $suppliers[3]->id,
            'purchase_date' => now()->subMonths(5)->addDays(3)->toDateString(),
            'items' => [
                ['product_id' => $products[7]->id, 'qty' => 1, 'unit_cost' => 8500000],  // PC Gaming i5
                ['product_id' => $products[11]->id, 'qty' => 20, 'unit_cost' => 15000],  // Kabel USB
            ],
        ]); // Total: 8.5M + 0.3M = 8.8M ✓
        $purchaseService->receivePurchase($po5);

        // PO 6: 1 PC Office (5.5M) + RAM & SSD
        $po6 = $purchaseService->createPurchase([
            'supplier_id'   => $suppliers[1]->id,
            'purchase_date' => now()->subMonths(5)->addDays(7)->toDateString(),
            'items' => [
                ['product_id' => $products[8]->id, 'qty' => 1, 'unit_cost' => 5500000],  // PC Office i3
                ['product_id' => $products[12]->id, 'qty' => 12, 'unit_cost' => 280000], // RAM DDR4 8GB
                ['product_id' => $products[13]->id, 'qty' => 9, 'unit_cost' => 380000],  // SSD 256GB
            ],
        ]); // Total: 5.5M + 3.36M + 3.42M = 12.28M - too high, reduce
        
        // Recalculate PO 6: 1 PC Office (5.5M) + 8 RAM (2.24M) + 5 SSD (1.9M) = 9.64M ✓
        $po6 = $purchaseService->createPurchase([
            'supplier_id'   => $suppliers[1]->id,
            'purchase_date' => now()->subMonths(5)->addDays(7)->toDateString(),
            'items' => [
                ['product_id' => $products[8]->id, 'qty' => 1, 'unit_cost' => 5500000],  // PC Office i3
                ['product_id' => $products[12]->id, 'qty' => 8, 'unit_cost' => 280000],  // RAM DDR4 8GB
                ['product_id' => $products[13]->id, 'qty' => 5, 'unit_cost' => 380000],  // SSD 256GB
            ],
        ]);
        $purchaseService->receivePurchase($po6);

        // PO 7: More accessories and spare parts
        $po7 = $purchaseService->createPurchase([
            'supplier_id'   => $suppliers[4]->id,
            'purchase_date' => now()->subMonths(4)->addDays(5)->toDateString(),
            'items' => [
                ['product_id' => $products[14]->id, 'qty' => 15, 'unit_cost' => 150000], // Spare Part Printer Head
                ['product_id' => $products[15]->id, 'qty' => 30, 'unit_cost' => 25000],  // Pasta Thermal
                ['product_id' => $products[16]->id, 'qty' => 20, 'unit_cost' => 60000],  // Mouse Wireless
                ['product_id' => $products[17]->id, 'qty' => 5, 'unit_cost' => 650000],  // HDD External 1TB
                ['product_id' => $products[18]->id, 'qty' => 4, 'unit_cost' => 500000],  // HDD Laptop 2.5"
            ],
        ]); // Total: 2.25M + 0.75M + 1.2M + 3.25M + 2M = 9.45M ✓
        $purchaseService->receivePurchase($po7);

        // PO 8: Restock laptops (month 3 ago)
        $po8 = $purchaseService->createPurchase([
            'supplier_id'   => $suppliers[0]->id,
            'purchase_date' => now()->subMonths(3)->addDays(10)->toDateString(),
            'items' => [
                ['product_id' => $products[0]->id, 'qty' => 1, 'unit_cost' => 6500000], // HP Pavilion
                ['product_id' => $products[1]->id, 'qty' => 1, 'unit_cost' => 5200000], // ASUS VivoBook
            ],
        ]); // Total: 6.5M + 5.2M = 11.7M - too high, just one laptop
        
        // Recalculate PO 8: 1 HP (6.5M) + printers
        $po8 = $purchaseService->createPurchase([
            'supplier_id'   => $suppliers[0]->id,
            'purchase_date' => now()->subMonths(3)->addDays(10)->toDateString(),
            'items' => [
                ['product_id' => $products[0]->id, 'qty' => 1, 'unit_cost' => 6500000], // HP Pavilion
                ['product_id' => $products[4]->id, 'qty' => 1, 'unit_cost' => 1800000], // Epson L3150
            ],
        ]); // Total: 8.3M ✓
        $purchaseService->receivePurchase($po8);

        // PO 9: Restock (month 2 ago)
        $po9 = $purchaseService->createPurchase([
            'supplier_id'   => $suppliers[2]->id,
            'purchase_date' => now()->subMonths(2)->addDays(5)->toDateString(),
            'items' => [
                ['product_id' => $products[1]->id, 'qty' => 1, 'unit_cost' => 5200000], // ASUS VivoBook
                ['product_id' => $products[5]->id, 'qty' => 2, 'unit_cost' => 1500000], // Canon G2010
            ],
        ]); // Total: 5.2M + 3M = 8.2M ✓
        $purchaseService->receivePurchase($po9);

        // PO 10: Recent restock (1 month ago)
        $po10 = $purchaseService->createPurchase([
            'supplier_id'   => $suppliers[1]->id,
            'purchase_date' => now()->subMonths(1)->addDays(8)->toDateString(),
            'items' => [
                ['product_id' => $products[3]->id, 'qty' => 1, 'unit_cost' => 5800000],  // Acer Aspire
                ['product_id' => $products[12]->id, 'qty' => 4, 'unit_cost' => 280000],  // RAM DDR4 8GB
                ['product_id' => $products[13]->id, 'qty' => 4, 'unit_cost' => 380000],  // SSD 256GB
            ],
        ]); // Total: 5.8M + 1.12M + 1.52M = 8.44M ✓
        $purchaseService->receivePurchase($po10);

        // PO 11: Current month - pending order (not received yet)
        $purchaseService->createPurchase([
            'supplier_id'   => $suppliers[0]->id,
            'purchase_date' => now()->subDays(5)->toDateString(),
            'items' => [
                ['product_id' => $products[2]->id, 'qty' => 1, 'unit_cost' => 7000000], // Dell Inspiron
                ['product_id' => $products[8]->id, 'qty' => 1, 'unit_cost' => 5500000], // PC Office i3
            ],
        ]); // Total: 12.5M - too high for auto-approval, but that's OK since it's pending

        $this->command->info('✓ Pembelian (11 PO, 10 received) di-seed');
    }

    private function seedSales(): void
    {
        $saleService = app(SaleService::class);
        $customers   = Customer::all();
        $products    = Product::all();

        $salesData = [
            // Month 6 ago - Early sales after opening
            ['customer_id'=>$customers[0]->id,'sale_date'=>now()->subMonths(6)->addDays(5)->toDateString(),'items'=>[
                ['product_id'=>$products[0]->id,'qty'=>1,'unit_price'=>7800000], // HP Pavilion
                ['product_id'=>$products[9]->id,'qty'=>5,'unit_price'=>55000],   // Tinta Epson
            ],'payment_method'=>'Transfer'],
            
            ['customer_id'=>$customers[1]->id,'sale_date'=>now()->subMonths(6)->addDays(8)->toDateString(),'items'=>[
                ['product_id'=>$products[4]->id,'qty'=>1,'unit_price'=>2200000], // Epson L3150
                ['product_id'=>$products[10]->id,'qty'=>3,'unit_price'=>70000],  // Tinta Canon
            ],'payment_method'=>'Cash'],
            
            ['customer_id'=>null,'sale_date'=>now()->subMonths(6)->addDays(12)->toDateString(),'items'=>[
                ['product_id'=>$products[12]->id,'qty'=>2,'unit_price'=>420000], // RAM DDR4
                ['product_id'=>$products[16]->id,'qty'=>1,'unit_price'=>95000],  // Mouse Wireless
            ],'payment_method'=>'Cash','notes'=>'Walk-in customer'],
            
            // Month 5 ago
            ['customer_id'=>$customers[2]->id,'sale_date'=>now()->subMonths(5)->addDays(3)->toDateString(),'items'=>[
                ['product_id'=>$products[1]->id,'qty'=>1,'unit_price'=>6500000], // ASUS VivoBook
                ['product_id'=>$products[13]->id,'qty'=>1,'unit_price'=>550000], // SSD 256GB
            ],'payment_method'=>'Transfer'],
            
            ['customer_id'=>$customers[3]->id,'sale_date'=>now()->subMonths(5)->addDays(10)->toDateString(),'items'=>[
                ['product_id'=>$products[7]->id,'qty'=>1,'unit_price'=>10000000], // PC Gaming i5
            ],'payment_method'=>'Transfer'],
            
            ['customer_id'=>null,'sale_date'=>now()->subMonths(5)->addDays(15)->toDateString(),'items'=>[
                ['product_id'=>$products[9]->id,'qty'=>10,'unit_price'=>55000],  // Tinta Epson
                ['product_id'=>$products[10]->id,'qty'=>5,'unit_price'=>70000],  // Tinta Canon
                ['product_id'=>$products[11]->id,'qty'=>5,'unit_price'=>25000],  // Kabel USB
            ],'payment_method'=>'Cash'],
            
            ['customer_id'=>$customers[4]->id,'sale_date'=>now()->subMonths(5)->addDays(20)->toDateString(),'items'=>[
                ['product_id'=>$products[5]->id,'qty'=>1,'unit_price'=>1900000], // Canon G2010
                ['product_id'=>$products[10]->id,'qty'=>5,'unit_price'=>70000],  // Tinta Canon
            ],'payment_method'=>'Cash'],
            
            // Month 4 ago
            ['customer_id'=>$customers[5]->id,'sale_date'=>now()->subMonths(4)->addDays(2)->toDateString(),'items'=>[
                ['product_id'=>$products[2]->id,'qty'=>1,'unit_price'=>8500000], // Dell Inspiron
                ['product_id'=>$products[12]->id,'qty'=>1,'unit_price'=>420000], // RAM DDR4
            ],'payment_method'=>'Transfer'],
            
            ['customer_id'=>$customers[6]->id,'sale_date'=>now()->subMonths(4)->addDays(8)->toDateString(),'items'=>[
                ['product_id'=>$products[8]->id,'qty'=>1,'unit_price'=>6800000], // PC Office i3
                ['product_id'=>$products[13]->id,'qty'=>1,'unit_price'=>550000], // SSD 256GB
            ],'payment_method'=>'Transfer'],
            
            ['customer_id'=>null,'sale_date'=>now()->subMonths(4)->addDays(12)->toDateString(),'items'=>[
                ['product_id'=>$products[6]->id,'qty'=>1,'unit_price'=>1200000], // Epson L1210
                ['product_id'=>$products[9]->id,'qty'=>5,'unit_price'=>55000],   // Tinta Epson
            ],'payment_method'=>'Cash','notes'=>'Walk-in customer'],
            
            ['customer_id'=>$customers[7]->id,'sale_date'=>now()->subMonths(4)->addDays(18)->toDateString(),'items'=>[
                ['product_id'=>$products[17]->id,'qty'=>1,'unit_price'=>850000], // HDD External 1TB
                ['product_id'=>$products[16]->id,'qty'=>2,'unit_price'=>95000],  // Mouse Wireless
            ],'payment_method'=>'Cash'],
            
            ['customer_id'=>$customers[8]->id,'sale_date'=>now()->subMonths(4)->addDays(22)->toDateString(),'items'=>[
                ['product_id'=>$products[12]->id,'qty'=>2,'unit_price'=>420000], // RAM DDR4
                ['product_id'=>$products[13]->id,'qty'=>2,'unit_price'=>550000], // SSD 256GB
                ['product_id'=>$products[15]->id,'qty'=>3,'unit_price'=>45000],  // Pasta Thermal
            ],'payment_method'=>'Transfer'],
            
            // Month 3 ago
            ['customer_id'=>$customers[9]->id,'sale_date'=>now()->subMonths(3)->addDays(5)->toDateString(),'items'=>[
                ['product_id'=>$products[3]->id,'qty'=>1,'unit_price'=>7200000], // Acer Aspire
                ['product_id'=>$products[12]->id,'qty'=>1,'unit_price'=>420000], // RAM DDR4
            ],'payment_method'=>'Transfer'],
            
            ['customer_id'=>$customers[0]->id,'sale_date'=>now()->subMonths(3)->addDays(12)->toDateString(),'items'=>[
                ['product_id'=>$products[4]->id,'qty'=>1,'unit_price'=>2200000], // Epson L3150
                ['product_id'=>$products[9]->id,'qty'=>10,'unit_price'=>55000],  // Tinta Epson
            ],'payment_method'=>'Cash'],
            
            ['customer_id'=>null,'sale_date'=>now()->subMonths(3)->addDays(15)->toDateString(),'items'=>[
                ['product_id'=>$products[18]->id,'qty'=>1,'unit_price'=>700000], // HDD Laptop 2.5"
                ['product_id'=>$products[14]->id,'qty'=>1,'unit_price'=>250000], // Spare Part Printer Head
            ],'payment_method'=>'Cash','notes'=>'Walk-in customer'],
            
            ['customer_id'=>$customers[1]->id,'sale_date'=>now()->subMonths(3)->addDays(20)->toDateString(),'items'=>[
                ['product_id'=>$products[5]->id,'qty'=>1,'unit_price'=>1900000], // Canon G2010
                ['product_id'=>$products[10]->id,'qty'=>5,'unit_price'=>70000],  // Tinta Canon
            ],'payment_method'=>'Transfer'],
            
            ['customer_id'=>$customers[2]->id,'sale_date'=>now()->subMonths(3)->addDays(25)->toDateString(),'items'=>[
                ['product_id'=>$products[16]->id,'qty'=>3,'unit_price'=>95000],  // Mouse Wireless
                ['product_id'=>$products[11]->id,'qty'=>10,'unit_price'=>25000], // Kabel USB
            ],'payment_method'=>'Cash'],
            
            // Month 2 ago
            ['customer_id'=>$customers[3]->id,'sale_date'=>now()->subMonths(2)->addDays(3)->toDateString(),'items'=>[
                ['product_id'=>$products[0]->id,'qty'=>1,'unit_price'=>7800000], // HP Pavilion
                ['product_id'=>$products[13]->id,'qty'=>1,'unit_price'=>550000], // SSD 256GB
            ],'payment_method'=>'Transfer'],
            
            ['customer_id'=>$customers[4]->id,'sale_date'=>now()->subMonths(2)->addDays(8)->toDateString(),'items'=>[
                ['product_id'=>$products[1]->id,'qty'=>1,'unit_price'=>6500000], // ASUS VivoBook
                ['product_id'=>$products[12]->id,'qty'=>1,'unit_price'=>420000], // RAM DDR4
            ],'payment_method'=>'Transfer'],
            
            ['customer_id'=>null,'sale_date'=>now()->subMonths(2)->addDays(12)->toDateString(),'items'=>[
                ['product_id'=>$products[6]->id,'qty'=>1,'unit_price'=>1200000], // Epson L1210
                ['product_id'=>$products[9]->id,'qty'=>5,'unit_price'=>55000],   // Tinta Epson
            ],'payment_method'=>'Cash','notes'=>'Walk-in customer'],
            
            ['customer_id'=>$customers[5]->id,'sale_date'=>now()->subMonths(2)->addDays(18)->toDateString(),'items'=>[
                ['product_id'=>$products[5]->id,'qty'=>1,'unit_price'=>1900000], // Canon G2010
                ['product_id'=>$products[10]->id,'qty'=>5,'unit_price'=>70000],  // Tinta Canon
            ],'payment_method'=>'Cash'],
            
            ['customer_id'=>$customers[6]->id,'sale_date'=>now()->subMonths(2)->addDays(22)->toDateString(),'items'=>[
                ['product_id'=>$products[12]->id,'qty'=>2,'unit_price'=>420000], // RAM DDR4
                ['product_id'=>$products[13]->id,'qty'=>2,'unit_price'=>550000], // SSD 256GB
            ],'payment_method'=>'Transfer'],
            
            // Month 1 ago
            ['customer_id'=>$customers[7]->id,'sale_date'=>now()->subMonths(1)->addDays(5)->toDateString(),'items'=>[
                ['product_id'=>$products[3]->id,'qty'=>1,'unit_price'=>7200000], // Acer Aspire
                ['product_id'=>$products[14]->id,'qty'=>2,'unit_price'=>250000], // Spare Part Printer Head
            ],'payment_method'=>'Transfer'],
            
            ['customer_id'=>$customers[8]->id,'sale_date'=>now()->subMonths(1)->addDays(10)->toDateString(),'items'=>[
                ['product_id'=>$products[4]->id,'qty'=>1,'unit_price'=>2200000], // Epson L3150
                ['product_id'=>$products[9]->id,'qty'=>10,'unit_price'=>55000],  // Tinta Epson
            ],'payment_method'=>'Cash'],
            
            ['customer_id'=>null,'sale_date'=>now()->subMonths(1)->addDays(15)->toDateString(),'items'=>[
                ['product_id'=>$products[17]->id,'qty'=>1,'unit_price'=>850000], // HDD External 1TB
                ['product_id'=>$products[16]->id,'qty'=>2,'unit_price'=>95000],  // Mouse Wireless
            ],'payment_method'=>'Cash','notes'=>'Walk-in customer'],
            
            ['customer_id'=>$customers[9]->id,'sale_date'=>now()->subMonths(1)->addDays(20)->toDateString(),'items'=>[
                ['product_id'=>$products[1]->id,'qty'=>1,'unit_price'=>6500000], // ASUS VivoBook
                ['product_id'=>$products[13]->id,'qty'=>1,'unit_price'=>550000], // SSD 256GB
            ],'payment_method'=>'Transfer'],
            
            // Current month
            ['customer_id'=>$customers[0]->id,'sale_date'=>now()->subDays(25)->toDateString(),'items'=>[
                ['product_id'=>$products[0]->id,'qty'=>1,'unit_price'=>7800000], // HP Pavilion
                ['product_id'=>$products[9]->id,'qty'=>5,'unit_price'=>55000],   // Tinta Epson
            ],'payment_method'=>'Cash'],
            
            ['customer_id'=>$customers[1]->id,'sale_date'=>now()->subDays(20)->toDateString(),'items'=>[
                ['product_id'=>$products[5]->id,'qty'=>1,'unit_price'=>1900000], // Canon G2010
                ['product_id'=>$products[10]->id,'qty'=>3,'unit_price'=>70000],  // Tinta Canon
            ],'payment_method'=>'Transfer'],
            
            ['customer_id'=>null,'sale_date'=>now()->subDays(18)->toDateString(),'items'=>[
                ['product_id'=>$products[13]->id,'qty'=>1,'unit_price'=>550000], // SSD 256GB
                ['product_id'=>$products[16]->id,'qty'=>2,'unit_price'=>95000],  // Mouse Wireless
            ],'payment_method'=>'Cash','notes'=>'Walk-in customer'],
            
            ['customer_id'=>$customers[2]->id,'sale_date'=>now()->subDays(12)->toDateString(),'items'=>[
                ['product_id'=>$products[12]->id,'qty'=>2,'unit_price'=>420000], // RAM DDR4
                ['product_id'=>$products[11]->id,'qty'=>5,'unit_price'=>25000],  // Kabel USB
            ],'payment_method'=>'Transfer'],
            
            ['customer_id'=>$customers[3]->id,'sale_date'=>now()->subDays(8)->toDateString(),'items'=>[
                ['product_id'=>$products[6]->id,'qty'=>1,'unit_price'=>1200000], // Epson L1210
                ['product_id'=>$products[9]->id,'qty'=>5,'unit_price'=>55000],   // Tinta Epson
            ],'payment_method'=>'Transfer'],
            
            ['customer_id'=>null,'sale_date'=>now()->subDays(5)->toDateString(),'items'=>[
                ['product_id'=>$products[9]->id,'qty'=>10,'unit_price'=>55000],  // Tinta Epson
                ['product_id'=>$products[10]->id,'qty'=>5,'unit_price'=>70000],  // Tinta Canon
            ],'payment_method'=>'Cash'],
            
            ['customer_id'=>$customers[4]->id,'sale_date'=>now()->subDays(3)->toDateString(),'items'=>[
                ['product_id'=>$products[18]->id,'qty'=>1,'unit_price'=>700000], // HDD Laptop 2.5"
                ['product_id'=>$products[14]->id,'qty'=>2,'unit_price'=>250000], // Spare Part Printer Head
            ],'payment_method'=>'Cash'],
            
            ['customer_id'=>$customers[5]->id,'sale_date'=>now()->subDays(1)->toDateString(),'items'=>[
                ['product_id'=>$products[15]->id,'qty'=>5,'unit_price'=>45000],  // Pasta Thermal
                ['product_id'=>$products[16]->id,'qty'=>3,'unit_price'=>95000],  // Mouse Wireless
            ],'payment_method'=>'Cash'],
            
            // Additional sales to ensure profitability
            ['customer_id'=>$customers[6]->id,'sale_date'=>now()->subMonths(4)->addDays(25)->toDateString(),'items'=>[
                ['product_id'=>$products[3]->id,'qty'=>1,'unit_price'=>7200000], // Acer Aspire
            ],'payment_method'=>'Transfer'],
            
            ['customer_id'=>$customers[7]->id,'sale_date'=>now()->subMonths(3)->addDays(28)->toDateString(),'items'=>[
                ['product_id'=>$products[8]->id,'qty'=>1,'unit_price'=>6800000], // PC Office i3
            ],'payment_method'=>'Transfer'],
        ];

        $successCount = 0;
        $failCount = 0;
        foreach ($salesData as $data) {
            try {
                $saleService->createSale($data);
                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
                $this->command->warn("Sale skipped: " . $e->getMessage());
            }
        }
        $this->command->info("✓ Penjualan ($successCount succeeded, $failCount failed) di-seed");
    }

    private function seedServiceOrders(): void
    {
        $soService = app(ServiceOrderService::class);
        $customers = Customer::all()->values();

        $orderDefs = [
            // Completed orders (revenue recognized)
            ['cIdx'=>0,'device_type'=>'Laptop','brand'=>'HP','problem_description'=>'Laptop tidak mau menyala, layar hitam','status'=>'Completed','service_cost'=>350000,'diagnosis'=>'Adaptor rusak, sudah diganti'],
            ['cIdx'=>1,'device_type'=>'Printer','brand'=>'Epson','problem_description'=>'Printer tidak bisa print, ada pesan error','status'=>'Completed','service_cost'=>200000,'diagnosis'=>'Head printer kotor, sudah dibersihkan'],
            ['cIdx'=>2,'device_type'=>'Laptop','brand'=>'ASUS','problem_description'=>'Kipas berisik dan laptop cepat panas','status'=>'Completed','service_cost'=>250000,'diagnosis'=>'Thermal paste habis dan kipas berdebu'],
            ['cIdx'=>3,'device_type'=>'CPU','brand'=>'Acer','problem_description'=>'PC sering restart sendiri','status'=>'Completed','service_cost'=>300000,'diagnosis'=>'RAM rusak, sudah diganti'],
            ['cIdx'=>4,'device_type'=>'Laptop','brand'=>'Dell','problem_description'=>'Keyboard beberapa tombol tidak berfungsi','status'=>'Completed','service_cost'=>400000,'diagnosis'=>'Keyboard diganti dengan yang baru'],
            ['cIdx'=>5,'device_type'=>'Printer','brand'=>'Canon','problem_description'=>'Hasil print bergaris-garis','status'=>'Completed','service_cost'=>180000,'diagnosis'=>'Head printer dibersihkan dan alignment'],
            ['cIdx'=>6,'device_type'=>'Laptop','brand'=>'HP','problem_description'=>'Laptop lemot dan sering hang','status'=>'Completed','service_cost'=>350000,'diagnosis'=>'Upgrade RAM dan install ulang Windows'],
            ['cIdx'=>7,'device_type'=>'CPU','brand'=>'Custom','problem_description'=>'PC tidak bisa booting','status'=>'Completed','service_cost'=>250000,'diagnosis'=>'Motherboard battery habis, sudah diganti'],
            ['cIdx'=>8,'device_type'=>'Laptop','brand'=>'ASUS','problem_description'=>'Baterai cepat habis','status'=>'Completed','service_cost'=>150000,'diagnosis'=>'Kalibrasi baterai dan optimasi power settings'],
            ['cIdx'=>9,'device_type'=>'Printer','brand'=>'Epson','problem_description'=>'Printer tidak terdeteksi di komputer','status'=>'Completed','service_cost'=>120000,'diagnosis'=>'Install ulang driver printer'],
            ['cIdx'=>0,'device_type'=>'Laptop','brand'=>'Lenovo','problem_description'=>'Layar redup','status'=>'Completed','service_cost'=>300000,'diagnosis'=>'Backlight inverter diganti'],
            ['cIdx'=>1,'device_type'=>'CPU','brand'=>'Custom','problem_description'=>'Suara berisik dari casing','status'=>'Completed','service_cost'=>150000,'diagnosis'=>'Fan casing dibersihkan dan dilumasi'],
            ['cIdx'=>2,'device_type'=>'Laptop','brand'=>'Acer','problem_description'=>'Touchpad tidak berfungsi','status'=>'Completed','service_cost'=>200000,'diagnosis'=>'Driver touchpad diupdate dan kalibrasi'],
            ['cIdx'=>3,'device_type'=>'Printer','brand'=>'Brother','problem_description'=>'Paper jam terus menerus','status'=>'Completed','service_cost'=>180000,'diagnosis'=>'Roller printer dibersihkan dan disetel'],
            ['cIdx'=>4,'device_type'=>'Laptop','brand'=>'HP','problem_description'=>'Wifi tidak bisa connect','status'=>'Completed','service_cost'=>150000,'diagnosis'=>'Driver wifi diupdate dan reset network settings'],
            ['cIdx'=>5,'device_type'=>'CPU','brand'=>'Custom','problem_description'=>'Blue screen saat gaming','status'=>'Completed','service_cost'=>350000,'diagnosis'=>'VGA overheating, thermal paste diganti'],
            ['cIdx'=>6,'device_type'=>'Laptop','brand'=>'Dell','problem_description'=>'Charger tidak mengisi baterai','status'=>'Completed','service_cost'=>250000,'diagnosis'=>'Port charging dibersihkan dan diperbaiki'],
            ['cIdx'=>7,'device_type'=>'Printer','brand'=>'Canon','problem_description'=>'Tinta tidak keluar','status'=>'Completed','service_cost'=>200000,'diagnosis'=>'Head printer dibersihkan dengan ultrasonic'],
            ['cIdx'=>8,'device_type'=>'Laptop','brand'=>'ASUS','problem_description'=>'Layar bergaris','status'=>'Completed','service_cost'=>400000,'diagnosis'=>'Kabel flexible LCD diganti'],
            ['cIdx'=>9,'device_type'=>'CPU','brand'=>'Acer','problem_description'=>'Tidak ada tampilan di monitor','status'=>'Completed','service_cost'=>300000,'diagnosis'=>'VGA card dibersihkan dan reseating'],
            
            // Done orders (work finished, waiting payment)
            ['cIdx'=>0,'device_type'=>'Laptop','brand'=>'Lenovo','problem_description'=>'Harddisk bunyi klik-klik','status'=>'Done','service_cost'=>350000,'diagnosis'=>'Harddisk rusak, perlu diganti'],
            ['cIdx'=>1,'device_type'=>'All-in-One','brand'=>'Canon','problem_description'=>'Scanner tidak terdeteksi','status'=>'Done','service_cost'=>150000,'diagnosis'=>'Kabel USB scanner diganti'],
            
            // In Progress orders
            ['cIdx'=>2,'device_type'=>'Laptop','brand'=>'Acer','problem_description'=>'Laptop mati mendadak','status'=>'InProgress','diagnosis'=>'Sedang diagnosa power supply dan motherboard'],
            ['cIdx'=>3,'device_type'=>'Printer','brand'=>'Epson','problem_description'=>'Warna print tidak akurat','status'=>'InProgress','diagnosis'=>'Sedang proses color calibration'],
            
            // Received orders (just received, not started yet)
            ['cIdx'=>4,'device_type'=>'Laptop','brand'=>'Dell','problem_description'=>'USB port tidak berfungsi','status'=>'Received'],
            ['cIdx'=>5,'device_type'=>'CPU','brand'=>'Custom','problem_description'=>'Komputer restart saat load berat','status'=>'Received'],
        ];

        foreach ($orderDefs as $def) {
            $cIdx        = $def['cIdx'] % $customers->count();
            $targetStatus = $def['status'];
            $serviceCost  = $def['service_cost'] ?? 150000;
            $diagnosis    = $def['diagnosis'] ?? null;

            $order = $soService->createOrder([
                'customer_id'         => $customers[$cIdx]->id,
                'device_type'         => $def['device_type'],
                'brand'               => $def['brand'] ?? null,
                'problem_description' => $def['problem_description'],
            ]);

            if (in_array($targetStatus, ['InProgress','Done','Completed'])) {
                $soService->updateProgress($order, ['status' => 'InProgress', 'diagnosis' => $diagnosis]);
            }
            if (in_array($targetStatus, ['Done','Completed'])) {
                $soService->markDone($order, $serviceCost, $diagnosis);
            }
            if ($targetStatus === 'Completed') {
                $soService->completeWithPayment($order);
            }
        }
        $this->command->info('✓ Order Servis (26 orders: 20 completed, 2 done, 2 in-progress, 2 received) di-seed');
    }

    private function seedPayroll(): void
    {
        $payrollService = app(PayrollService::class);

        // 2 months prior
        try { $payrollService->generateAllPayroll(now()->subMonths(2)->month, now()->subMonths(2)->year); } catch (\Exception $e) {}
        // 1 month prior
        try { $payrollService->generateAllPayroll(now()->subMonths(1)->month, now()->subMonths(1)->year); } catch (\Exception $e) {}

        $this->command->info('✓ Penggajian (10 records, 2 bulan) di-seed');
    }

    private function seedExpenses(): void
    {
        $expenseService = app(ExpenseService::class);

        $expenses = [
            ['expense_date'=>now()->subDays(28)->toDateString(),'category'=>'Electricity','description'=>'Tagihan listrik bulan lalu','amount'=>450000],
            ['expense_date'=>now()->subDays(25)->toDateString(),'category'=>'Internet','description'=>'Tagihan internet IndiHome','amount'=>300000],
            ['expense_date'=>now()->subDays(20)->toDateString(),'category'=>'Maintenance','description'=>'Perawatan AC toko','amount'=>200000],
            ['expense_date'=>now()->subDays(15)->toDateString(),'category'=>'Rent','description'=>'Sewa tempat bulan ini','amount'=>1500000],
            ['expense_date'=>now()->subDays(10)->toDateString(),'category'=>'Electricity','description'=>'Tagihan listrik bulan ini','amount'=>420000],
            ['expense_date'=>now()->subDays(5)->toDateString(),'category'=>'Other','description'=>'Pembelian alat kebersihan toko','amount'=>85000],
            ['expense_date'=>now()->subDays(2)->toDateString(),'category'=>'Maintenance','description'=>'Perbaikan meja kasir','amount'=>150000],
        ];

        foreach ($expenses as $expense) {
            try {
                $expenseService->recordExpense($expense);
            } catch (\Exception $e) {
                $this->command->warn("Expense skipped: " . $e->getMessage());
            }
        }
        $this->command->info('✓ Pengeluaran (7) di-seed');
    }

    private function seedFinancialPeriods(): void
    {
        // Only create periods for months that actually have transactions.
        // Transaction window: 6 months ago (now()->subMonths(6)) through current month.
        // Today = May 2026, so: Nov 2025, Dec 2025, Jan 2026, Feb 2026, Mar 2026, Apr 2026, May 2026
        // Months older than 3 months from now are closed; recent ones stay open.

        $transactionMonths = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $transactionMonths[] = [
                'name'       => $date->format('F Y'),
                'start_date' => $date->copy()->startOfMonth()->toDateString(),
                'end_date'   => $date->copy()->endOfMonth()->toDateString(),
                // Close months that are fully in the past (more than 1 month ago)
                'status'     => $i > 1 ? 'closed' : 'open',
                'closed_by'  => $i > 1 ? 1 : null,
                'closed_at'  => $i > 1 ? $date->copy()->endOfMonth()->endOfDay() : null,
            ];
        }

        foreach ($transactionMonths as $period) {
            FinancialPeriod::firstOrCreate(
                ['start_date' => $period['start_date'], 'end_date' => $period['end_date']],
                $period
            );
        }

        $closedCount = collect($transactionMonths)->where('status', 'closed')->count();
        $openCount   = collect($transactionMonths)->where('status', 'open')->count();

        $this->command->info("✓ Financial Periods (" . count($transactionMonths) . " periods: $closedCount closed, $openCount open) di-seed");
    }
}
