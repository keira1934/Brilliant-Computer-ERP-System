@extends('layouts.app')
@php $title = 'Employee Profile'; @endphp
@section('content')

<div class="page-header">
    <div>
        <h1>Employee Profile</h1>
        <p class="page-sub">{{ $employee->employee_code }} — {{ $employee->name }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

<div class="grid-2" style="gap:24px">

    {{-- LEFT: Identity & Employment --}}
    <div style="display:flex;flex-direction:column;gap:20px">

        {{-- Identity Card --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-person-badge" style="color:var(--navy-500)"></i> Identity Information</h3>
                @if($employee->is_active)
                    <span class="badge badge-success"><i class="bi bi-circle-fill" style="font-size:8px"></i> Active</span>
                @else
                    <span class="badge badge-gray">Inactive</span>
                @endif
            </div>
            <div class="card-body">
                <div style="display:flex;align-items:center;gap:20px;margin-bottom:20px">
                    <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--navy-600),var(--navy-400));display:flex;align-items:center;justify-content:center;color:#fff;font-size:28px;font-weight:800;flex-shrink:0">
                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:20px;font-weight:800;color:var(--navy-900)">{{ $employee->name }}</div>
                        <div style="font-size:13px;color:var(--gray-500);margin-top:2px">{{ $employee->position }}</div>
                        <div class="font-mono" style="font-size:12px;color:var(--navy-600);margin-top:4px">{{ $employee->employee_code }}</div>
                    </div>
                </div>

                <table style="width:100%;font-size:13.5px;border-collapse:collapse">
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600;width:40%">Employee ID</td>
                        <td style="padding:8px 0;font-weight:600" class="font-mono">{{ $employee->employee_code }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Full Name</td>
                        <td style="padding:8px 0">{{ $employee->name }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Position</td>
                        <td style="padding:8px 0">{{ $employee->position }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Phone</td>
                        <td style="padding:8px 0">{{ $employee->phone ?? '—' }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Email</td>
                        <td style="padding:8px 0">{{ $employee->email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Address</td>
                        <td style="padding:8px 0">{{ $employee->address ?? '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Employment Details --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-briefcase" style="color:var(--navy-500)"></i> Employment Details</h3>
            </div>
            <div class="card-body">
                <table style="width:100%;font-size:13.5px;border-collapse:collapse">
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600;width:40%">Join Date</td>
                        <td style="padding:8px 0">{{ $employee->join_date ? $employee->join_date->format('d F Y') : '—' }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Years of Service</td>
                        <td style="padding:8px 0">
                            @if($employee->join_date)
                                {{ $employee->join_date->diffInYears(now()) }} year(s),
                                {{ $employee->join_date->copy()->addYears($employee->join_date->diffInYears(now()))->diffInMonths(now()) }} month(s)
                            @else —
                            @endif
                        </td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Employment Status</td>
                        <td style="padding:8px 0">
                            @if($employee->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-gray">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Salary Type</td>
                        <td style="padding:8px 0"><span class="badge badge-navy">{{ ucfirst($employee->salary_type) }}</span></td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Base Salary</td>
                        <td style="padding:8px 0;font-weight:700;color:var(--navy-900)">Rp {{ number_format($employee->base_salary, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

    </div>

    {{-- RIGHT: Payroll History --}}
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-cash-stack" style="color:var(--navy-500)"></i> Recent Payroll History</h3>
                <a href="{{ route('payroll.index') }}" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="card-body" style="padding:0">
                @if($recentPayrolls->isEmpty())
                    <div class="empty-state" style="padding:32px">
                        <i class="bi bi-cash-stack"></i>
                        <p>No payroll records found</p>
                    </div>
                @else
                    <table class="data-table compact">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th class="text-right">Base Salary</th>
                                <th class="text-right">Allowances</th>
                                <th class="text-right">Deductions</th>
                                <th class="text-right">Net Salary</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPayrolls as $payroll)
                            <tr>
                                <td class="fw-semibold">
                                    {{ \Carbon\Carbon::createFromDate($payroll->period_year, $payroll->period_month, 1)->format('M Y') }}
                                </td>
                                <td class="text-right td-muted">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
                                <td class="text-right text-success">+Rp {{ number_format($payroll->allowances ?? 0, 0, ',', '.') }}</td>
                                <td class="text-right text-danger">-Rp {{ number_format($payroll->deductions ?? 0, 0, ',', '.') }}</td>
                                <td class="text-right fw-bold">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</td>
                                <td>
                                    @if(($payroll->status ?? '') === 'paid' || $payroll->paid_at)
                                        <span class="badge badge-success">Paid</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
