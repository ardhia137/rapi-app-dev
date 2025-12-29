<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\SetorInvoiceRequest;
use App\Models\ProductBatch;
use App\Models\Product;
use App\Traits\ActivityLogger;
use Illuminate\Support\Facades\Storage;
use App\Models\SuratJalan;
use Illuminate\Support\Facades\Log;


class InvoiceController extends Controller
{
    use ActivityLogger;

    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        Invoice::where('status_pembayaran', 'unpaid')
            ->whereNotNull('tanggal_jatuh_tempo')
            ->whereDate('tanggal_jatuh_tempo', '<', now()->toDateString())
            ->update(['status_pembayaran' => 'overdue']);

        $query = Invoice::with(['customer', 'user'])
            ->when($dateFrom, fn($q) => $q->whereDate('tanggal_invoice', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('tanggal_invoice', '<=', $dateTo))
            ->orderByDesc('created_at');

        $invoices = $query->paginate(20)->appends($request->only('date_from', 'date_to'));

        $paidFilter = Invoice::query()
            ->when($dateFrom, fn($q) => $q->whereDate('tanggal_invoice', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('tanggal_invoice', '<=', $dateTo));

        $totalCount = (clone $paidFilter)->count();
        $paidCount = (clone $paidFilter)->where('status_pembayaran', 'paid')->count();
        $totalPaid = (clone $paidFilter)->where('status_pembayaran', 'paid')->sum('grand_total');
        $totalSetor = (clone $paidFilter)->where('status_pembayaran', 'paid')
            ->where('status_setor', 'sudah')
            ->sum('grand_total');

        return view('penjualan.invoices.index', compact('invoices', 'totalPaid', 'totalSetor', 'paidCount', 'totalCount', 'dateFrom', 'dateTo'));
    }

    public function create()
    {
        $customers = \App\Models\Customer::all();
        $products = \App\Models\Product::all();
        $batches = \App\Models\ProductBatch::query()
            ->where(function ($q) {
                $q->whereNull('tanggal_expired')
                    ->orWhereDate('tanggal_expired', '>=', now()->toDateString());
            })
            ->where('quantity_sekarang', '>', 0)
            ->orderByDesc('created_at')
            ->get();

        return view('penjualan.invoices.create', compact('customers', 'products', 'batches'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $data = $request->validated();

        // Normalisasi input tambahan dari form
        $ongkir = (int) preg_replace('/\D/', '', (string) ($request->input('ongkos_kirim') ?? 0));
        $diskon = (int) preg_replace('/\D/', '', (string) ($request->input('diskon') ?? 0));
        $metodePembayaran = $request->input('metode_pembayaran');

        // Handle upload bukti_setor (ini adalah bukti SETORAN KE BANK, bukan bukti bayar customer)
        $buktiSetorPath = null;
        if ($request->hasFile('bukti_setor')) {
            $buktiSetorPath = $request->file('bukti_setor')->store('setor', 'public');
        }

        return DB::transaction(function () use ($data, $ongkir, $diskon, $metodePembayaran, $buktiSetorPath) {
            // Tentukan customer_id
            $customerId = null;
            if (($data['customer_type'] ?? 'existing') === 'new') {
                $newCustomer = Customer::create([
                    'nama_customer' => $data['customer_name'],
                    'kategori_pelanggan' => $data['kategori_pelanggan'] ?? 'Konsumen',
                ]);
                $customerId = $newCustomer->id;
            } else {
                $customerId = $data['customer_id'];
            }

            // 🔥 LOGIKA BARU - LEBIH JELAS:
            // 1. Status Pembayaran: tetap sesuai pilihan user (paid/unpaid/overdue)
            // 2. Status Setor: HANYA "sudah" jika ada bukti_setor (bukti setoran ke bank)

            $statusPembayaran = $data['status_pembayaran'] ?? 'unpaid';
            $statusSetor = 'belum'; // Default: belum disetor
            $tanggalSetor = null;

            // Jika ada bukti setor -> status setor = "sudah"
            if ($buktiSetorPath) {
                $statusSetor = 'sudah';
                $tanggalSetor = now()->toDateString();
            }

            $invoice = Invoice::create([
                'invoice_number' => $data['invoice_number'] ?? Str::upper(Str::random(8)),
                'customer_id' => $customerId,
                'user_id' => $data['user_id'],
                'tanggal_invoice' => $data['tanggal_invoice'],
                'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'],
                'tanggal_setor' => $tanggalSetor,
                'status_pembayaran' => $statusPembayaran,
                'status_setor' => $statusSetor,
                'bukti_setor' => $buktiSetorPath,
                'alasan_cancel' => $data['alasan_cancel'] ?? null,
                'grand_total' => 0,
            ]);

            // Set metode pembayaran dan nilai tambahan
            if (!is_null($metodePembayaran)) {
                $invoice->metode_pembayaran = $metodePembayaran;
            }
            $invoice->ongkos_kirim = $ongkir;
            $invoice->diskon = $diskon;
            $invoice->save();

            $grandTotal = 0;
            foreach ($data['items'] as $item) {
                $subTotal = (int)$item['quantity'] * (float)$item['harga'];
                $grandTotal += $subTotal;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'batch_id' => $item['batch_id'],
                    'quantity' => $item['quantity'],
                    'harga' => $item['harga'],
                    'sub_total' => $subTotal,
                ]);
            }

            // Terapkan ongkos kirim (+) dan diskon (-) ke grand total
            $grandTotal = max(0, (float) $grandTotal + (float) $ongkir - (float) $diskon);
            $invoice->update(['grand_total' => $grandTotal]);

            // Tambah poin customer jika lunas
            if (($invoice->status_pembayaran ?? 'unpaid') === 'paid') {
                try {
                    $customer = \App\Models\Customer::find($invoice->customer_id);
                    if ($customer) {
                        if ($customer->point === null) {
                            $customer->point = 0;
                            $customer->save();
                        }

                        $customer->increment('point', 1);
                    }
                } catch (\Throwable $e) {
                }
            }

            // Auto-create Surat Jalan
            SuratJalan::create([
                'nomor_surat_jalan' => 'SJ-' . now()->format('ymd') . '-' . Str::upper(Str::random(5)),
                'customer_id'       => $invoice->customer_id,
                'invoice_id'        => $invoice->id,
                'tanggal'           => $invoice->tanggal_invoice ?? now()->toDateString(),
                'status'            => 'belum dikirim',
                'bukti_pengiriman'  => null,
                'alasan_cancel'     => null,
            ]);

            self::logCreate($invoice, 'Invoice', 'Penjualan');

            return redirect()->route('invoices.index')->with('success', 'Invoice created successfully');
        });
    }

