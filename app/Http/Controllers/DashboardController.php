<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryLine;
use App\Models\Product;
use App\Models\Sale;
use App\Models\ServiceOrder;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(private AccountingService $accounting) {}

    public function index()
    {
        $from = now()->startOfMonth()->toDateString();
        $to   = now()->toDateString();

        // KPI
        $totalRevenue  = $this->accounting->getTotalByType('revenue', 'credit', $from, $to);
        $totalExpenses = $this->accounting->getTotalByType('expense', 'debit', $from, $to);
        $netProfit     = $totalRevenue - $totalExpenses;
        $lowStockCount = Product::whereColumn('stock', '<=', 'min_stock')->count();

        // Low stock products
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')->take(5)->get();

        // Active service orders
        $activeOrders = ServiceOrder::whereIn('status', ['Received', 'InProgress'])
            ->with('customer')->latest()->take(5)->get();

        // Recent sales
        $recentSales = Sale::with('customer')->latest()->take(5)->get();

        // Monthly Revenue vs Expenses chart (last 6 months)
        $chartData = $this->getMonthlyChartData();

        // Service order status breakdown
        $serviceStatusData = ServiceOrder::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status');

        return view('dashboard.index', compact(
            'totalRevenue', 'totalExpenses', 'netProfit', 'lowStockCount',
            'lowStockProducts', 'activeOrders', 'recentSales',
            'chartData', 'serviceStatusData'
        ));
    }

    private function getMonthlyChartData(): array
    {
        $months  = [];
        $revenue = [];
        $expense = [];

        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $from  = $date->copy()->startOfMonth()->toDateString();
            $to    = $date->copy()->endOfMonth()->toDateString();
            $label = $date->format('M Y');

            $months[]  = $label;
            $revenue[] = $this->accounting->getTotalByType('revenue', 'credit', $from, $to);
            $expense[] = $this->accounting->getTotalByType('expense', 'debit', $from, $to);
        }

        return compact('months', 'revenue', 'expense');
    }
}
