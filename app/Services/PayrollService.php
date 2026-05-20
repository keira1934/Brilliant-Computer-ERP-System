<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    public function __construct(
        private AccountingService $accounting,
        private AuditService $auditService
    ) {}

    public function generatePayroll(int $employeeId, int $month, int $year, float $allowances = 0, float $deductions = 0): Payroll
    {
        if ($allowances < 0 || $deductions < 0) {
            throw new \RuntimeException('Payroll allowances and deductions cannot be negative.');
        }

        $periodDate = Carbon::create($year, $month)->endOfMonth()->toDateString();

        return DB::transaction(function () use ($employeeId, $month, $year, $allowances, $deductions, $periodDate) {
            $employee = Employee::whereKey($employeeId)->lockForUpdate()->firstOrFail();

            // Lock the employee row and existing payroll record to serialize period generation.
            $existing = Payroll::where('employee_id', $employeeId)
                ->where('period_month', $month)
                ->where('period_year', $year)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                throw new \RuntimeException("Payroll for {$employee->name} ({$this->monthName($month)} {$year}) has already been generated.");
            }

            $baseSalary = (float) $employee->base_salary;
            $grossSalary = $baseSalary + $allowances;
            $netSalary = $grossSalary - $deductions;

            if ($grossSalary <= 0 || $netSalary < 0) {
                throw new \RuntimeException('Invalid payroll calculation: net salary cannot be negative.');
            }

            $payroll = Payroll::create([
                'employee_id'  => $employee->id,
                'period_month' => $month,
                'period_year'  => $year,
                'base_salary'  => $baseSalary,
                'allowances'   => $allowances,
                'deductions'   => $deductions,
                'net_salary'   => $netSalary,
                'paid_at'      => $periodDate,
                'status'       => 'Paid',
            ]);

            $journalLines = [
                ['code' => '5-2000', 'debit' => $grossSalary, 'credit' => 0,          'description' => "Salary expense: {$employee->name}"],
                ['code' => '1-1000', 'debit' => 0,            'credit' => $netSalary, 'description' => "Cash paid for salary: {$employee->name}"],
            ];

            if ($deductions > 0) {
                $journalLines[] = [
                    'code' => '2-3000',
                    'debit' => 0,
                    'credit' => $deductions,
                    'description' => "Payroll deductions withheld: {$employee->name}",
                ];
            }

            $this->accounting->postJournal(
                $periodDate,
                "Salary - {$employee->name} ({$this->monthName($month)} {$year})",
                'Payroll',
                $payroll->id,
                $journalLines
            );

            $this->auditService->logCreation('payroll', $payroll, "Payroll generated for {$employee->name} ({$this->monthName($month)} {$year})");

            return $payroll;
        });
    }

    /** Generate payroll for all active employees in a period. */
    public function generateAllPayroll(int $month, int $year, float $allowances = 0, float $deductions = 0): array
    {
        $employees = Employee::where('is_active', true)->get();
        $results   = [];
        $errors    = [];

        foreach ($employees as $employee) {
            try {
                $results[] = $this->generatePayroll($employee->id, $month, $year, $allowances, $deductions);
            } catch (\RuntimeException $e) {
                $errors[] = $e->getMessage();
            }
        }

        return ['created' => $results, 'errors' => $errors];
    }

    private function monthName(int $month): string
    {
        $names = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December',
        ];

        return $names[$month - 1] ?? $month;
    }
}
