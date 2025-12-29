<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Traits\ActivityLogger;

class ProductBatchController extends Controller
{
    use ActivityLogger;

    public function index()
    {
        ProductBatch::whereDate('tanggal_expired', '<', Carbon::today())
            ->where('status', '!=', 'expired')
            ->update(['status' => 'expired']);

        ProductBatch::where('quantity_sekarang', '<=', 0)
            ->where('status', '!=', 'sold_out')
            ->update(['status' => 'sold_out']);

        $batches = ProductBatch::with('product')->get();

        return view('product-batches.index', compact('batches'));
    }

    public function create()
    {
        $products = Product::select('id', 'nama_produk', 'barcode')->get();
        return view('product-batches.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barcode' => 'nullable|string|exists:products,barcode',
            'produk'  => 'nullable|exists:products,id',
            'batch_number' => 'required|string|max:50',
            'harga_beli' => 'required|numeric',
            'tanggal_masuk' => 'required|date',
            'tanggal_expired' => 'required|date|after_or_equal:tanggal_masuk',
            'quantity_masuk' => 'required|integer|min:1',
            'quantity_sekarang' => 'required|integer|min:0',
            'supplier' => 'nullable|string|max:255',
            'status' => 'required|in:active,expired,sold_out',
        ]);

        if (!$request->filled('produk') && !$request->filled('barcode')) {
            return back()
                ->withErrors(['produk' => 'Pilih produk atau masukkan barcode'])
                ->withInput();
        }

        $product = $request->filled('produk')
            ? Product::find($request->produk)
            : Product::where('barcode', $request->barcode)->first();

        if (!$product) {
            return back()
                ->withErrors(['produk' => 'Produk tidak ditemukan'])
                ->withInput();
        }

        $batch = ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => $request->batch_number,
            'harga_beli' => $request->harga_beli,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_expired' => $request->tanggal_expired,
            'quantity_masuk' => $request->quantity_masuk,
            'quantity_sekarang' => $request->quantity_sekarang,
            'supplier' => $request->supplier,
            'status' => $request->status,
        ]);

        $batch->refreshStatus();
        $product->refreshAvailability();

        self::logCreate($batch, 'Batch Produk', 'Batch Produk');

        return redirect()
            ->route('product-batches.index')
            ->with('success', 'Batch produk berhasil ditambahkan!');
    }

    public function edit(ProductBatch $productBatch)
    {
        $products = Product::all();
        return view('product-batches.edit', compact('productBatch', 'products'));
    }

    public function update(Request $request, ProductBatch $productBatch)
    {
        $request->validate([
            'barcode'           => 'required|string|exists:products,barcode',
            'batch_number'      => 'required|string|max:50',
            'harga_beli'        => 'required|numeric',
            'tanggal_masuk'     => 'required|date',
            'tanggal_expired'   => 'required|date',
            'quantity_masuk'    => 'required|integer|min:1',
            'quantity_sekarang' => 'required|integer|min:0',
            'supplier'          => 'nullable|string|max:255',
            'status'            => 'required|in:active,expired,sold_out',
        ]);

        $oldValues = $productBatch->only([
            'product_id',
            'batch_number',
            'harga_beli',
            'tanggal_masuk',
            'tanggal_expired',
            'quantity_masuk',
            'quantity_sekarang',
            'supplier',
            'status'
        ]);

        $product = Product::where('barcode', $request->barcode)->first();

        $productBatch->update([
            'product_id'        => $product->id,
            'batch_number'      => $request->batch_number,
            'harga_beli'        => $request->harga_beli,
            'tanggal_masuk'     => $request->tanggal_masuk,
            'tanggal_expired'   => $request->tanggal_expired,
            'quantity_masuk'    => $request->quantity_masuk,
            'quantity_sekarang' => $request->quantity_sekarang,
            'supplier'          => $request->supplier,
            'status'            => $request->status,
        ]);

        $productBatch->refreshStatus();
        $product->refreshAvailability();

        $newValues = $productBatch->only([
            'product_id',
            'batch_number',
            'harga_beli',
            'tanggal_masuk',
            'tanggal_expired',
            'quantity_masuk',
            'quantity_sekarang',
            'supplier',
            'status'
        ]);
        self::logUpdate($productBatch, 'Batch Produk', $oldValues, $newValues, 'Batch Produk');

        return redirect()->route('product-batches.index')
            ->with('success', 'Batch produk berhasil diperbarui!');
    }

    public function destroy(ProductBatch $productBatch)
    {
        self::logDelete($productBatch, 'Batch Produk', 'Batch Produk');

        $productId = $productBatch->product_id;
        $productBatch->delete();

        if ($productId) {
            if ($p = Product::find($productId)) {
                $p->refreshAvailability();
            }
        }

        return redirect()->route('product-batches.index')
            ->with('success', 'Batch produk berhasil dihapus!');
    }

    public function report(Request $request)
    {
        $filter = $request->filter;
        $start = $request->start_date;
        $end = $request->end_date;
        if ($request->start_date) {
            $start = $request->start_date . '-01';
        }

        if ($request->end_date) {
            $end = \Carbon\Carbon::createFromFormat('Y-m', $request->end_date)->endOfMonth()->format('Y-m-d');
        }

        $query = ProductBatch::with('product');

        if ($filter == 'tanggal_masuk' && $start && $end) {
            $query->whereBetween('tanggal_masuk', [$start, $end]);
        }

        if ($filter == 'tanggal_expired' && $start && $end) {
            $query->whereBetween('tanggal_expired', [$start, $end]);
        }

        $batches = $query->get();

        $pdf = PDF::loadView('product-batches.report', [
            'batches' => $batches,
            'filter' => $filter,
            'start' => $start,
            'end' => $end,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('laporan_batch_produk.pdf');
    }
}
