<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LedgerController extends Controller
{
    public function __construct(private AccountingService $accountingService) {}
    public function coa()
    {
        $accounts = ChartOfAccount::orderBy('code')->get()->groupBy('type');
        return view('ledger.coa', compact('accounts'));
    }

    public function createAccount()
    {
        return view('ledger.coa-create');
    }

    public function storeAccount(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:chart_of_accounts,code',
            'name' => 'required|string|max:120',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'normal_balance' => 'required|in:debit,credit',
            'description' => 'nullable|string|max:255',
        ]);

        $data['is_active'] = true;
        ChartOfAccount::create($data);

        return redirect()->route('ledger.coa')->with('success', 'Chart of account added successfully.');
    }

    /**
     * Update the opening / beginning balance for a single COA account.
     * This does NOT post a journal entry — the opening balance is stored directly
     * on the account and factored into getBalance() for cumulative reports.
     */
    public function updateOpeningBalance(Request $request, ChartOfAccount $account)
    {
        $data = $request->validate([
            'opening_balance'      => 'required|numeric',
            'opening_balance_date' => 'nullable|date',
        ]);

        // Prevent duplicate: if an opening balance already exists and is being changed,
        // just update the stored value — no journal entry is created.
        try {
            $account->update([
                'opening_balance'      => (float) $data['opening_balance'],
                'opening_balance_date' => $data['opening_balance_date'] ?? null,
            ]);

            $this->accountingService->syncOpeningBalanceJournal();
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        $label = number_format((float) $data['opening_balance'], 2);
        return redirect()->route('ledger.coa')
            ->with('success', "Opening balance for \"{$account->name}\" set to Rp {$label}.");
    }

    public function general(Request $request)
    {
        $from      = $request->from ?? now()->startOfYear()->toDateString();
        $to        = $request->to   ?? now()->toDateString();
        $accountId = $request->account_id;
        $accounts  = ChartOfAccount::orderBy('code')->get();
        $isPrint   = (bool) $request->print;

        $query = JournalEntryLine::with(['journalEntry', 'account'])
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entries.entry_date', '>=', $from)
            ->where('journal_entries.entry_date', '<=', $to)
            ->whereIn('journal_entries.status', JournalEntry::ledgerStatuses())
            ->select(
                'journal_entry_lines.*',
                'journal_entries.entry_date',
                'journal_entries.created_at as entry_created_at',
                'journal_entries.description as entry_desc'
            );

        if ($accountId) {
            $query->where('journal_entry_lines.account_id', $accountId);
        }

        $query->orderByDesc('journal_entries.entry_date')->orderByDesc('journal_entries.id');

        $lines           = $isPrint ? $query->get() : $query->paginate(30)->withQueryString();
        $selectedAccount = $accountId ? ChartOfAccount::find($accountId) : null;
        $openingBalance  = null;
        $endingBalance   = null;

        if ($selectedAccount) {
            $openingBalance = $selectedAccount->getBalance(null, Carbon::parse($from)->subDay()->toDateString());
            $endingBalance = $selectedAccount->getBalance(null, $to);
            $running = $openingBalance;

            $orderedLines = ($isPrint ? $lines : $lines->getCollection())
                ->sortBy(fn($line) => sprintf('%s-%010d-%010d', $line->entry_date, $line->journal_entry_id, $line->id));

            foreach ($orderedLines as $line) {
                $delta = $selectedAccount->normal_balance === 'debit'
                    ? ((float) $line->debit - (float) $line->credit)
                    : ((float) $line->credit - (float) $line->debit);
                $running += $delta;
                $line->running_balance = $running;
            }
        }

        return view('ledger.general', compact('lines', 'accounts', 'from', 'to', 'accountId', 'selectedAccount', 'openingBalance', 'endingBalance', 'isPrint'));
    }

    public function journal(Request $request)
    {
        $from    = $request->from ?? now()->startOfYear()->toDateString();
        $to      = $request->to   ?? now()->toDateString();
        $isPrint = (bool) $request->print;

        $query = JournalEntry::with('lines.account')
            ->whereBetween('entry_date', [$from, $to])
            ->orderByDesc('entry_date')
            ->orderByDesc('id');

        $entries = $isPrint ? $query->get() : $query->paginate(20)->withQueryString();

        return view('ledger.journal', compact('entries', 'from', 'to', 'isPrint'));
    }

    public function reverseJournal(Request $request, JournalEntry $journalEntry)
    {
        if (!auth()->user()?->isManager()) {
            abort(403, 'Journal reversal requires manager authorization.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            $reversal = $this->accountingService->reverseJournal($journalEntry, $request->reason);
            return back()->with('success', "Journal {$journalEntry->journal_number} reversed. Reversal entry: {$reversal->journal_number}");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
