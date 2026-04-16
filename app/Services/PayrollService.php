<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    public function __construct(private AccountingService $accounting) {}

    public function generatePayroll(int $employeeId, int $month, int $year): Payroll
    {
        $employee = Employee::findOrFail($employeeId);

        // Check if already generated for this period
        $existing = Payroll::where('employee_id', $employeeId)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->first();

        if ($existing) {
            throw new \RuntimeException("Payroll for {$employee->name} ({$this->monthName($month)} {$year}) has already been generated.");
        }

        $netSalary = (float) $employee->base_salary;

        // Use end-of-period date as the journal/payment date (proper accounting)
        $periodDate = Carbon::create($year, $month)->endOfMonth()->toDateString();

        return DB::transaction(function () use ($employee, $month, $year, $netSalary, $periodDate) {
            $payroll = Payroll::create([
                'employee_id'  => $employee->id,
                'period_month' => $month,
                'period_year'  => $year,
                'base_salary'  => $employee->base_salary,
                'allowances'   => 0,
                'deductions'   => 0,
                'net_salary'   => $netSalary,
                'paid_at'      => $periodDate,
                'status'       => 'Paid',
            ]);

            $this->accounting->postJournal(
                $periodDate,
                "Salary — {$employee->name} ({$this->monthName($month)} {$year})",
                'Payroll',
                $payroll->id,
                [
                    ['code' => '5-2000', 'debit' => $netSalary, 'credit' => 0,          'description' => "Salary expense: {$employee->name}"],
                    ['code' => '1-1000', 'debit' => 0,          'credit' => $netSalary, 'description' => "Cash paid for salary: {$employee->name}"],
                ]
            );

            return $payroll;
        });
    }

    /** Generate payroll for all active employees in a period */
    public function generateAllPayroll(int $month, int $year): array
    {
        $employees = Employee::where('is_active', true)->get();
        $results   = [];
        $errors    = [];

        foreach ($employees as $employee) {
            try {
                $results[] = $this->generatePayroll($employee->id, $month, $year);
            } catch (\RuntimeException $e) {
                $errors[] = $e->getMessage();
            }
        }

        return ['created' => $results, 'errors' => $errors];
    }

    private function monthName(int $month): string
    {
        $names = ['January','February','March','April','May','June',
                  'July','August','September','October','November','December'];
        return $names[$month - 1] ?? $month;
    }
}
