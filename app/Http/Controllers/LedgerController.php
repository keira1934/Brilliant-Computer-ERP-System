<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function coa()
    {
        $accounts = ChartOfAccount::orderBy('code')->get()->groupBy('type');
        return view('ledger.coa', compact('accounts'));
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
            ->select('journal_entry_lines.*', 'journal_entries.entry_date', 'journal_entries.description as entry_desc');

        if ($accountId) {
            $query->where('journal_entry_lines.account_id', $accountId);
        }

        $query->orderBy('journal_entries.entry_date')->orderBy('journal_entries.id');

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
            ->latest('entry_date');

        $entries = $isPrint ? $query->get() : $query->paginate(20)->withQueryString();

        return view('ledger.journal', compact('entries', 'from', 'to', 'isPrint'));
    }
}
