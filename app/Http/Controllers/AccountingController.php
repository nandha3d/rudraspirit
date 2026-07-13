<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Basic Accounting (Plan B, Phase 1): expenses, categories, financial accounts,
 * and a Profit & Loss statement that reuses the Plan A order-line cost snapshot.
 * All read/writes are on the new accounting tables only.
 */
class AccountingController extends Controller
{
    public function __construct()
    {
        // License gate: accounting requires the 'accounting' feature.
        $this->middleware(function ($request, $next) {
            abort_unless(feature_allowed('accounting'), 404);
            return $next($request);
        });
    }

    private function demoBlocked(): bool
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('This action is disabled in demo mode'))->warning();
            return true;
        }
        return false;
    }

    // ---------------- Expenses ----------------
    public function expenses(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from)->startOfDay() : Carbon::now()->startOfMonth();
        $to   = $request->to ? Carbon::parse($request->to)->endOfDay() : Carbon::now()->endOfDay();

        $query = Expense::with(['category', 'account'])->whereBetween('date', [$from, $to]);
        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }
        $total = (clone $query)->sum(DB::raw('amount + tax'));
        $expenses = $query->orderByDesc('date')->orderByDesc('id')->paginate(20)->appends($request->all());

        $categories = ExpenseCategory::orderBy('name')->get();
        $accounts = FinancialAccount::orderBy('name')->get();

        return view('backend.accounting.expenses', compact('expenses', 'categories', 'accounts', 'total', 'from', 'to'));
    }

    public function store_expense(Request $request)
    {
        if ($this->demoBlocked()) return back();
        $request->validate(['amount' => 'required|numeric|min:0', 'date' => 'required']);

        Expense::create([
            'expense_category_id' => $request->expense_category_id ?: null,
            'financial_account_id' => $request->financial_account_id ?: null,
            'amount' => $request->amount,
            'tax' => $request->tax ?: 0,
            'date' => $request->date,
            'payee' => $request->payee,
            'reference' => $request->reference,
            'attachment' => $request->attachment,
            'note' => $request->note,
        ]);
        flash(translate('Expense added'))->success();
        return back();
    }

    public function destroy_expense($id)
    {
        if ($this->demoBlocked()) return back();
        Expense::destroy($id);
        flash(translate('Expense deleted'))->success();
        return back();
    }

    // ---------------- Categories ----------------
    public function categories()
    {
        $categories = ExpenseCategory::withCount('expenses')->orderBy('name')->get();
        return view('backend.accounting.categories', compact('categories'));
    }

    public function store_category(Request $request)
    {
        if ($this->demoBlocked()) return back();
        $request->validate(['name' => 'required|max:191']);
        ExpenseCategory::create(['name' => $request->name]);
        flash(translate('Category added'))->success();
        return back();
    }

    public function destroy_category($id)
    {
        if ($this->demoBlocked()) return back();
        ExpenseCategory::destroy($id);
        flash(translate('Category deleted'))->success();
        return back();
    }

    // ---------------- Financial accounts ----------------
    public function accounts()
    {
        $accounts = FinancialAccount::orderBy('name')->get();
        foreach ($accounts as $acc) {
            $spent = Expense::where('financial_account_id', $acc->id)->sum(DB::raw('amount + tax'));
            $acc->current_balance = (float) $acc->opening_balance - (float) $spent;
        }
        return view('backend.accounting.accounts', compact('accounts'));
    }

    public function store_account(Request $request)
    {
        if ($this->demoBlocked()) return back();
        $request->validate(['name' => 'required|max:191']);
        FinancialAccount::create([
            'name' => $request->name,
            'type' => $request->type ?: 'cash',
            'opening_balance' => $request->opening_balance ?: 0,
        ]);
        flash(translate('Account added'))->success();
        return back();
    }

    public function destroy_account($id)
    {
        if ($this->demoBlocked()) return back();
        FinancialAccount::destroy($id);
        flash(translate('Account deleted'))->success();
        return back();
    }

    // ---------------- Profit & Loss ----------------
    public function profit_loss(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from)->startOfDay() : Carbon::now()->startOfMonth();
        $to   = $request->to ? Carbon::parse($request->to)->endOfDay() : Carbon::now()->endOfDay();

        $sales = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->where('orders.delivery_status', '!=', 'cancelled')
            ->selectRaw('
                COALESCE(SUM(order_details.price),0) as revenue,
                COALESCE(SUM(order_details.cost_price),0) as cogs,
                COALESCE(SUM(order_details.coupon_discount),0) as discount,
                COALESCE(SUM(order_details.shipping_cost),0) as shipping,
                COALESCE(SUM(order_details.tax),0) as tax
            ')->first();

        $expensesByCat = DB::table('expenses')
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
            ->whereBetween('expenses.date', [$from, $to])
            ->selectRaw('COALESCE(expense_categories.name, "Uncategorized") as category, SUM(expenses.amount + expenses.tax) as total')
            ->groupBy('category')->orderByDesc('total')->get();

        $totalExpenses = (float) $expensesByCat->sum('total');

        $revenue = (float) $sales->revenue;
        $cogs = (float) $sales->cogs;
        $discount = (float) $sales->discount;
        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $discount - $totalExpenses;

        return view('backend.accounting.profit_loss', compact(
            'from', 'to', 'revenue', 'cogs', 'discount', 'grossProfit',
            'expensesByCat', 'totalExpenses', 'netProfit'
        ) + ['shipping' => (float) $sales->shipping]);
    }
}
