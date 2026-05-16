<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index()
    {
        $employees = Employee::orderByDesc('id')->paginate(20);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $autoCode = $this->previewEmployeeCode();
        return view('employees.create', compact('autoCode'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_code' => 'nullable|string|max:20',
            'name'          => 'required|string|max:100',
            'position'      => 'required|string|max:80',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:100',
            'address'       => 'nullable|string',
            'salary_type'   => 'required|in:monthly,daily',
            'base_salary'   => 'required|numeric|min:0',
            'join_date'     => 'required|date',
        ]);
        $employee = DB::transaction(function () use ($data) {
            $data['employee_code'] = $this->generateEmployeeCode();
            $data['is_active'] = true;
            $employee = Employee::create($data);
            $this->auditService->logCreation('employee', $employee, "Employee {$employee->employee_code} created");

            return $employee;
        });

        return redirect()->route('employees.index')->with('success', "Employee {$data['name']} added successfully.");
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'employee_code' => 'nullable|string|max:20',
            'name'          => 'required|string|max:100',
            'position'      => 'required|string|max:80',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:100',
            'address'       => 'nullable|string',
            'salary_type'   => 'required|in:monthly,daily',
            'base_salary'   => 'required|numeric|min:0',
            'join_date'     => 'required|date',
            'is_active'     => 'boolean',
        ]);
        $data['employee_code'] = $employee->employee_code;
        $data['is_active'] = $request->boolean('is_active');
        $oldValues = $employee->toArray();
        $employee->update($data);
        $this->auditService->logUpdate('employee', $employee, $oldValues, "Employee {$employee->employee_code} updated");

        return redirect()->route('employees.index')->with('success', "Employee {$employee->name} updated successfully.");
    }

    public function destroy(Employee $employee)
    {
        $oldValues = $employee->toArray();
        $employee->update(['is_active' => false]);
        $employee->delete();
        $this->auditService->log('employee', 'soft_delete', $employee, $oldValues, null, "Employee {$employee->employee_code} deleted");

        return redirect()->route('employees.index')->with('success', "{$employee->name} has been deleted.");
    }

    private function previewEmployeeCode(): string
    {
        $lastCode = Employee::withTrashed()
            ->orderByDesc(DB::raw("CAST(SUBSTRING(employee_code, 5) AS UNSIGNED)"))
            ->value('employee_code');
        $nextNum = 1;

        if ($lastCode && preg_match('/(\d+)$/', $lastCode, $matches)) {
            $nextNum = (int) $matches[1] + 1;
        }

        return 'EMP-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }

    private function generateEmployeeCode(): string
    {
        $lastNumber = Employee::withTrashed()
            ->where('employee_code', 'like', 'EMP-%')
            ->lockForUpdate()
            ->max(DB::raw("CAST(SUBSTRING(employee_code, 5) AS UNSIGNED)"));

        return 'EMP-' . str_pad(((int) $lastNumber) + 1, 3, '0', STR_PAD_LEFT);
    }
}
