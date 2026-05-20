<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\ClosingEntry;
use App\Models\FinancialPeriod;
use Illuminate\Support\Facades\DB;

class PeriodClosingService
{
    public function __construct(
        private AccountingService $accounting,
        private AuditService $auditService
    ) {}

    public function closeNominalAccounts(FinancialPeriod $period): ?ClosingEntry
    {
        return DB::transaction(function () use ($period) {
            $existing = ClosingEntry::where('financial_period_id', $period->id)->lockForUpdate()->first();
            if ($existing) {
                return $existing;
            }

            $lines = [];
            $totalRevenue = 0;
            $totalExpenses = 0;

            $revenueAccounts = ChartOfAccount::where('type', 'revenue')->where('is_active', true)->orderBy('code')->get();
            foreach ($revenueAccounts as $account) {
                $balance = round($account->getBalance($period->start_date->toDateString(), $period->end_date->toDateString()), 2);
                if ($balance <= 0) continue;

                $totalRevenue += $balance;
                $lines[] = [
                    'code' => $account->code,
                    'debit' => $balance,
                    'credit' => 0,
                    'description' => "Close revenue account {$account->code}",
                ];
            }

            $expenseAccounts = ChartOfAccount::where('type', 'expense')->where('is_active', true)->orderBy('code')->get();
            foreach ($expenseAccounts as $account) {
                $balance = round($account->getBalance($period->start_date->toDateString(), $period->end_date->toDateString()), 2);
                if ($balance <= 0) continue;

                $totalExpenses += $balance;
                $lines[] = [
                    'code' => $account->code,
                    'debit' => 0,
                    'credit' => $balance,
                    'description' => "Close expense account {$account->code}",
                ];
            }

            $netIncome = round($totalRevenue - $totalExpenses, 2);
            if (round($totalRevenue + $totalExpenses, 2) <= 0) {
                return null;
            }

            if ($netIncome > 0) {
                $lines[] = [
                    'code' => '3-2000',
                    'debit' => 0,
                    'credit' => $netIncome,
                    'description' => 'Transfer net income to retained earnings',
                ];
            } elseif ($netIncome < 0) {
                $lines[] = [
                    'code' => '3-2000',
                    'debit' => abs($netIncome),
                    'credit' => 0,
                    'description' => 'Transfer net loss to retained earnings',
                ];
            }

            $journal = $this->accounting->postJournal(
                $period->end_date->toDateString(),
                "Closing entries - {$period->name}",
                'PeriodClosing',
                $period->id,
                $lines
            );

            $closing = ClosingEntry::create([
                'financial_period_id' => $period->id,
                'journal_entry_id' => $journal->id,
                'closing_date' => $period->end_date,
                'revenue_closed' => $totalRevenue,
                'expenses_closed' => $totalExpenses,
                'net_income' => $netIncome,
            ]);

            $this->auditService->logCreation('period_closing', $closing, "Closing entry posted for {$period->name}");

            return $closing;
        });
    }
}
