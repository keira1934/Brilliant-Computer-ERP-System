<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function incomeStatement(Request $request)
    {
        $from    = $request->from ?? now()->startOfYear()->toDateString();
        $to      = $request->to   ?? now()->toDateString();
        $isPrint = (bool) $request->print;

        $revenueAccounts = ChartOfAccount::where('type', 'revenue')
            ->orderBy('code')->get()
            ->map(function ($account) use ($from, $to) {
                $account->period_balance = $account->getBalance($from, $to);
                return $account;
            })->filter(fn($a) => $a->period_balance > 0 || true); // show all

        $expenseAccounts = ChartOfAccount::where('type', 'expense')
            ->orderBy('code')->get()
            ->map(function ($account) use ($from, $to) {
                $account->period_balance = $account->getBalance($from, $to);
                return $account;
            });

        $totalRevenue  = $revenueAccounts->sum('period_balance');
        $totalExpenses = $expenseAccounts->sum('period_balance');
        $netProfit     = $totalRevenue - $totalExpenses;

        return view('reports.income-statement', compact(
            'from', 'to', 'revenueAccounts', 'expenseAccounts',
            'totalRevenue', 'totalExpenses', 'netProfit', 'isPrint'
        ));
    }

    public function cashFlow(Request $request)
    {
        $from    = $request->from ?? now()->startOfYear()->toDateString();
        $to      = $request->to   ?? now()->toDateString();
        $isPrint = (bool) $request->print;

        // Include BOTH cash (1-1000) and bank (1-1100)
        $liquidAccounts = ChartOfAccount::whereIn('code', ['1-1000', '1-1100'])->get();

        $cashIn  = collect();
        $cashOut = collect();

        foreach ($liquidAccounts as $account) {
            $inRows = JournalEntryLine::join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->where('journal_entry_lines.account_id', $account->id)
                ->whereBetween('journal_entries.entry_date', [$from, $to])
                ->where('journal_entry_lines.debit', '>', 0)
                ->select('journal_entry_lines.*', 'journal_entries.entry_date', 'journal_entries.description as entry_desc')
                ->orderBy('journal_entries.entry_date')
                ->get();

            $outRows = JournalEntryLine::join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->where('journal_entry_lines.account_id', $account->id)
                ->whereBetween('journal_entries.entry_date', [$from, $to])
                ->where('journal_entry_lines.credit', '>', 0)
                ->select('journal_entry_lines.*', 'journal_entries.entry_date', 'journal_entries.description as entry_desc')
                ->orderBy('journal_entries.entry_date')
                ->get();

            $cashIn  = $cashIn->merge($inRows);
            $cashOut = $cashOut->merge($outRows);
        }

        $cashIn  = $cashIn->sortBy('entry_date');
        $cashOut = $cashOut->sortBy('entry_date');

        $totalIn  = $cashIn->sum('debit');
        $totalOut = $cashOut->sum('credit');
        $netCash  = $totalIn - $totalOut;

        return view('reports.cash-flow', compact('from', 'to', 'cashIn', 'cashOut', 'totalIn', 'totalOut', 'netCash', 'isPrint'));
    }

    public function transactions(Request $request)
    {
        $from    = $request->from ?? now()->startOfYear()->toDateString();
        $to      = $request->to   ?? now()->toDateString();
        $isPrint = (bool) $request->print;

        $query = JournalEntry::with('lines.account')
            ->whereBetween('entry_date', [$from, $to])
            ->latest('entry_date');

        $entries = $isPrint ? $query->get() : $query->paginate(20)->withQueryString();

        return view('reports.transactions', compact('from', 'to', 'entries', 'isPrint'));
    }
}
