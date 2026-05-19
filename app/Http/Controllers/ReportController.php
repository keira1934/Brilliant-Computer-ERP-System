<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function __construct(private AccountingService $accounting) {}

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
            });

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
        return $this->accounting->getAccountMovement($account, $from, $to, true)['signed'];
    }

    public function cashFlow(Request $request)
    {
        $from    = $request->from ?? now()->startOfYear()->toDateString();
        $to      = $request->to   ?? now()->toDateString();
        $isPrint = (bool) $request->print;

        $liquidAccounts = ChartOfAccount::whereIn('code', ['1-1000', '1-1100'])->get();
        $cashIn  = collect();
        $cashOut = collect();

        foreach ($liquidAccounts as $account) {
            $base = JournalEntryLine::join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->where('journal_entry_lines.account_id', $account->id)
                ->whereIn('journal_entries.status', JournalEntry::ledgerStatuses())
                ->whereBetween('journal_entries.entry_date', [$from, $to])
                ->select(
                    'journal_entry_lines.*',
                    'journal_entries.entry_date',
                    'journal_entries.created_at as entry_created_at',
                    'journal_entries.description as entry_desc',
                    'journal_entries.reference_type'
                )
                ->orderByDesc('journal_entries.entry_date')
                ->orderByDesc('journal_entries.id');

            $cashIn = $cashIn->merge((clone $base)->where('journal_entry_lines.debit', '>', 0)->get());
            $cashOut = $cashOut->merge((clone $base)->where('journal_entry_lines.credit', '>', 0)->get());
        }

        $cashIn = $cashIn
            ->sortByDesc(fn($row) => sprintf('%s-%010d-%010d', $row->entry_date, $row->journal_entry_id, $row->id))
            ->values();
        $cashOut = $cashOut
            ->sortByDesc(fn($row) => sprintf('%s-%010d-%010d', $row->entry_date, $row->journal_entry_id, $row->id))
            ->values();

        $openingCash = $this->accounting->getCashBalance(Carbon::parse($from)->subDay()->toDateString());
        $totalIn  = $cashIn->sum('debit');
        $totalOut = $cashOut->sum('credit');
        $netCash  = $totalIn - $totalOut;
        $endingCash = $openingCash + $netCash;
        $balanceSheetCash = $this->accounting->getCashBalance($to);

        return view('reports.cash-flow', compact(
            'from', 'to', 'cashIn', 'cashOut', 'openingCash', 'totalIn',
            'totalOut', 'netCash', 'endingCash', 'balanceSheetCash', 'isPrint'
        ));
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

    public function trialBalance(Request $request)
    {
        $from    = $request->from ?? now()->startOfYear()->toDateString();
        $to      = $request->to   ?? now()->toDateString();
        $isPrint = (bool) $request->print;

        $accounts = $this->accounting->getTrialBalance($from, $to);
        $totalDebit  = $accounts->sum('tb_debit');
        $totalCredit = $accounts->sum('tb_credit');
        $isBalanced  = round($totalDebit, 2) === round($totalCredit, 2);

        return view('reports.trial-balance', compact(
            'from', 'to', 'accounts', 'totalDebit', 'totalCredit', 'isBalanced', 'isPrint'
        ));
    }

    public function balanceSheet(Request $request)
    {
        $asOf    = $request->as_of ?? now()->toDateString();
        $isPrint = (bool) $request->print;

        extract($this->accounting->getBalanceSheet($asOf));

        return view('reports.balance-sheet', compact(
            'asOf', 'assetAccounts', 'liabilityAccounts', 'equityAccounts',
            'currentEarnings', 'totalAssets', 'totalLiabilities', 'totalEquity',
            'totalRevenue', 'totalExpenses', 'isBalanced', 'isPrint'
        ));
    }
}
