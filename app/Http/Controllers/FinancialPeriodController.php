<?php

namespace App\Http\Controllers;

use App\Models\FinancialPeriod;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Services\AuditService;
use App\Services\PeriodClosingService;
use Illuminate\Http\Request;

class FinancialPeriodController extends Controller
{
    public function __construct(
        private AuditService $auditService,
        private PeriodClosingService $periodClosingService
    ) {}

    public function index()
    {
        $periods = FinancialPeriod::orderByDesc('start_date')->paginate(20);
        return view('financial-periods.index', compact('periods'));
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

        $period->update([
            'status'    => 'open',
            'closed_by' => null,
            'closed_at' => null,
        ]);

        $this->auditService->logStatusChange('financial_period', $period, 'reopen', "Period '{$period->name}' reopened");

        return back()->with('success', "Financial period '{$period->name}' has been reopened.");
    }
}
