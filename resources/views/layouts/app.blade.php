<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — Briliant Computer ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
<div class="app-layout">

    {{-- SIDEBAR --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-pc-display-horizontal"></i></div>
            <div class="brand-text">
                <span class="brand-name">Briliant Computer</span>
                <span class="brand-sub">ERP System v2.0</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i><span>Dashboard</span>
                </a>
            </div>

            <div class="nav-section">Sales & Service</div>
            @if(auth()->user()->hasRole('finance', 'cashier', 'manager'))
            <div class="nav-item">
                <a href="#" class="nav-link has-submenu" data-submenu="menu-sales">
                    <i class="bi bi-shop"></i><span>Sales & Service</span>
                    <i class="bi bi-chevron-right nav-arrow"></i>
                </a>
                <div class="nav-submenu" id="menu-sales">
                    <a href="{{ route('customers.index') }}" class="nav-sub-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">Customers</a>
                    <a href="{{ route('service-orders.index') }}" class="nav-sub-link {{ request()->routeIs('service-orders.*') ? 'active' : '' }}">Service Orders</a>
                    <a href="{{ route('sales.index') }}" class="nav-sub-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">Sales Transactions</a>
                    <a href="{{ route('accounts-receivable.index') }}" class="nav-sub-link {{ request()->routeIs('accounts-receivable.*') ? 'active' : '' }}">Accounts Receivable</a>
                </div>
            </div>
            @endif

            <div class="nav-section">Inventory</div>
            @if(auth()->user()->hasRole('inventory', 'manager', 'finance'))
            <div class="nav-item">
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i><span>Product Catalog</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('inventory-movements.index') }}" class="nav-link {{ request()->routeIs('inventory-movements.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i><span>Inventory Ledger</span>
                </a>
            </div>
            @endif

            <div class="nav-section">Purchasing</div>
            @if(auth()->user()->hasRole('finance', 'inventory', 'manager'))
            <div class="nav-item">
                <a href="#" class="nav-link has-submenu" data-submenu="menu-purchase">
                    <i class="bi bi-cart3"></i><span>Purchasing</span>
                    <i class="bi bi-chevron-right nav-arrow"></i>
                </a>
                <div class="nav-submenu" id="menu-purchase">
                    <a href="{{ route('suppliers.index') }}" class="nav-sub-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">Suppliers</a>
                    <a href="{{ route('purchases.index') }}" class="nav-sub-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">Purchase Orders</a>
                </div>
            </div>
            @endif

            <div class="nav-section">Finance & Accounting</div>
            @if(auth()->user()->hasRole('finance', 'manager'))
            <div class="nav-item">
                <a href="#" class="nav-link has-submenu" data-submenu="menu-finance">
                    <i class="bi bi-calculator"></i><span>Accounting</span>
                    <i class="bi bi-chevron-right nav-arrow"></i>
                </a>
                <div class="nav-submenu" id="menu-finance">
                    <a href="{{ route('ledger.coa') }}" class="nav-sub-link {{ request()->routeIs('ledger.coa') ? 'active' : '' }}">Chart of Accounts</a>
                    <a href="{{ route('ledger.general') }}" class="nav-sub-link {{ request()->routeIs('ledger.general') ? 'active' : '' }}">General Ledger</a>
                    <a href="{{ route('ledger.journal') }}" class="nav-sub-link {{ request()->routeIs('ledger.journal') ? 'active' : '' }}">Journal Entries</a>
                    <a href="{{ route('adjusting-entries.index') }}" class="nav-sub-link {{ request()->routeIs('adjusting-entries.*') ? 'active' : '' }}">Adjusting Entries</a>
                    <a href="{{ route('accounts-payable.index') }}" class="nav-sub-link {{ request()->routeIs('accounts-payable.*') ? 'active' : '' }}">Accounts Payable</a>
                    <a href="{{ route('financial-periods.index') }}" class="nav-sub-link {{ request()->routeIs('financial-periods.*') ? 'active' : '' }}">Financial Periods</a>
                </div>
            </div>

            <div class="nav-item">
                <a href="#" class="nav-link has-submenu" data-submenu="menu-reports">
                    <i class="bi bi-file-earmark-bar-graph"></i><span>Financial Reports</span>
                    <i class="bi bi-chevron-right nav-arrow"></i>
                </a>
                <div class="nav-submenu" id="menu-reports">
                    <a href="{{ route('reports.trial-balance') }}" class="nav-sub-link {{ request()->routeIs('reports.trial-balance') ? 'active' : '' }}">Trial Balance</a>
                    <a href="{{ route('reports.balance-sheet') }}" class="nav-sub-link {{ request()->routeIs('reports.balance-sheet') ? 'active' : '' }}">Balance Sheet</a>
                    <a href="{{ route('reports.income-statement') }}" class="nav-sub-link {{ request()->routeIs('reports.income-statement') ? 'active' : '' }}">Income Statement</a>
                    <a href="{{ route('reports.cash-flow') }}" class="nav-sub-link {{ request()->routeIs('reports.cash-flow') ? 'active' : '' }}">Cash Flow</a>
                    <a href="{{ route('reports.transactions') }}" class="nav-sub-link {{ request()->routeIs('reports.transactions') ? 'active' : '' }}">Transaction Summary</a>
                </div>
            </div>

            <div class="nav-item">
                <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i><span>Expenses</span>
                </a>
            </div>
            @endif

            <div class="nav-section">HR & Payroll</div>
            @if(auth()->user()->hasRole('hr', 'manager'))
            <div class="nav-item">
                <a href="#" class="nav-link has-submenu" data-submenu="menu-hr">
                    <i class="bi bi-people"></i><span>HR & Payroll</span>
                    <i class="bi bi-chevron-right nav-arrow"></i>
                </a>
                <div class="nav-submenu" id="menu-hr">
                    <a href="{{ route('employees.index') }}" class="nav-sub-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">Employees</a>
                    <a href="{{ route('payroll.index') }}" class="nav-sub-link {{ request()->routeIs('payroll.*') ? 'active' : '' }}">Payroll</a>
                </div>
            </div>
            @endif

            @if(auth()->user()->isAdmin())
            <div class="nav-section">Administration</div>
            <div class="nav-item">
                <a href="#" class="nav-link has-submenu" data-submenu="menu-admin">
                    <i class="bi bi-gear"></i><span>System Admin</span>
                    <i class="bi bi-chevron-right nav-arrow"></i>
                </a>
                <div class="nav-submenu" id="menu-admin">
                    <a href="{{ route('users.index') }}" class="nav-sub-link {{ request()->routeIs('users.*') ? 'active' : '' }}">User Management</a>
                    <a href="{{ route('audit-logs.index') }}" class="nav-sub-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">Audit Trail</a>
                </div>
            </div>
            @endif
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-footer-text">
                &copy; {{ date('Y') }} Briliant Computer<br>Accounting ERP System
            </div>
        </div>
    </aside>

    {{-- TOPBAR --}}
    <header class="topbar">
        <div>
            <div class="topbar-title">{{ $title ?? 'Dashboard' }}</div>
            @isset($breadcrumb)<div class="topbar-breadcrumb">{{ $breadcrumb }}</div>@endisset
        </div>
        <div class="topbar-right">
            <div class="topbar-date"><i class="bi bi-calendar3" style="margin-right:5px"></i>{{ now()->format('l, d F Y') }}</div>
            <div class="topbar-divider"></div>
            <div class="topbar-user">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="user-info">
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <span class="user-role">{{ auth()->user()->getRoleLabel() }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0">
                @csrf
                <button type="submit" class="btn-logout" title="Sign Out">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </header>

    {{-- CONTENT --}}
    <main class="main-content fade-in">
        @if(session('success'))
            <div class="alert alert-success" data-auto-dismiss>
                <i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger" data-auto-dismiss>
                <i class="bi bi-exclamation-circle-fill"></i><span>{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div><strong>Please correct the following errors:</strong>
                    <ul style="margin:4px 0 0 16px;font-size:13px">
                        @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                    </ul>
                </div>
            </div>
        @endif
        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@if(request()->boolean('print'))
<script>
window.addEventListener('load', function () {
    setTimeout(function () { window.print(); }, 250);
});
</script>
@endif
@stack('scripts')
</body>
</html>
