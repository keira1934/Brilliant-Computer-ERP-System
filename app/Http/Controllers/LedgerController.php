<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Services\AccountingService;
use Illuminate\Http\Request;

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

        return view('ledger.general', compact('lines', 'accounts', 'from', 'to', 'accountId', 'selectedAccount', 'isPrint'));
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
