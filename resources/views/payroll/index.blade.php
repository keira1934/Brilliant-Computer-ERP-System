@extends('layouts.app')
@php
    $title = 'Payroll';
    $monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
@endphp
@section('content')
<div class="page-header">
    <div>
        <h1>Payroll</h1>
        <p class="page-sub">{{ $monthNames[$month-1] }} {{ $year }} — Total Paid: <strong>Rp {{ number_format($totalPaid,0,',','.') }}</strong></p>
    </div>
</div>

<div class="grid-2 mb-5" style="align-items:start">
    {{-- Generate Form --}}
    <div class="card">
        <div class="card-header"><h5 class="card-title">Generate & Pay Salary</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('payroll.generate') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Month <span class="required">*</span></label>
                        <select name="month" class="form-control" required>
                            @foreach($monthNames as $i => $mn)
                            <option value="{{ $i+1 }}" {{ $month==($i+1)?'selected':'' }}>{{ $mn }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Year <span class="required">*</span></label>
                        <input name="year" type="number" min="2000" max="2099" value="{{ $year }}" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Employee</label>
                    <select name="employee_id" class="form-control">
                        <option value="">-- All Active Employees --</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->employee_code }} — {{ $emp->name }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Leave blank to generate payroll for all active employees</div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Allowances</label>
                        <input name="allowances" type="number" min="0" step="0.01" value="0" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Deductions</label>
                        <input name="deductions" type="number" min="0" step="0.01" value="0" class="form-control">
                    </div>
                </div>
                <button type="submit" onclick="return confirm('Generate payroll and post journal entries?')" class="btn btn-primary w-100">
                    <i class="bi bi-currency-dollar"></i> Generate & Pay Salary
                </button>
            </form>
        </div>
    </div>

    {{-- Period Filter --}}
    <div class="card">
        <div class="card-header"><h5 class="card-title">View Period</h5></div>
        <div class="card-body">
            <form method="GET" action="{{ route('payroll.index') }}">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Month</label>
                        <select name="month" class="form-control">
                            @foreach($monthNames as $i => $mn)
                            <option value="{{ $i+1 }}" {{ $month==($i+1)?'selected':'' }}>{{ $mn }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Year</label>
                        <input name="year" type="number" value="{{ $year }}" class="form-control">
                    </div>
                </div>
                <button class="btn btn-secondary w-100"><i class="bi bi-search"></i> View Payroll</button>
            </form>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Payroll: {{ $monthNames[$month-1] }} {{ $year }}</h5>
        <span class="badge badge-navy">{{ $payrolls->count() }} Records</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Code</th><th>Employee</th><th>Position</th>
                    <th class="text-right">Base Salary</th><th class="text-right">Allowances</th>
                    <th class="text-right">Deductions</th><th class="text-right">Net Salary</th>
                    <th>Status</th><th>Paid Date</th>
                </tr>
            </thead>
            <tbody>
            @forelse($payrolls as $p)
            <tr>
                <td class="font-mono td-muted">{{ $p->employee->employee_code }}</td>
                <td class="td-primary">{{ $p->employee->name }}</td>
                <td class="td-muted">{{ $p->employee->position }}</td>
                <td class="text-right">Rp {{ number_format($p->base_salary,0,',','.') }}</td>
                <td class="text-right td-muted">{{ $p->allowances > 0 ? 'Rp '.number_format($p->allowances,0,',','.') : '-' }}</td>
                <td class="text-right td-muted">{{ $p->deductions > 0 ? 'Rp '.number_format($p->deductions,0,',','.') : '-' }}</td>
                <td class="text-right fw-bold">Rp {{ number_format($p->net_salary,0,',','.') }}</td>
                <td><span class="badge {{ $p->status === 'Paid' ? 'badge-success' : 'badge-warning' }}">{{ $p->status }}</span></td>
                <td class="td-muted">{{ $p->paid_at?->format('d/m/Y') }}<br><span class="font-mono" style="font-size:11px">{{ $p->created_at?->timezone(config('app.timezone'))->format('H:i:s') }}</span></td>
            </tr>
            @empty
            <tr><td colspan="9"><div class="empty-state"><i class="bi bi-currency-dollar"></i><p>No payroll records for {{ $monthNames[$month-1] }} {{ $year }}.<br>Use the form above to generate payroll.</p></div></td></tr>
            @endforelse
            </tbody>
            @if($payrolls->count() > 0)
            <tfoot>
                <tr style="background:var(--navy-50)">
                    <td colspan="6" class="text-right fw-bold" style="padding:12px 16px;color:var(--navy-900)">TOTAL NET SALARY</td>
                    <td class="text-right fw-bold" style="padding:12px 16px;color:var(--navy-900);font-size:15px">Rp {{ number_format($totalPaid,0,',','.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
