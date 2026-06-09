<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Expense;
use Exception;

class ExpenseController extends Controller
{
    protected function getCompanyId()
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        try {
            $query = Expense::query();
            if ($companyId = $this->getCompanyId()) {
                $query->where('company_id', $companyId);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('expense_name', 'like', "%{$s}%")
                      ->orWhere('description', 'like', "%{$s}%");
                });
            }

            $expenses = $query->latest()->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admin.expense._table', compact('expenses'))->render(),
                    'pagination' => view('admin.expense._pagination', compact('expenses'))->render(),
                ]);
            }

            return view('admin.expense.index', compact('expenses'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        return view('admin.expense.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_name' => 'required',
            'description'  => 'nullable',
        ]);

        try {
            $data = $request->all();
            $data['company_id'] = $this->getCompanyId();
            $data['created_by'] = Auth::id();
            $data['status'] = 'active';

            Expense::create($data);

            return redirect()->route('admin.expense.index')->with('success', 'Expense created successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit(Expense $expense)
    {
        $this->authorizeAccess($expense);
        return view('admin.expense.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorizeAccess($expense);
        $request->validate([
            'expense_name' => 'required',
            'description'  => 'nullable',
        ]);

        try {
            $data = $request->all();
            $expense->update($data);

            return redirect()->route('admin.expense.index')->with('success', 'Expense updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function toggleStatus(Expense $expense)
    {
        $this->authorizeAccess($expense);
        try {
            $expense->status = $expense->status === 'active' ? 'inactive' : 'active';
            $expense->save();
            return response()->json(['success' => true, 'status' => $expense->status]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy(Expense $expense)
    {
        $this->authorizeAccess($expense);
        try {
            $expense->delete();
            return redirect()->route('admin.expense.index')->with('success', 'Expense deleted successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function authorizeAccess(Expense $expense)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $expense->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
