<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Traits\ActivityLogger;

class CustomerController extends Controller
{
    use ActivityLogger;

    public function index()
    {
        $customers = Customer::orderBy('nama_customer', 'asc')->get();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string|max:255',
            'point' => 'nullable|integer',
            'kategori_pelanggan' => 'required|in:Toko,Konsumen,Aplikator/Tukang',
        ]);

        $customer = Customer::create([
            'nama_customer' => $request->nama_customer,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'point' => $request->point,
            'kategori_pelanggan' => $request->kategori_pelanggan,
        ]);

        // ← PARAMETER KE-3 ADALAH CATEGORY
        self::logCreate($customer, 'Pelanggan', 'Pelanggan');

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function show($id)
    {
        $customer = Customer::with([
            'invoices.invoiceItems.product',
            'invoices.invoiceItems.batch'
        ])->findOrFail($id);

        foreach ($customer->invoices as $inv) {
            $inv->items_json = $inv->invoiceItems->map(function ($item) {
                return [
                    'batch_number' => $item->batch->batch_number ?? '-',
                    'produk'       => $item->product->nama_produk,
                    'qty'          => $item->quantity,
                    'harga'        => $item->harga,
                    'subtotal'     => $item->quantity * $item->harga,
                ];
            });
        }

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'no_hp' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'alamat' => 'required|string|max:255',
            'point' => 'nullable|integer',
            'kategori_pelanggan' => 'required|in:Toko,Konsumen,Aplikator/Tukang',
        ]);

        $oldValues = $customer->only(['nama_customer', 'no_hp', 'email', 'alamat', 'point']);

        $customer->update([
            'nama_customer' => $request->nama_customer,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'point' => $request->point,
            'kategori_pelanggan' => $request->kategori_pelanggan,
        ]);

        $newValues = $customer->only(['nama_customer', 'no_hp', 'email', 'alamat', 'point']);

        // ← PARAMETER KE-5 ADALAH CATEGORY
        self::logUpdate($customer, 'Pelanggan', $oldValues, $newValues, 'Pelanggan');

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        // ← PARAMETER KE-3 ADALAH CATEGORY
        self::logDelete($customer, 'Pelanggan', 'Pelanggan');

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
