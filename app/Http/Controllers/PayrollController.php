<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use App\Services\PayrollService;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function __construct(private PayrollService $payrollService) {}

    public function index(Request $request)
    {
        $month = (int) ($request->month ?? now()->month);
        $year  = (int) ($request->year  ?? now()->year);

        $payrolls  = Payroll::with('employee')
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->orderBy('id')
            ->get();

        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        $totalPaid = $payrolls->where('status', 'Paid')->sum('net_salary');

        return view('payroll.index', compact('payrolls', 'employees', 'month', 'year', 'totalPaid'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'month'       => 'required|integer|min:1|max:12',
            'year'        => 'required|integer|min:2000|max:2099',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $month = (int) $request->month;
        $year  = (int) $request->year;

        try {
            if ($request->employee_id) {
                $this->payrollService->generatePayroll($request->employee_id, $month, $year);
                $msg = 'Payroll generated and journal posted successfully.';
            } else {
                $result  = $this->payrollService->generateAllPayroll($month, $year);
                $created = count($result['created']);
                $failed  = count($result['errors']);
                $msg     = "{$created} payroll record(s) generated successfully.";
                if ($failed > 0) {
                    $msg .= " {$failed} skipped (already exists): " . implode('; ', $result['errors']);
                }
            }
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('payroll.index', ['month' => $month, 'year' => $year])
            ->with('success', $msg);
    }
}
