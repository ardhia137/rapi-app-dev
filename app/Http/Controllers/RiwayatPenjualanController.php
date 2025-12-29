<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\SuratJalan;
use Barryvdh\DomPDF\Facade\Pdf;

class RiwayatPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter'); // 'invoice' | 'surat_jalan' | null
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $items = collect();

        if (!$filter || $filter === 'invoice') {
            $invQ = Invoice::with(['customer'])
                ->when($dateFrom, fn($q) => $q->whereDate('tanggal_invoice', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->whereDate('tanggal_invoice', '<=', $dateTo));
            // Stage: baru tahap invoice (belum SJ), consider all invoices
            $invoices = $invQ->orderByDesc('tanggal_invoice')->get()->map(function ($inv) {
                return [
                    'type' => 'invoice',
                    'nomor' => $inv->invoice_number,
                    'customer' => $inv->customer->nama_customer ?? '-',
                    'tanggal' => $inv->tanggal_invoice,
                    'grand_total' => (float) ($inv->grand_total ?? 0),
                    'status' => $inv->status_pembayaran,
                    'link' => route('invoices.show', $inv),
                ];
            });
            $items = $items->merge($invoices);
        }

        if (!$filter || $filter === 'surat_jalan') {
            $sjQ = SuratJalan::with(['customer', 'invoice'])
                ->when($dateFrom, fn($q) => $q->whereDate('tanggal', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->whereDate('tanggal', '<=', $dateTo))
                ->where('status', 'sudah dikirim'); // hanya surat jalan yang sudah dikirim
            $suratJalans = $sjQ->orderByDesc('tanggal')->get()->map(function ($sj) {
                return [
                    'type' => 'surat_jalan',
                    'nomor' => $sj->nomor_surat_jalan,
                    'customer' => $sj->customer->nama_customer ?? '-',
                    'tanggal' => $sj->tanggal,
                    'grand_total' => (float) ($sj->grand_total ?? 0),
                    'status' => $sj->status,
                    'link' => route('surat-jalan.show', $sj),
                ];
            });
            $items = $items->merge($suratJalans);
        }

        // Sort combined list by tanggal desc
        $riwayat = $items->sortByDesc('tanggal')->values();

        return view('penjualan.riwayat_penjualan.index', [
            'riwayat' => $riwayat,
            'filter' => $filter,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function pdf(Request $request)
    {
        // Similar logic as index to get filtered data
        $filter = $request->input('filter');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Determine filter label for PDF header
        $filterLabel = match ($filter) {
            'invoice' => 'Invoice',
            'surat_jalan' => 'Surat Jalan',
            default => 'Semua',
        };

        $items = collect();

        if (!$filter || $filter === 'invoice') {
            $invQ = Invoice::with(['customer'])
                ->when($dateFrom, fn($q) => $q->whereDate('tanggal_invoice', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->whereDate('tanggal_invoice', '<=', $dateTo));
            $invoices = $invQ->orderByDesc('tanggal_invoice')->get()->map(function ($inv) {
                $mapStatus = function ($status) {
                    return match ($status) {
                        'paid' => 'Lunas',
                        'unpaid' => 'Belum Lunas',
                        'overdue' => 'Terlambat',
                        'cancelled' => 'Dibatalkan',
                        default => ucfirst((string) $status),
                    };
                };
                return [
                    'type' => 'Invoice',
                    'nomor' => $inv->invoice_number,
                    'customer' => $inv->customer->nama_customer ?? '-',
                    'tanggal' => $inv->tanggal_invoice,
                    'grand_total' => (float) ($inv->grand_total ?? 0),
                    'status' => $mapStatus($inv->status_pembayaran),
                ];
            });
            $items = $items->merge($invoices);
        }

        if (!$filter || $filter === 'surat_jalan') {
            $sjQ = SuratJalan::with(['customer', 'invoice'])
                ->when($dateFrom, fn($q) => $q->whereDate('tanggal', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->whereDate('tanggal', '<=', $dateTo))
                ->where('status', 'sudah dikirim');
            $suratJalans = $sjQ->orderByDesc('tanggal')->get()->map(function ($sj) {
                $mapStatus = function ($status) {
                    return match ($status) {
                        'sudah dikirim' => 'Sudah Dikirim',
                        'pending' => 'Belum Dikirim',
                        'cancel' => 'Dibatalkan',
                        default => ucfirst((string) $status),
                    };
                };
                return [
                    'type' => 'Surat Jalan',
                    'nomor' => $sj->nomor_surat_jalan,
                    'customer' => $sj->customer->nama_customer ?? '-',
                    'tanggal' => $sj->tanggal,
                    'grand_total' => (float) ($sj->grand_total ?? 0),
                    'status' => $mapStatus($sj->status),
                ];
            });
            $items = $items->merge($suratJalans);
        }

        // Sort combined list by tanggal desc
        $riwayat = $items->sortByDesc('tanggal')->values();

        // Generate PDF using a view (you need to create this view)
        $pdf = Pdf::loadView('penjualan.riwayat_penjualan.pdf', [
            'riwayat' => $riwayat,
            'filterLabel' => $filterLabel,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);

        return $pdf->stream('riwayat_penjualan.pdf');
    }
}
