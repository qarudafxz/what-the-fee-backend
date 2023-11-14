<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;

class ExpensesController extends Controller
{
    public function addNewExpenses(int $college_id)
    {
        $validated = request()->validate([
            'title' => 'required|string|max:50',
            'desc' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date_borrowed' => 'required|date',
        ]);

        $expense = new Expense();
        $expense->fill($validated);
        $expense->college_id = $college_id;
        $expense->save();

        return response()->json([
            'message' => 'Expense added',
            'expense' => $expense,
        ]);
    }

    public function getAllExpensesInSpecificCollege(int $college_id)
    {
        $expenses = Expense::where('college_id', $college_id)->get();

        $total = Expense::where('college_id', $college_id)->sum('amount');

        return response()->json([
            'expenses' => $expenses,
            'total' => $total,
        ]);
    }
}
