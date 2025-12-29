<?php

namespace App\Http\Controllers;

use App\Models\FinanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Traits\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;

class FinanceRecordController extends Controller
{
    use ActivityLogger;

    public function index(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));

        $financeRecords = FinanceRecord::with('user')
            ->where('periode', $periode)
            ->orderBy('tanggal', 'desc')
            ->get();

        $budgetTarget = \App\Models\BudgetTarget::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$periode])->first();

        $totalPemasukan = $financeRecords->where('tipe', 'income')->sum('jumlah');
        $totalPengeluaran = $financeRecords->where('tipe', 'expense')->sum('jumlah');
        $saldo = $totalPemasukan - $totalPengeluaran;

        $availablePeriods = \App\Models\BudgetTarget::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as periode")
            ->orderBy('tanggal', 'desc')
            ->pluck('periode')
            ->unique();

        return view('finance.finance-records.index', compact('financeRecords', 'periode', 'budgetTarget', 'totalPemasukan', 'totalPengeluaran', 'saldo', 'availablePeriods'));
    }

    public function previewPdf(Request $request)
    {
        $periode = $request->get('periode');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = FinanceRecord::with('user');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif ($periode) {
            $query->where('periode', $periode);
        } else {
            $periode = date('Y-m');
            $query->where('periode', $periode);
        }

        $financeRecords = $query->orderBy('tanggal', 'desc')->get();

        $budgetTarget = null;
        if ($periode) {
            $budgetTarget = \App\Models\BudgetTarget::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$periode])->first();
        } elseif ($startDate) {
            $budgetTarget = \App\Models\BudgetTarget::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [date('Y-m', strtotime($startDate))])->first();
        }

        $totalPemasukan = $financeRecords->where('tipe', 'income')->sum('jumlah');
        $totalPengeluaran = $financeRecords->where('tipe', 'expense')->sum('jumlah');
        $saldoSisa = $budgetTarget ? ($budgetTarget->budget_bulanan - $totalPengeluaran) : 0;

        return view('finance.finance-records.pdf-preview', compact(
            'financeRecords',
            'periode',
            'startDate',
            'endDate',
            'budgetTarget',
            'totalPemasukan',
            'totalPengeluaran',
            'saldoSisa'
        ));
    }

    public function downloadPdf(Request $request)
    {
        $periode = $request->get('periode');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = FinanceRecord::with('user');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif ($periode) {
            $query->where('periode', $periode);
        } else {
            $periode = date('Y-m');
            $query->where('periode', $periode);
        }

        $financeRecords = $query->orderBy('tanggal', 'desc')->get();

        $budgetTarget = null;
        if ($periode) {
            $budgetTarget = \App\Models\BudgetTarget::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$periode])->first();
        } elseif ($startDate) {
            $budgetTarget = \App\Models\BudgetTarget::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [date('Y-m', strtotime($startDate))])->first();
        }

        $totalPemasukan = $financeRecords->where('tipe', 'income')->sum('jumlah');
        $totalPengeluaran = $financeRecords->where('tipe', 'expense')->sum('jumlah');
        $saldoSisa = $budgetTarget ? ($budgetTarget->budget_bulanan - $totalPengeluaran) : 0;

        $pdf = Pdf::loadView('finance.finance-records.pdf', compact(
            'financeRecords',
            'periode',
            'startDate',
            'endDate',
            'budgetTarget',
            'totalPemasukan',
            'totalPengeluaran',
            'saldoSisa'
        ));

        $pdf->setPaper('a4', 'portrait');

        $filename = 'Laporan-Keuangan';
        if ($periode) {
            $filename .= '-' . \Carbon\Carbon::parse($periode . '-01')->format('F-Y');
        } elseif ($startDate && $endDate) {
            $filename .= '-' . \Carbon\Carbon::parse($startDate)->format('dMY') . '-' . \Carbon\Carbon::parse($endDate)->format('dMY');
        }

        // ✅ LOG EXPORT dengan kategori
        self::logExport('Laporan Keuangan PDF', [
            'periode' => $periode,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ], 'Riwayat Keuangan');

        return $pdf->download($filename . '.pdf');
    }

    public function history(Request $request)
    {
        $currentMonth = date('Y-m');
        $hasCurrentMonthTarget = \App\Models\BudgetTarget::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$currentMonth])->exists();

        $periode = $request->get('periode');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if (!$request->has('periode') && !$request->has('start_date') && !$request->has('end_date') && $hasCurrentMonthTarget) {
            $periode = $currentMonth;
        }

        $query = FinanceRecord::with('user');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif ($periode) {
            $query->where('periode', $periode);
        }

        $financeRecords = $query->orderBy('tanggal', 'desc')->get();

        $budgetTarget = null;
        if ($periode) {
            $budgetTarget = \App\Models\BudgetTarget::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$periode])->first();
        } elseif ($startDate) {
            $budgetTarget = \App\Models\BudgetTarget::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [date('Y-m', strtotime($startDate))])->first();
        }

        $availablePeriods = \App\Models\BudgetTarget::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as periode")
            ->orderBy('tanggal', 'desc')
            ->pluck('periode')
            ->unique();

        return view('finance.finance-records.history', compact('financeRecords', 'periode', 'startDate', 'endDate', 'budgetTarget', 'availablePeriods'));
    }

    public function create()
    {
        return view('finance.finance-records.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'tipe' => 'required|in:income,expense',
            'kategori' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'foto_nota' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'tanggal.required' => 'Tanggal harus diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'tipe.required' => 'Tipe transaksi harus dipilih',
            'tipe.in' => 'Tipe transaksi tidak valid',
            'kategori.required' => 'Kategori harus diisi',
            'jumlah.required' => 'Jumlah harus diisi',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah tidak boleh kurang dari 0',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['periode'] = date('Y-m', strtotime($validated['tanggal']));

        $fotoPath = $request->file('foto_nota')->store('nota', 'public');
        $validated['foto_nota'] = $fotoPath;

        // ✅ PERBAIKAN: Hapus duplikat create
        $financeRecord = FinanceRecord::create($validated);

        // ✅ LOG CREATE dengan kategori
        self::logCreate($financeRecord, 'Data Keuangan', 'Input Keuangan');

        return redirect()->route('finance-records.index', ['periode' => $validated['periode']])->with('success', 'Data keuangan berhasil ditambahkan');
    }

    public function show(FinanceRecord $financeRecord)
    {
        return view('finance.finance-records.show', compact('financeRecord'));
    }

    public function edit(FinanceRecord $financeRecord)
    {
        return view('finance.finance-records.edit', compact('financeRecord'));
    }

    public function update(Request $request, FinanceRecord $financeRecord)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'tipe' => 'required|in:income,expense',
            'kategori' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'foto_nota' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'deskripsi' => 'nullable|string',
        ], [
            'tanggal.required' => 'Tanggal harus diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'tipe.required' => 'Tipe transaksi harus dipilih',
            'tipe.in' => 'Tipe transaksi tidak valid',
            'kategori.required' => 'Kategori harus diisi',
            'jumlah.required' => 'Jumlah harus diisi',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah tidak boleh kurang dari 0',
        ]);

        // Simpan nilai lama
        $oldValues = $financeRecord->only(['tanggal', 'tipe', 'kategori', 'jumlah', 'deskripsi']);

        $validated['created_by'] = Auth::id();
        $validated['periode'] = date('Y-m', strtotime($validated['tanggal']));

        // Update SEMUA FIELD KECUALI foto_nota
        $financeRecord->update(
            collect($validated)->except('foto_nota')->toArray()
        );

        // Handle foto jika ada upload baru
        if ($request->hasFile('foto_nota')) {
            if ($financeRecord->foto_nota && Storage::disk('public')->exists($financeRecord->foto_nota)) {
                Storage::disk('public')->delete($financeRecord->foto_nota);
            }

            $fotoPath = $request->file('foto_nota')->store('nota', 'public');
            $financeRecord->update(['foto_nota' => $fotoPath]);
        }

        // Nilai baru
        $newValues = $financeRecord->only(['tanggal', 'tipe', 'kategori', 'jumlah', 'deskripsi']);

        // ✅ LOG UPDATE dengan kategori
        self::logUpdate($financeRecord, 'Data Keuangan', $oldValues, $newValues, 'Input Keuangan');

        return redirect()->route('finance-records.index', ['periode' => $validated['periode']])->with('success', 'Data keuangan berhasil diperbarui');
    }

    public function destroy(FinanceRecord $financeRecord)
    {
        // Hapus foto jika ada
        if ($financeRecord->foto_nota && Storage::disk('public')->exists($financeRecord->foto_nota)) {
            Storage::disk('public')->delete($financeRecord->foto_nota);
        }

        // ✅ LOG DELETE dengan kategori
        self::logDelete($financeRecord, 'Data Keuangan', 'Input Keuangan');

        $financeRecord->delete();

        return redirect()->route('finance-records.index')->with('success', 'Data keuangan berhasil dihapus');
    }
}
