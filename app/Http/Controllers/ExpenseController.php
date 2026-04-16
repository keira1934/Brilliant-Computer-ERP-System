<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct(private ExpenseService $expenseService) {}

    public function index(Request $request)
    {
        $query = Expense::with('account')->latest('expense_date');
        if ($request->category) $query->where('category', $request->category);
        if ($request->from)     $query->where('expense_date', '>=', $request->from);
        if ($request->to)       $query->where('expense_date', '<=', $request->to);

        $expenses    = $query->paginate(20)->withQueryString();
        $totalMonth  = Expense::whereBetween('expense_date', [
            now()->startOfMonth()->toDateString(),
            now()->toDateString(),
        ])->sum('amount');

        return view('expenses.index', compact('expenses', 'totalMonth'));
    }

    public function create()
    {
        $expenseAccounts = ChartOfAccount::where('type', 'expense')->orderBy('code')->get();
        return view('expenses.create', compact('expenseAccounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_date' => 'required|date',
            'category'     => 'required|in:Electricity,Maintenance,Internet,Rent,Other',
            'description'  => 'nullable|string|max:255',
            'amount'       => 'required|numeric|min:1',
            'account_id'   => 'nullable|exists:chart_of_accounts,id',
            'reference'    => 'nullable|string|max:100',
        ]);

        try {
            $this->expenseService->recordExpense($data);
            return redirect()->route('expenses.index')
                ->with('success', 'Expense recorded. Journal entry posted.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
