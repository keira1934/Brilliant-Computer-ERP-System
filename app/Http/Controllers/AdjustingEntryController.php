<?php

namespace App\Http\Controllers;

use App\Models\AdjustingEntry;
use App\Models\ChartOfAccount;
use App\Services\AccountingService;
use App\Services\AuditService;
use Illuminate\Http\Request;

class AdjustingEntryController extends Controller
{
    public function __construct(
        private AccountingService $accountingService,
        private AuditService $auditService
    ) {}

    public function index()
    {
        $entries = AdjustingEntry::with('journalEntry')->latest('adjustment_date')->paginate(20);
        return view('adjusting-entries.index', compact('entries'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();
        return view('adjusting-entries.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'adjustment_date' => 'required|date',
            'adjustment_type' => 'required|in:depreciation,accrual,prepaid,inventory,other',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'debit_account_id' => 'required|exists:chart_of_accounts,id',
            'credit_account_id' => 'required|exists:chart_of_accounts,id|different:debit_account_id',
        ]);

        $debitAccount = ChartOfAccount::findOrFail($data['debit_account_id']);
        $creditAccount = ChartOfAccount::findOrFail($data['credit_account_id']);

        try {
            $journal = $this->accountingService->postJournal(
                $data['adjustment_date'],
                "Adjusting entry - {$data['description']}",
                'AdjustingEntry',
                null,
                [
                    ['code' => $debitAccount->code, 'debit' => $data['amount'], 'credit' => 0, 'description' => $data['description']],
                    ['code' => $creditAccount->code, 'debit' => 0, 'credit' => $data['amount'], 'description' => $data['description']],
                ]
            );

            $entry = AdjustingEntry::create([
                'journal_entry_id' => $journal->id,
                'adjustment_date' => $data['adjustment_date'],
                'adjustment_type' => $data['adjustment_type'],
                'description' => $data['description'],
                'amount' => $data['amount'],
                'status' => 'Posted',
            ]);

            $journal->update(['reference_id' => $entry->id]);
            $this->auditService->logCreation('adjusting_entry', $entry, "Adjusting entry posted: {$entry->description}");

            return redirect()->route('adjusting-entries.index')->with('success', 'Adjusting entry posted successfully.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
