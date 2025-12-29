<?php

namespace App\Http\Controllers;

use App\Models\BudgetTarget;
use Illuminate\Http\Request;
use App\Traits\ActivityLogger;

class BudgetTargetController extends Controller
{
    use ActivityLogger;

    public function index()
    {
        $budgetTargets = BudgetTarget::orderBy('tanggal', 'desc')->get();
        return view('finance.budget-target.index', compact('budgetTargets'));
    }

    public function create()
    {
        return view('finance.budget-target.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'budget_bulanan' => 'required|numeric|min:0',
        ], [
            'tanggal.required' => 'Tanggal harus diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'budget_bulanan.required' => 'Budget bulanan harus diisi',
            'budget_bulanan.numeric' => 'Budget bulanan harus berupa angka',
            'budget_bulanan.min' => 'Budget bulanan tidak boleh kurang dari 0',
        ]);

        $budgetTarget = BudgetTarget::create($validated);

        self::logCreate($budgetTarget, 'Target Anggaran', 'Target Anggaran');

        return redirect()->route('budget-target.index')->with('success', 'Target anggaran berhasil ditambahkan');
    }

    public function show(BudgetTarget $budgetTarget)
    {
        return view('finance.budget-target.show', compact('budgetTarget'));
    }

    public function edit(BudgetTarget $budgetTarget)
    {
        return view('finance.budget-target.edit', compact('budgetTarget'));
    }

    public function update(Request $request, BudgetTarget $budgetTarget)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'budget_bulanan' => 'required|numeric|min:0',
        ], [
            'tanggal.required' => 'Tanggal harus diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'budget_bulanan.required' => 'Budget bulanan harus diisi',
            'budget_bulanan.numeric' => 'Budget bulanan harus berupa angka',
            'budget_bulanan.min' => 'Budget bulanan tidak boleh kurang dari 0',
        ]);


        $oldValues = $budgetTarget->only(['tanggal', 'budget_bulanan']);

        $budgetTarget->update($validated);

        $newValues = $budgetTarget->only(['tanggal', 'budget_bulanan']);
        self::logUpdate($budgetTarget, 'Target Anggaran', $oldValues, $newValues, 'Target Anggaran');

        return redirect()->route('budget-target.index')->with('success', 'Target anggaran berhasil diperbarui');
    }

    public function destroy(BudgetTarget $budgetTarget)
    {
        self::logDelete($budgetTarget, 'Target Anggaran', 'Target Anggaran');

        $budgetTarget->delete();

        return redirect()->route('budget-target.index')->with('success', 'Target anggaran berhasil dihapus');
    }
}
