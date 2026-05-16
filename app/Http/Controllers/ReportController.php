<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                $account->period_balance = $this->accountBalanceExcludingClosing($account, $from, $to);
                return $account;
            })->filter(fn($a) => $a->period_balance > 0 || true); // show all

        $expenseAccounts = ChartOfAccount::where('type', 'expense')
            ->orderBy('code')->get()
            ->map(function ($account) use ($from, $to) {
                $account->period_balance = $this->accountBalanceExcludingClosing($account, $from, $to);
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

    private function accountBalanceExcludingClosing(ChartOfAccount $account, ?string $from, ?string $to): float
    {
        $q = $account->journalLines()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->whereIn('journal_entries.status', JournalEntry::ledgerStatuses())
            ->where(function ($query) {
                $query->whereNull('journal_entries.reference_type')
                    ->orWhere('journal_entries.reference_type', '!=', 'PeriodClosing');
            });

        if ($from) $q->where('journal_entries.entry_date', '>=', $from);
        if ($to) $q->where('journal_entries.entry_date', '<=', $to);

        $debit = (float) (clone $q)->sum('journal_entry_lines.debit');
        $credit = (float) (clone $q)->sum('journal_entry_lines.credit');

        return $account->normal_balance === 'debit' ? ($debit - $credit) : ($credit - $debit);
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
                ->whereIn('journal_entries.status', JournalEntry::ledgerStatuses())
                ->whereBetween('journal_entries.entry_date', [$from, $to])
                ->where('journal_entry_lines.debit', '>', 0)
                ->select(
                    'journal_entry_lines.*',
                    'journal_entries.entry_date',
                    'journal_entries.created_at as entry_created_at',
                    'journal_entries.description as entry_desc'
                )
                ->orderByDesc('journal_entries.entry_date')
                ->orderByDesc('journal_entries.id')
                ->get();

            $outRows = JournalEntryLine::join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->where('journal_entry_lines.account_id', $account->id)
                ->whereIn('journal_entries.status', JournalEntry::ledgerStatuses())
                ->whereBetween('journal_entries.entry_date', [$from, $to])
                ->where('journal_entry_lines.credit', '>', 0)
                ->select(
                    'journal_entry_lines.*',
                    'journal_entries.entry_date',
                    'journal_entries.created_at as entry_created_at',
                    'journal_entries.description as entry_desc'
                )
                ->orderByDesc('journal_entries.entry_date')
                ->orderByDesc('journal_entries.id')
                ->get();

            $cashIn  = $cashIn->merge($inRows);
            $cashOut = $cashOut->merge($outRows);
        }

        $cashIn = $cashIn
            ->sortByDesc(fn($row) => sprintf('%s-%010d-%010d', $row->entry_date, $row->journal_entry_id, $row->id))
            ->values();
        $cashOut = $cashOut
            ->sortByDesc(fn($row) => sprintf('%s-%010d-%010d', $row->entry_date, $row->journal_entry_id, $row->id))
            ->values();

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
            ->orderByDesc('entry_date')
            ->orderByDesc('id');

        $entries = $isPrint ? $query->get() : $query->paginate(20)->withQueryString();

        return view('reports.transactions', compact('from', 'to', 'entries', 'isPrint'));
    }

    /**
     * Trial Balance — sums all account debits and credits from posted journals.
     * If total debits ≠ total credits, an accounting error warning is shown.
     */
    public function trialBalance(Request $request)
    {
        $from    = $request->from ?? now()->startOfYear()->toDateString();
        $to      = $request->to   ?? now()->toDateString();
        $isPrint = (bool) $request->print;

        $accounts = ChartOfAccount::where('is_active', true)
            ->orderBy('code')->get()
            ->map(function ($account) use ($from, $to) {
                // Get raw debit and credit totals from posted journals
                $q = $account->journalLines()
                    ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                    ->whereIn('journal_entries.status', JournalEntry::ledgerStatuses())
                    ->where('journal_entries.entry_date', '>=', $from)
                    ->where('journal_entries.entry_date', '<=', $to);

                $account->total_debit  = (float) (clone $q)->sum('journal_entry_lines.debit');
                $account->total_credit = (float) (clone $q)->sum('journal_entry_lines.credit');
                $account->balance      = $account->getBalance($from, $to);

                // For trial balance display: show debit or credit balance
                if ($account->normal_balance === 'debit') {
                    $net = $account->total_debit - $account->total_credit;
                    $account->tb_debit  = $net >= 0 ? $net : 0;
                    $account->tb_credit = $net < 0 ? abs($net) : 0;
                } else {
                    $net = $account->total_credit - $account->total_debit;
                    $account->tb_credit = $net >= 0 ? $net : 0;
                    $account->tb_debit  = $net < 0 ? abs($net) : 0;
                }

                return $account;
            })
            ->filter(fn($a) => $a->total_debit > 0 || $a->total_credit > 0);

        $totalDebit  = $accounts->sum('tb_debit');
        $totalCredit = $accounts->sum('tb_credit');
        $isBalanced  = round($totalDebit, 2) === round($totalCredit, 2);

        return view('reports.trial-balance', compact(
            'from', 'to', 'accounts', 'totalDebit', 'totalCredit', 'isBalanced', 'isPrint'
        ));
    }

    /**
     * Balance Sheet — Assets = Liabilities + Equity (must balance).
     * Dynamically generated from posted journal data.
     */
    public function balanceSheet(Request $request)
    {
        $asOf    = $request->as_of ?? now()->toDateString();
        $isPrint = (bool) $request->print;

        $assetAccounts = ChartOfAccount::where('type', 'asset')
            ->where('is_active', true)->orderBy('code')->get()
            ->map(function ($account) use ($asOf) {
                $account->balance = $account->getBalance(null, $asOf);
                if ($account->normal_balance === 'credit') {
                    $account->balance = -abs($account->balance);
                }
                return $account;
            });

        $liabilityAccounts = ChartOfAccount::where('type', 'liability')
            ->where('is_active', true)->orderBy('code')->get()
            ->map(function ($account) use ($asOf) {
                $account->balance = $account->getBalance(null, $asOf);
                return $account;
            });

        $equityAccounts = ChartOfAccount::where('type', 'equity')
            ->where('is_active', true)->orderBy('code')->get()
            ->map(function ($account) use ($asOf) {
                $account->balance = $account->getBalance(null, $asOf);
                return $account;
            });

        // Calculate retained earnings (revenue - expenses for all time up to asOf)
        $totalRevenue  = ChartOfAccount::where('type', 'revenue')->get()
            ->sum(fn($a) => $a->getBalance(null, $asOf));
        $totalExpenses = ChartOfAccount::where('type', 'expense')->get()
            ->sum(fn($a) => $a->getBalance(null, $asOf));
        $currentEarnings = $totalRevenue - $totalExpenses;

        $totalAssets      = $assetAccounts->sum('balance');
        $totalLiabilities = $liabilityAccounts->sum('balance');
        $totalEquity      = $equityAccounts->sum('balance') + $currentEarnings;
        $isBalanced       = round($totalAssets, 2) === round($totalLiabilities + $totalEquity, 2);

        return view('reports.balance-sheet', compact(
            'asOf', 'assetAccounts', 'liabilityAccounts', 'equityAccounts',
            'currentEarnings', 'totalAssets', 'totalLiabilities', 'totalEquity',
            'totalRevenue', 'totalExpenses', 'isBalanced', 'isPrint'
        ));
    }
}
