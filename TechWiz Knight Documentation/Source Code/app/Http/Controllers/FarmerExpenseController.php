<?php

namespace App\Http\Controllers;

use App\Models\FarmerExpense;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FarmerExpenseController extends Controller
{
    private function profile()
    {
        $profile = auth()->user()->farmerProfile;
        abort_unless($profile, 403);

        return $profile;
    }

    public function index(Request $request)
    {
        $farmer = $this->profile();
        $month = $request->input('month', now()->format('Y-m'));
        $start = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $expenses = $farmer->expenses()
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->latest('expense_date')
            ->latest('id')
            ->get();

        $total = (float) $expenses->sum('amount');
        $byCategory = $expenses->groupBy('category')->map(fn ($rows) => (float) $rows->sum('amount'));

        $sales = (float) $farmer->orders()
            ->where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');

        return view('farmer.expenses.index', [
            'farmer' => $farmer,
            'expenses' => $expenses,
            'total' => $total,
            'sales' => $sales,
            'net' => $sales - $total,
            'byCategory' => $byCategory,
            'month' => $month,
            'categories' => FarmerExpense::categories(),
        ]);
    }

    public function store(Request $request)
    {
        $farmer = $this->profile();
        $farmer->expenses()->create($this->validated($request));

        return back()->with('success', 'Expense saved.');
    }

    public function update(Request $request, FarmerExpense $expense)
    {
        $farmer = $this->profile();
        abort_unless((int) $expense->farmer_id === (int) $farmer->id, 403);
        $expense->update($this->validated($request));

        return back()->with('success', 'Expense updated.');
    }

    public function destroy(FarmerExpense $expense)
    {
        $farmer = $this->profile();
        abort_unless((int) $expense->farmer_id === (int) $farmer->id, 403);
        $expense->delete();

        return back()->with('success', 'Expense removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'category' => ['required', Rule::in(array_keys(FarmerExpense::categories()))],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:99999999'],
            'expense_date' => ['required', 'date'],
            'crop_name' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);
    }
}
