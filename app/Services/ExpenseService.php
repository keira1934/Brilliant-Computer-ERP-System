<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    public function __construct(
        private AccountingService $accounting,
        private AuditService $auditService
    ) {}

    /** Map category to COA code */
    private static array $categoryAccountMap = [
        'Electricity'  => '5-3000',
        'Maintenance'  => '5-4000',
        'Internet'     => '5-5000',
        'Rent'         => '5-5000',
        'Other'        => '5-5000',
    ];

    public function recordExpense(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            $accountCode = self::$categoryAccountMap[$data['category']] ?? '5-5000';

            // If account_id not explicitly provided, look it up by code
            if (empty($data['account_id'])) {
                $account = ChartOfAccount::where('code', $accountCode)->firstOrFail();
                $data['account_id'] = $account->id;
            } else {
                $account = ChartOfAccount::findOrFail($data['account_id']);
                $accountCode = $account->code;
            }

            $expense = Expense::create([
                'expense_date' => $data['expense_date'],
                'category'     => $data['category'],
                'description'  => $data['description'] ?? null,
                'amount'       => $data['amount'],
                'account_id'   => $data['account_id'],
                'reference'    => $data['reference'] ?? null,
            ]);

            $this->accounting->postJournal(
                $data['expense_date'],
                "Pengeluaran: {$data['category']}" . ($data['description'] ? " - {$data['description']}" : ''),
                'Expense',
                $expense->id,
                [
                    ['code' => $accountCode, 'debit' => $data['amount'], 'credit' => 0,               'description' => $data['description'] ?? $data['category']],
                    ['code' => '1-1000',     'debit' => 0,               'credit' => $data['amount'], 'description' => 'Pengeluaran kas operasional'],
                ]
            );

            $this->auditService->logCreation('expense', $expense, "Expense {$expense->category} recorded and posted");

            return $expense;
        });
    }
}
