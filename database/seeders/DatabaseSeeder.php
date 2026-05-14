<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Employee;
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
        $this->seedPurchases();
        $this->seedSales();
        $this->seedServiceOrders();
        $this->seedPayroll();
        $this->seedExpenses();

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
        $supplier = Supplier::first();
        $products  = Product::all()->values();

        // PO 1 (2 months ago — Received)
        $po1 = $purchaseService->createPurchase([
            'supplier_id'   => $supplier->id,
            'purchase_date' => now()->subMonths(2)->toDateString(),
            'items' => [
                ['product_id' => $products[0]->id, 'qty' => 3, 'unit_cost' => 6500000],
                ['product_id' => $products[4]->id, 'qty' => 5, 'unit_cost' => 1800000],
            ],
        ]);
        $purchaseService->receivePurchase($po1);

        // PO 2 (1 month ago — Received)
        $supplier2 = Supplier::skip(1)->first() ?? $supplier;
        $po2 = $purchaseService->createPurchase([
            'supplier_id'   => $supplier2->id,
            'purchase_date' => now()->subMonths(1)->toDateString(),
            'items' => [
                ['product_id' => $products[1]->id, 'qty' => 5, 'unit_cost' => 5200000],
                ['product_id' => $products[9]->id, 'qty' => 30, 'unit_cost' => 35000],
            ],
        ]);
        $purchaseService->receivePurchase($po2);

        // PO 3 (current month — Ordered only)
        $purchaseService->createPurchase([
            'supplier_id'   => $supplier->id,
            'purchase_date' => now()->subDays(5)->toDateString(),
            'items' => [
                ['product_id' => $products[2]->id, 'qty' => 2, 'unit_cost' => 7000000],
            ],
        ]);

        $this->command->info('✓ Pembelian (3 PO) di-seed');
    }

    private function seedSales(): void
    {
        $saleService = app(SaleService::class);
        $customers   = Customer::take(5)->get();
        $products    = Product::all();

        $salesData = [
            ['customer_id'=>$customers[0]->id,'sale_date'=>now()->subDays(25)->toDateString(),'items'=>[
                ['product_id'=>$products[0]->id,'qty'=>1,'unit_price'=>7800000],
                ['product_id'=>$products[9]->id,'qty'=>5,'unit_price'=>55000],
            ],'payment_method'=>'Cash'],
            ['customer_id'=>$customers[1]->id,'sale_date'=>now()->subDays(20)->toDateString(),'items'=>[
                ['product_id'=>$products[4]->id,'qty'=>1,'unit_price'=>2200000],
                ['product_id'=>$products[10]->id,'qty'=>3,'unit_price'=>70000],
            ],'payment_method'=>'Transfer'],
            ['customer_id'=>null,'sale_date'=>now()->subDays(18)->toDateString(),'items'=>[
                ['product_id'=>$products[13]->id,'qty'=>1,'unit_price'=>550000],
                ['product_id'=>$products[16]->id,'qty'=>2,'unit_price'=>95000],
            ],'payment_method'=>'Cash','notes'=>'Walk-in customer'],
            ['customer_id'=>$customers[2]->id,'sale_date'=>now()->subDays(12)->toDateString(),'items'=>[
                ['product_id'=>$products[1]->id,'qty'=>1,'unit_price'=>6500000],
                ['product_id'=>$products[3]->id,'qty'=>1,'unit_price'=>420000],
            ],'payment_method'=>'Transfer'],
            ['customer_id'=>$customers[3]->id,'sale_date'=>now()->subDays(8)->toDateString(),'items'=>[
                ['product_id'=>$products[7]->id,'qty'=>1,'unit_price'=>10000000],
            ],'payment_method'=>'Transfer'],
            ['customer_id'=>null,'sale_date'=>now()->subDays(5)->toDateString(),'items'=>[
                ['product_id'=>$products[9]->id,'qty'=>10,'unit_price'=>55000],
                ['product_id'=>$products[10]->id,'qty'=>5,'unit_price'=>70000],
            ],'payment_method'=>'Cash'],
            ['customer_id'=>$customers[4]->id,'sale_date'=>now()->subDays(3)->toDateString(),'items'=>[
                ['product_id'=>$products[3]->id,'qty'=>1,'unit_price'=>7200000],
                ['product_id'=>$products[14]->id,'qty'=>2,'unit_price'=>550000],
            ],'payment_method'=>'Cash','discount'=>100000],
            ['customer_id'=>$customers[2]->id,'sale_date'=>now()->subDays(1)->toDateString(),'items'=>[
                ['product_id'=>$products[5]->id,'qty'=>1,'unit_price'=>1900000],
            ],'payment_method'=>'Cash'],
        ];

        foreach ($salesData as $data) {
            try {
                $saleService->createSale($data);
            } catch (\Exception $e) {
                $this->command->warn("Sale skipped: " . $e->getMessage());
            }
        }
        $this->command->info('✓ Penjualan (8 transaksi) di-seed');
    }

    private function seedServiceOrders(): void
    {
        $soService = app(ServiceOrderService::class);
        $customers = Customer::all()->values();

        $orderDefs = [
            ['cIdx'=>0,'device_type'=>'Laptop','brand'=>'HP','problem_description'=>'Laptop tidak mau menyala, layar hitam','status'=>'Completed','service_cost'=>250000,'diagnosis'=>'Adaptor rusak, sudah diganti'],
            ['cIdx'=>1,'device_type'=>'Printer','brand'=>'Epson','problem_description'=>'Printer tidak bisa print, ada pesan error','status'=>'Completed','service_cost'=>150000,'diagnosis'=>'Head printer kotor, sudah dibersihkan'],
            ['cIdx'=>2,'device_type'=>'Laptop','brand'=>'ASUS','problem_description'=>'Kipas berisik dan laptop cepat panas','status'=>'Done','service_cost'=>175000,'diagnosis'=>'Thermal paste habis dan kipas berdebu'],
            ['cIdx'=>3,'device_type'=>'CPU','brand'=>'Acer','problem_description'=>'PC sering restart sendiri','status'=>'InProgress','diagnosis'=>'Sedang diagnosa RAM dan power supply'],
            ['cIdx'=>4,'device_type'=>'All-in-One','brand'=>'Canon','problem_description'=>'Scanner tidak terdeteksi','status'=>'Received'],
            ['cIdx'=>5,'device_type'=>'Laptop','brand'=>'Dell','problem_description'=>'Keyboard beberapa tombol tidak berfungsi','status'=>'Received'],
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
        $this->command->info('✓ Order Servis (6) di-seed');
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
}
