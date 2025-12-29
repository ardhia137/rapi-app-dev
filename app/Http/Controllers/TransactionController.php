<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Traits\ActivityLogger;

class TransactionController extends Controller
{
    use ActivityLogger;

    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $invoice = Invoice::findOrFail($data['invoice_id']);

            // Simpan status lama untuk logging
            $oldStatus = $invoice->status_pembayaran;

            $trx = Transaction::create([
                'invoice_id' => $invoice->id,
                'surat_jalan_id' => $data['surat_jalan_id'] ?? null,
                'tanggal_pembayaran' => $data['tanggal_pembayaran'],
                'jumlah_bayar' => $data['jumlah_bayar'],
                'metode_pembayaran' => $data['metode_pembayaran'],
                'bukti_pembayaran' => $data['bukti_pembayaran'] ?? null,
            ]);

            $paid = (float) $invoice->transactions()->sum('jumlah_bayar');
            $newPaid = $paid + (float) $data['jumlah_bayar'];

            $status = 'partial';
            if ($newPaid <= 0) {
                $status = 'unpaid';
            } elseif ($newPaid >= (float) $invoice->grand_total) {
                $status = 'paid';
            }

            $invoice->update(['status_pembayaran' => $status]);

            // ✅ LOG CREATE TRANSAKSI dengan kategori
            self::logCreate($trx, 'Transaksi Pembayaran', 'Riwayat Transaksi');

            // ✅ LOG PERUBAHAN STATUS jika status berubah
            if ($oldStatus != $status) {
                self::logStatusChange($invoice, 'Invoice', $oldStatus, $status, 'Riwayat Transaksi');
            }

            return redirect()->route('invoices.show', $invoice)->with('success', 'Payment recorded');
        });
    }

    public function index()
    {
        $query = Transaction::query();
        if (request('invoice_id')) {
            $query->where('invoice_id', request('invoice_id'));
        }
        if (request('surat_jalan_id')) {
            $query->where('surat_jalan_id', request('surat_jalan_id'));
        }
        $transactions = $query->with(['invoice'])->orderByDesc('tanggal_pembayaran')->paginate(20);
        return view('transactions.index', compact('transactions'));
    }
}
