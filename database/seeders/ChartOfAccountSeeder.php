<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // ASSETS
            ['code'=>'1-1000','name'=>'Kas','type'=>'asset','normal_balance'=>'debit','description'=>'Uang tunai di tangan'],
            ['code'=>'1-1100','name'=>'Bank','type'=>'asset','normal_balance'=>'debit','description'=>'Saldo rekening bank perusahaan'],
            ['code'=>'1-1200','name'=>'Piutang Usaha','type'=>'asset','normal_balance'=>'debit','description'=>'Tagihan kepada customer'],
            ['code'=>'1-2000','name'=>'Persediaan Barang','type'=>'asset','normal_balance'=>'debit','description'=>'Stok barang dagang (laptop, printer, aksesori)'],
            ['code'=>'1-3000','name'=>'Peralatan Kantor','type'=>'asset','normal_balance'=>'debit','description'=>'Aset tetap peralatan kantor dan alat servis'],
            ['code'=>'1-3100','name'=>'Akumulasi Penyusutan Peralatan','type'=>'asset','normal_balance'=>'credit','description'=>'Akumulasi penyusutan peralatan'],

            // LIABILITIES
            ['code'=>'2-1000','name'=>'Hutang Usaha','type'=>'liability','normal_balance'=>'credit','description'=>'Kewajiban kepada supplier'],
            ['code'=>'2-2000','name'=>'Hutang Gaji','type'=>'liability','normal_balance'=>'credit','description'=>'Gaji karyawan yang belum dibayar'],
            ['code'=>'2-3000','name'=>'Hutang Pajak','type'=>'liability','normal_balance'=>'credit','description'=>'Kewajiban pajak'],

            // EQUITY
            ['code'=>'3-1000','name'=>'Modal Pemilik','type'=>'equity','normal_balance'=>'credit','description'=>'Modal awal pemilik usaha'],
            ['code'=>'3-2000','name'=>'Laba Ditahan','type'=>'equity','normal_balance'=>'credit','description'=>'Akumulasi laba yang tidak dibagi'],

            // REVENUE
            ['code'=>'4-1000','name'=>'Pendapatan Penjualan','type'=>'revenue','normal_balance'=>'credit','description'=>'Pendapatan dari penjualan produk (laptop, printer, aksesori)'],
            ['code'=>'4-2000','name'=>'Pendapatan Jasa Servis','type'=>'revenue','normal_balance'=>'credit','description'=>'Pendapatan dari layanan perbaikan perangkat'],

            // EXPENSES
            ['code'=>'5-1000','name'=>'Harga Pokok Penjualan (HPP)','type'=>'expense','normal_balance'=>'debit','description'=>'Biaya perolehan barang yang terjual'],
            ['code'=>'5-2000','name'=>'Beban Gaji','type'=>'expense','normal_balance'=>'debit','description'=>'Gaji dan upah karyawan'],
            ['code'=>'5-3000','name'=>'Beban Listrik','type'=>'expense','normal_balance'=>'debit','description'=>'Tagihan listrik operasional'],
            ['code'=>'5-4000','name'=>'Beban Pemeliharaan','type'=>'expense','normal_balance'=>'debit','description'=>'Biaya perawatan dan perbaikan aset'],
            ['code'=>'5-5000','name'=>'Beban Operasional Lain','type'=>'expense','normal_balance'=>'debit','description'=>'Biaya operasional lainnya (internet, sewa, dll)'],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::firstOrCreate(['code' => $account['code']], $account);
        }

        $this->command->info('✓ Chart of Accounts seeded (' . count($accounts) . ' akun)');
    }
}
