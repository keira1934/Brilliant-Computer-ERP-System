<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use App\Services\AccountingService;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Opening balance date: the day before the 6-month transaction window starts.
        // All transactions are seeded from now()->subMonths(6) onward, so the opening
        // balance is effective one day before that.
        $openingDate = now()->subMonths(6)->subDay()->toDateString();

        $accounts = [
            // ── ASSETS ──────────────────────────────────────────────────────────────
            // Kas: owner deposited Rp 80 juta cash to start the business
            ['code'=>'1-1000','name'=>'Kas',
             'type'=>'asset','normal_balance'=>'debit',
             'description'=>'Uang tunai di tangan',
             'opening_balance'=>80000000, 'opening_balance_date'=>$openingDate],

            // Bank: owner transferred Rp 120 juta to the company bank account
            ['code'=>'1-1100','name'=>'Bank',
             'type'=>'asset','normal_balance'=>'debit',
             'description'=>'Saldo rekening bank perusahaan',
             'opening_balance'=>120000000, 'opening_balance_date'=>$openingDate],

            // Piutang Usaha: no outstanding receivables at opening
            ['code'=>'1-1200','name'=>'Piutang Usaha',
             'type'=>'asset','normal_balance'=>'debit',
             'description'=>'Tagihan kepada customer',
             'opening_balance'=>0, 'opening_balance_date'=>$openingDate],

            // Persediaan Barang: initial stock at cost (2 HP, 2 ASUS, 1 Dell, 2 Acer,
            // 3 Epson L3150, 3 Canon G2010, 3 Epson L1210, 1 CPU Gaming, 2 CPU Office,
            // + accessories) = Rp 88,405,000
            ['code'=>'1-2000','name'=>'Persediaan Barang',
             'type'=>'asset','normal_balance'=>'debit',
             'description'=>'Stok barang dagang (laptop, printer, aksesori)',
             'opening_balance'=>88405000, 'opening_balance_date'=>$openingDate],

            // Peralatan Kantor: service tools, display units, furniture = Rp 35 juta
            ['code'=>'1-3000','name'=>'Peralatan Kantor',
             'type'=>'asset','normal_balance'=>'debit',
             'description'=>'Aset tetap peralatan kantor dan alat servis',
             'opening_balance'=>35000000, 'opening_balance_date'=>$openingDate],

            // Akumulasi Penyusutan: no accumulated depreciation at opening
            ['code'=>'1-3100','name'=>'Akumulasi Penyusutan Peralatan',
             'type'=>'asset','normal_balance'=>'credit',
             'description'=>'Akumulasi penyusutan peralatan',
             'opening_balance'=>0, 'opening_balance_date'=>$openingDate],

            // ── LIABILITIES ─────────────────────────────────────────────────────────
            // No outstanding payables at opening
            ['code'=>'2-1000','name'=>'Hutang Usaha',
             'type'=>'liability','normal_balance'=>'credit',
             'description'=>'Kewajiban kepada supplier',
             'opening_balance'=>0, 'opening_balance_date'=>$openingDate],

            ['code'=>'2-2000','name'=>'Hutang Gaji',
             'type'=>'liability','normal_balance'=>'credit',
             'description'=>'Gaji karyawan yang belum dibayar',
             'opening_balance'=>0, 'opening_balance_date'=>$openingDate],

            ['code'=>'2-3000','name'=>'Hutang Pajak',
             'type'=>'liability','normal_balance'=>'credit',
             'description'=>'Kewajiban pajak',
             'opening_balance'=>0, 'opening_balance_date'=>$openingDate],

            // ── EQUITY ──────────────────────────────────────────────────────────────
            // Modal Pemilik: total owner investment = cash + bank + inventory + equipment
            // = 80,000,000 + 120,000,000 + 88,405,000 + 35,000,000 = 323,405,000
            // The syncOpeningBalanceJournal() will auto-plug any debit/credit difference
            // into this account, so we set it to the exact balancing amount.
            ['code'=>'3-1000','name'=>'Modal Pemilik',
             'type'=>'equity','normal_balance'=>'credit',
             'description'=>'Modal awal pemilik usaha',
             'opening_balance'=>323405000, 'opening_balance_date'=>$openingDate],

            ['code'=>'3-2000','name'=>'Laba Ditahan',
             'type'=>'equity','normal_balance'=>'credit',
             'description'=>'Akumulasi laba yang tidak dibagi',
             'opening_balance'=>0, 'opening_balance_date'=>$openingDate],

            // ── REVENUE ─────────────────────────────────────────────────────────────
            ['code'=>'4-1000','name'=>'Pendapatan Penjualan',
             'type'=>'revenue','normal_balance'=>'credit',
             'description'=>'Pendapatan dari penjualan produk (laptop, printer, aksesori)',
             'opening_balance'=>0, 'opening_balance_date'=>$openingDate],

            ['code'=>'4-2000','name'=>'Pendapatan Jasa Servis',
             'type'=>'revenue','normal_balance'=>'credit',
             'description'=>'Pendapatan dari layanan perbaikan perangkat',
             'opening_balance'=>0, 'opening_balance_date'=>$openingDate],

            // ── EXPENSES ────────────────────────────────────────────────────────────
            ['code'=>'5-1000','name'=>'Harga Pokok Penjualan (HPP)',
             'type'=>'expense','normal_balance'=>'debit',
             'description'=>'Biaya perolehan barang yang terjual',
             'opening_balance'=>0, 'opening_balance_date'=>$openingDate],

            ['code'=>'5-2000','name'=>'Beban Gaji',
             'type'=>'expense','normal_balance'=>'debit',
             'description'=>'Gaji dan upah karyawan',
             'opening_balance'=>0, 'opening_balance_date'=>$openingDate],

            ['code'=>'5-3000','name'=>'Beban Listrik',
             'type'=>'expense','normal_balance'=>'debit',
             'description'=>'Tagihan listrik operasional',
             'opening_balance'=>0, 'opening_balance_date'=>$openingDate],

            ['code'=>'5-4000','name'=>'Beban Pemeliharaan',
             'type'=>'expense','normal_balance'=>'debit',
             'description'=>'Biaya perawatan dan perbaikan aset',
             'opening_balance'=>0, 'opening_balance_date'=>$openingDate],

            ['code'=>'5-5000','name'=>'Beban Operasional Lain',
             'type'=>'expense','normal_balance'=>'debit',
             'description'=>'Biaya operasional lainnya (internet, sewa, dll)',
             'opening_balance'=>0, 'opening_balance_date'=>$openingDate],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::firstOrCreate(['code' => $account['code']], $account);
        }

        // Post the opening balance journal entry so all balances are reflected
        // in the ledger from day one.
        app(AccountingService::class)->syncOpeningBalanceJournal();

        $this->command->info('✓ Chart of Accounts seeded (' . count($accounts) . ' akun) with opening balances');
    }
}