    public function edit(Invoice $invoice)
    {
        $customers = \App\Models\Customer::all();
        $products = \App\Models\Product::all();
        $batches = \App\Models\ProductBatch::query()
            ->where(function ($q) {
                $q->whereNull('tanggal_expired')
                    ->orWhereDate('tanggal_expired', '>=', now()->toDateString());
            })
            ->where('quantity_sekarang', '>', 0)
            ->orderByDesc('created_at')
            ->get();

        $invoice->load(['items.product', 'items.batch']);
        return view('penjualan.invoices.edit', compact('invoice', 'customers', 'products', 'batches'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $data = $request->validated();

        $oldCustomerId = $invoice->customer_id;
        $oldGrandTotal = $invoice->grand_total;
        $wasPaid = $invoice->status_pembayaran === 'paid';

        $oldValues = $invoice->only([
            'customer_id',
            'user_id',
            'tanggal_invoice',
            'tanggal_jatuh_tempo',
            'status_pembayaran',
            'grand_total',
            'alasan_cancel'
        ]);

        $ongkir = (int) preg_replace('/\D/', '', (string) ($request->input('ongkos_kirim') ?? 0));
        $diskon = (int) preg_replace('/\D/', '', (string) ($request->input('diskon') ?? 0));
        $metodePembayaran = $request->input('metode_pembayaran');

        // Handle upload bukti_setor
        $buktiSetorPath = $invoice->bukti_setor;
        if ($request->hasFile('bukti_setor')) {
            if ($invoice->bukti_setor && Storage::disk('public')->exists($invoice->bukti_setor)) {
                Storage::disk('public')->delete($invoice->bukti_setor);
            }
            $buktiSetorPath = $request->file('bukti_setor')->store('setor', 'public');
        }

        return DB::transaction(function () use ($request, $invoice, $data, $ongkir, $diskon, $metodePembayaran, $buktiSetorPath, $wasPaid, $oldCustomerId, $oldGrandTotal, $oldValues) {

            $customerId = null;
            if (($data['customer_type'] ?? 'existing') === 'new') {
                $newCustomer = Customer::create([
                    'nama_customer' => $data['customer_name'],
                    'kategori_pelanggan' => $data['kategori_pelanggan'] ?? 'Konsumen',
                ]);
                $customerId = $newCustomer->id;
            } else {
                $customerId = $data['customer_id'];
            }

            $isCancelled = isset($data['status_pembayaran']) && $data['status_pembayaran'] === 'cancelled';

            // Hapus items lama (stok tidak diubah pada tahap invoice)
            $invoice->items()->delete();

            // 🔥 LOGIKA BARU:
            // Status pembayaran: sesuai input user
            // Status setor: HANYA "sudah" jika ada bukti setor

            $statusPembayaran = $data['status_pembayaran'] ?? 'unpaid';
            $statusSetor = $invoice->status_setor ?? 'belum';
            $tanggalSetor = $invoice->tanggal_setor;

            // Jika ada bukti setor (baru atau existing) -> status setor = "sudah"
            if ($buktiSetorPath) {
                $statusSetor = 'sudah';
                $tanggalSetor = $tanggalSetor ?? now()->toDateString();
            } else {
                // Jika tidak ada bukti setor -> status setor = "belum"
                $statusSetor = 'belum';
                $tanggalSetor = null;
            }

            $invoice->update([
                'customer_id' => $customerId,
                'user_id' => $data['user_id'],
                'tanggal_invoice' => $data['tanggal_invoice'],
                'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'],
                'status_pembayaran' => $statusPembayaran,
                'status_setor' => $statusSetor,
                'tanggal_setor' => $tanggalSetor,
                'bukti_setor' => $buktiSetorPath,
                'alasan_cancel' => $data['alasan_cancel'] ?? null,
            ]);

            if (!is_null($metodePembayaran)) {
                $invoice->metode_pembayaran = $metodePembayaran;
            }
            $invoice->ongkos_kirim = $ongkir;
            $invoice->diskon = $diskon;
            $invoice->save();

            $grandTotal = 0;
            if (!$isCancelled) {
                foreach ($data['items'] as $item) {
                    $qty = (int) $item['quantity'];
                    $harga = (float) $item['harga'];

                    // Stok tidak berubah saat edit invoice; akan dikurangi saat Surat Jalan dikirim

                    $subTotal = $qty * $harga;
                    $grandTotal += $subTotal;

                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'product_id' => $item['product_id'],
                        'batch_id' => $item['batch_id'] ?? null,
                        'quantity' => $qty,
                        'harga' => $harga,
                        'sub_total' => $subTotal,
                    ]);
                }
            }

            // Terapkan ongkos kirim (+) dan diskon (-) ke grand total
            $grandTotal = max(0, (float) $grandTotal + (float) $ongkir - (float) $diskon);
            $invoice->update(['grand_total' => $grandTotal]);

            // Update surat jalan jika invoice dibatalkan
            try {
                $suratJalans = SuratJalan::where('invoice_id', $invoice->id)->get();

                foreach ($suratJalans as $sj) {
                    if ($isCancelled) {
                        Log::info('Before update', ['status' => $sj->status]);

                        $sj->update([
                            'status'        => 'cancel',
                            'alasan_cancel' => $data['alasan_cancel'] ?? null,
                        ]);

                        $sj->refresh();
                        Log::info('After update', ['status' => $sj->status]);
                    }
                }
            } catch (\Throwable $e) {
                // optional: log error
            }

            // Update poin customer
            $isNowPaid = $invoice->status_pembayaran === 'paid';
            $oldEarnedPoints = $wasPaid ? intdiv((int) round($oldGrandTotal), 100000) : 0;
            $newEarnedPoints = $isNowPaid ? intdiv((int) round($grandTotal), 100000) : 0;

            try {
                if ($oldCustomerId !== $invoice->customer_id) {
                    if ($oldEarnedPoints > 0 && $oldCustomerId) {
                        $oldCustomer = \App\Models\Customer::find($oldCustomerId);
                        if ($oldCustomer) {
                            $curr = (int) ($oldCustomer->point ?? 0);
                            $oldCustomer->update(['point' => max(0, $curr - $oldEarnedPoints)]);
                        }
                    }
                    if ($newEarnedPoints > 0 && $invoice->customer_id) {
                        $newCustomer = \App\Models\Customer::find($invoice->customer_id);
                        if ($newCustomer) {
                            if ($newCustomer->point === null) {
                                $newCustomer->point = 0;
                                $newCustomer->save();
                            }
                            $newCustomer->increment('point', $newEarnedPoints);
                        }
                    }
                } else {
                    $customer = \App\Models\Customer::find($invoice->customer_id);
                    if ($customer) {
                        if ($customer->point === null) {
                            $customer->point = 0;
                            $customer->save();
                        }

                        if ($isNowPaid && $wasPaid) {
                            $delta = $newEarnedPoints - $oldEarnedPoints;
                            if ($delta !== 0) {
                                if ($delta > 0) {
                                    $customer->increment('point', $delta);
                                } else {
                                    $curr = (int) ($customer->point ?? 0);
                                    $customer->update(['point' => max(0, $curr + $delta)]);
                                }
                            }
                        } elseif ($isNowPaid && !$wasPaid) {
                            if ($newEarnedPoints > 0) {
                                $customer->increment('point', $newEarnedPoints);
                            }
                        } elseif (!$isNowPaid && $wasPaid) {
                            if ($oldEarnedPoints > 0) {
                                $curr = (int) ($customer->point ?? 0);
                                $customer->update(['point' => max(0, $curr - $oldEarnedPoints)]);
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
            }

            $newValues = $invoice->only([
                'customer_id',
                'user_id',
                'tanggal_invoice',
                'tanggal_jatuh_tempo',
                'status_pembayaran',
                'grand_total',
                'alasan_cancel'
            ]);
            self::logUpdate($invoice, 'Invoice', $oldValues, $newValues, 'Penjualan');

            return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully');
        });
    }
    public function show(Invoice $invoice)
    {
        $invoice->load(['items', 'customer', 'user', 'transactions', 'surat_jalan']);
        if (request()->ajax()) {
            return view('penjualan.invoices.partials.show_modal', compact('invoice'));
        }
        return view('penjualan.invoices.show', compact('invoice'));
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['items', 'customer', 'user']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('penjualan.invoices.pdf', compact('invoice'))
            ->setPaper('a4');
        $filename = 'Invoice-' . ($invoice->invoice_number ?? $invoice->id) . '.pdf';
        return $pdf->stream($filename);
    }

    public function destroy(Invoice $invoice)
    {
        self::logDelete($invoice, 'Invoice', 'Penjualan');

        // Hapus bukti setor jika ada (ini adalah bukti pembayaran dari customer)
        if ($invoice->bukti_setor && Storage::disk('public')->exists($invoice->bukti_setor)) {
            Storage::disk('public')->delete($invoice->bukti_setor);
        }

        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully');
    }

    public function indexSetor(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $statusFilter = $request->input('status_setor');

        // ✅ Tampilkan SEMUA invoice
        $base = Invoice::with(['customer', 'user'])
            // ❌ HAPUS: ->where('status_pembayaran', 'paid')
            ->when($dateFrom, fn($q) => $q->whereDate('tanggal_invoice', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('tanggal_invoice', '<=', $dateTo))
            ->when($statusFilter, function ($q) use ($statusFilter) {
                if ($statusFilter === 'sudah') {
                    $q->where('status_setor', 'sudah');
                } elseif ($statusFilter === 'belum') {
                    $q->where(function ($sq) {
                        $sq->whereNull('status_setor')->orWhere('status_setor', '!=', 'sudah');
                    });
                }
            });

        // Total hanya hitung yang PAID
        $notDepositedTotal = (clone $base)
            ->where('status_pembayaran', 'paid') // Hanya hitung yang lunas
            ->where(function ($q) {
                $q->whereNull('status_setor')->orWhere('status_setor', '!=', 'sudah');
            })
            ->sum('grand_total');

        $depositedTotal = (clone $base)
            ->where('status_pembayaran', 'paid')
            ->where('status_setor', 'sudah')
            ->sum('grand_total');

        $invoices = (clone $base)
            ->orderByDesc('created_at')
            ->paginate(20)
            ->appends($request->only('date_from', 'date_to', 'status_setor'));

        return view('penjualan.invoices.setor_index', compact(
            'invoices',
            'notDepositedTotal',
            'depositedTotal',
            'dateFrom',
            'dateTo',
            'statusFilter'
        ));
    }
    public function editSetor(Invoice $invoice)
    {
        $invoice->load(['customer', 'user']);
        return view('penjualan.invoices.setor_edit', compact('invoice'));
    }

    public function updateSetor(Request $request, Invoice $invoice)
    {
        // VALIDASI BARU: Status "sudah" wajib ada bukti setor
        $request->validate([
            'status_setor' => 'required|in:belum,sudah',
            'bukti_setor' => [
                function ($attribute, $value, $fail) use ($request, $invoice) {
                    $statusSetor = $request->input('status_setor');

                    // Jika status "sudah", wajib ada bukti (baik upload baru atau sudah ada)
                    if ($statusSetor === 'sudah') {
                        $hasNewFile = $request->hasFile('bukti_setor');
                        $hasExistingFile = !empty($invoice->bukti_setor);

                        if (!$hasNewFile && !$hasExistingFile) {
                            $fail('Bukti setor wajib diisi jika status setor adalah "Sudah".');
                        }
                    }
                },
                'nullable',
                'image',
                'max:2048'
            ]
        ], [
            'status_setor.required' => 'Status setor wajib dipilih.',
            'status_setor.in' => 'Status setor tidak valid.',
            'bukti_setor.image' => 'Bukti setor harus berupa gambar.',
            'bukti_setor.max' => 'Ukuran bukti setor maksimal 2MB.',
        ]);

        $statusSetor = $request->input('status_setor');
        $buktiSetorPath = $invoice->bukti_setor;

        // Upload file baru jika ada
        if ($request->hasFile('bukti_setor')) {
            // Hapus file lama jika ada
            if ($invoice->bukti_setor && Storage::disk('public')->exists($invoice->bukti_setor)) {
                Storage::disk('public')->delete($invoice->bukti_setor);
            }
            $buktiSetorPath = $request->file('bukti_setor')->store('setor', 'public');
        }

        // LOGIKA BARU: Status setor "sudah" HANYA jika ada bukti setor
        if ($statusSetor === 'sudah' && !empty($buktiSetorPath)) {
            $invoice->update([
                'status_setor' => 'sudah',
                'bukti_setor' => $buktiSetorPath,
                'tanggal_setor' => now()->toDateString(),
            ]);
        } elseif ($statusSetor === 'belum') {
            // Jika diubah ke "belum", tetap simpan bukti setor jika ada (untuk history)
            $invoice->update([
                'status_setor' => 'belum',
                'bukti_setor' => $buktiSetorPath,
                'tanggal_setor' => null,
            ]);
        }

        return redirect()->route('invoices.setor')->with('success', 'Setor berhasil diperbarui.');
    }
}
