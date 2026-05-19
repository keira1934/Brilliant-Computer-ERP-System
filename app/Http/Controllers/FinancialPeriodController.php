<?php

namespace App\Http\Controllers;

use App\Models\ApInvoice;
use App\Models\ArInvoice;
use App\Models\ChartOfAccount;
use App\Models\ClosingEntry;
use App\Models\FinancialPeriod;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Product;
use App\Services\AccountingService;
use App\Services\AuditService;
use App\Services\PeriodClosingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialPeriodController extends Controller
{
    public function __construct(
        private AuditService $auditService,
        private PeriodClosingService $periodClosingService,
        private AccountingService $accounting
    ) {}

    public function index()
    {
        // Group periods by year so the view can render year-folder accordion
        $periodsByYear = FinancialPeriod::orderByDesc('start_date')
            ->get()
            ->groupBy(fn($p) => $p->start_date->format('Y'))
            ->sortKeysDesc();

        return view('financial-periods.index', compact('periodsByYear'));
    }

    public function show(FinancialPeriod $period)
    {
        $from = $period->start_date->toDateString();
        $to = $period->end_date->toDateString();

        $revenue = ChartOfAccount::where('type', 'revenue')->get()
            ->sum(fn($account) => $this->accounting->getAccountMovement($account, $from, $to, true)['signed']);
        $expenses = ChartOfAccount::where('type', 'expense')->get()
            ->sum(fn($account) => $this->accounting->getAccountMovement($account, $from, $to, true)['signed']);
        $netProfit = $revenue - $expenses;

        $trialAccounts = $this->accounting->getTrialBalance($from, $to);
        $trialDebit = $trialAccounts->sum('tb_debit');
        $trialCredit = $trialAccounts->sum('tb_credit');
        $trialBalanced = round($trialDebit, 2) === round($trialCredit, 2);

        $balanceSheet = $this->accounting->getBalanceSheet($to);
        $openingCash = $this->accounting->getCashBalance($period->start_date->copy()->subDay()->toDateString());
        $endingCash = $this->accounting->getCashBalance($to);
        $netCash = $endingCash - $openingCash;

        $journalCount = JournalEntry::whereBetween('entry_date', [$from, $to])->count();
        $transactionCount = JournalEntryLine::join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->whereBetween('journal_entries.entry_date', [$from, $to])
            ->count();

        $arBalance = ChartOfAccount::where('code', '1-1200')->first()?->getBalance(null, $to) ?? 0;
        $apBalance = ChartOfAccount::where('code', '2-1000')->first()?->getBalance(null, $to) ?? 0;
        $inventoryValuation = Product::query()->sum(DB::raw('stock * cost_price'));

        $recentEntries = JournalEntry::with('lines.account')
            ->whereBetween('entry_date', [$from, $to])
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $openAr = ArInvoice::whereIn('status', ['Open', 'Partially Paid'])->sum(DB::raw('total - paid_amount'));
        $openAp = ApInvoice::whereIn('status', ['Open', 'Partially Paid'])->sum(DB::raw('total - paid_amount'));

        return view('financial-periods.show', compact(
            'period', 'revenue', 'expenses', 'netProfit', 'trialDebit',
            'trialCredit', 'trialBalanced', 'balanceSheet', 'openingCash',
            'netCash', 'endingCash', 'journalCount', 'transactionCount',
            'arBalance', 'apBalance', 'inventoryValuation', 'recentEntries',
            'openAr', 'openAp'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        // Check for overlapping periods
        $overlap = FinancialPeriod::where(function ($q) use ($data) {
            $q->whereBetween('start_date', [$data['start_date'], $data['end_date']])
              ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']])
              ->orWhere(function ($q2) use ($data) {
                  $q2->where('start_date', '<=', $data['start_date'])
                     ->where('end_date', '>=', $data['end_date']);
              });
        })->exists();

        if ($overlap) {
            return back()->with('error', 'This period overlaps with an existing financial period.')->withInput();
        }

        $period = FinancialPeriod::create($data);

        $this->auditService->logCreation('financial_period', $period, "Financial period '{$period->name}' created");

        return redirect()->route('financial-periods.index')
            ->with('success', "Financial period '{$period->name}' created successfully.");
    }

    public function close(FinancialPeriod $period)
    {
        if ($period->isClosed()) {
            return back()->with('error', 'This period is already closed.');
        }

        $totals = JournalEntryLine::join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->whereIn('journal_entries.status', JournalEntry::ledgerStatuses())
            ->whereBetween('journal_entries.entry_date', [
                $period->start_date->toDateString(),
                $period->end_date->toDateString(),
            ])
            ->selectRaw('COALESCE(SUM(journal_entry_lines.debit), 0) as debit_total, COALESCE(SUM(journal_entry_lines.credit), 0) as credit_total')
            ->first();

        if (round((float) $totals->debit_total, 2) !== round((float) $totals->credit_total, 2)) {
            return back()->with('error', 'Cannot close this period because the trial balance is not balanced.');
        }

        try {
            $this->periodClosingService->closeNominalAccounts($period);

            $period->update([
                'status'    => 'closed',
                'closed_by' => auth()->id(),
                'closed_at' => now(),
            ]);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $this->auditService->logStatusChange('financial_period', $period, 'close', "Period '{$period->name}' closed");

        return back()->with('success', "Financial period '{$period->name}' has been closed. No further transactions can be posted to this period.");
    }

    public function reopen(FinancialPeriod $period)
    {
        if ($period->isOpen()) {
            return back()->with('error', 'This period is already open.');
        }

        if (!auth()->user()?->isManager()) {
            abort(403, 'Only managers can reopen a closed financial period.');
        }

        $closing = ClosingEntry::with('journalEntry')->where('financial_period_id', $period->id)->first();
        if ($closing?->journalEntry) {
            $closing->journalEntry->update(['status' => JournalEntry::STATUS_CANCELLED]);
            $closing->delete();
        }

        $period->update([
            'status'    => 'open',
            'closed_by' => null,
            'closed_at' => null,
        ]);

        $this->auditService->logStatusChange('financial_period', $period, 'reopen', "Period '{$period->name}' reopened");

        return back()->with('success', "Financial period '{$period->name}' has been reopened.");
    }
}
