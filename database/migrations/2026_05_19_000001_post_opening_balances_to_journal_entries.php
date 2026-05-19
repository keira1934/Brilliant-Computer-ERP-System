<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('journal_entries')->where('reference_type', 'OpeningBalance')->exists()) {
            return;
        }

        $accounts = DB::table('chart_of_accounts')
            ->where('is_active', true)
            ->where('opening_balance', '!=', 0)
            ->orderBy('code')
            ->get();

        if ($accounts->isEmpty()) {
            return;
        }

        $openingDate = $accounts->pluck('opening_balance_date')->filter()->min()
            ?: now()->startOfYear()->toDateString();

        $journalId = DB::table('journal_entries')->insertGetId([
            'journal_number' => 'JRN-' . str_replace('-', '', substr($openingDate, 0, 7)) . '-OPENING',
            'entry_date' => $openingDate,
            'description' => 'SYSTEM OPENING ENTRY - OPENING BALANCE',
            'reference_type' => 'OpeningBalance',
            'reference_id' => null,
            'status' => 'posted',
            'posted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $amount = round(abs((float) $account->opening_balance), 2);
            $isNormalSide = (float) $account->opening_balance >= 0;
            $debit = ($account->normal_balance === 'debit') === $isNormalSide ? $amount : 0;
            $credit = $debit > 0 ? 0 : $amount;
            $totalDebit += $debit;
            $totalCredit += $credit;

            DB::table('journal_entry_lines')->insert([
                'journal_entry_id' => $journalId,
                'account_id' => $account->id,
                'description' => "Opening balance - {$account->code} {$account->name}",
                'debit' => $debit,
                'credit' => $credit,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $difference = round($totalDebit - $totalCredit, 2);
        if ($difference != 0.0) {
            $equity = DB::table('chart_of_accounts')->where('code', '3-1000')->first();
            if ($equity) {
                DB::table('journal_entry_lines')->insert([
                    'journal_entry_id' => $journalId,
                    'account_id' => $equity->id,
                    'description' => 'Opening balance equity plug',
                    'debit' => $difference < 0 ? abs($difference) : 0,
                    'credit' => $difference > 0 ? $difference : 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $ids = DB::table('journal_entries')->where('reference_type', 'OpeningBalance')->pluck('id');
        DB::table('journal_entry_lines')->whereIn('journal_entry_id', $ids)->delete();
        DB::table('journal_entries')->whereIn('id', $ids)->delete();
    }
};
