<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('employee_code')->paginate(20);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $lastCode = Employee::orderByDesc('id')->value('employee_code');
        $nextNum  = 1;
        if ($lastCode && preg_match('/(\d+)$/', $lastCode, $m)) {
            $nextNum = (int) $m[1] + 1;
        }
        $autoCode = 'EMP-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        return view('employees.create', compact('autoCode'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_code' => 'required|string|max:20|unique:employees,employee_code',
            'name'          => 'required|string|max:100',
            'position'      => 'required|string|max:80',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:100',
            'address'       => 'nullable|string',
            'salary_type'   => 'required|in:monthly,daily',
            'base_salary'   => 'required|numeric|min:0',
            'join_date'     => 'required|date',
        ]);
        $data['is_active'] = true;
        Employee::create($data);
        return redirect()->route('employees.index')->with('success', "Employee {$data['name']} added successfully.");
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'employee_code' => 'required|string|max:20|unique:employees,employee_code,' . $employee->id,
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
        $data['is_active'] = $request->boolean('is_active', true);
        $employee->update($data);
        return redirect()->route('employees.index')->with('success', "Employee {$employee->name} updated successfully.");
    }

    public function destroy(Employee $employee)
    {
        $employee->update(['is_active' => false]);
        return redirect()->route('employees.index')->with('success', "{$employee->name} has been deactivated.");
    }
}
