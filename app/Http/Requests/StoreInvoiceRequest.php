<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ProductBatch;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Pilihan jenis pelanggan: existing atau new
            'customer_type' => ['required', 'in:existing,new'],

            // ✅ Format invoice number - INV-DDMMYY-XXXX# (16 karakter)
            'invoice_number' => [
                'nullable',
                'string',
                'size:16',  // 16 karakter
                'regex:/^INV-\d{6}-[A-Z]{4}\d{1}$/',  // Format: INV-271225-ABCD1 (Tanggal-Bulan-Tahun)
                'unique:invoices,invoice_number'
            ],

            // Jika memilih pelanggan terdaftar, wajib isi customer_id
            'customer_id' => ['nullable', 'required_if:customer_type,existing', 'exists:customers,id'],
            // Jika memilih pelanggan baru, wajib isi nama & kategori
            'customer_name' => ['nullable', 'required_if:customer_type,new', 'string', 'max:255'],
            'kategori_pelanggan' => ['nullable', 'required_if:customer_type,new', 'in:Toko,Konsumen,Aplikator/Tukang'],
            'user_id' => ['required', 'exists:users,id'],
            'tanggal_invoice' => ['required', 'date'],
            'tanggal_jatuh_tempo' => ['required', 'date', 'after_or_equal:tanggal_invoice'],
            'status_pembayaran' => ['nullable', 'in:unpaid,paid,overdue,cancelled,partial'],
            'status_setor' => ['nullable', 'in:belum,sudah'],
            // File upload untuk bukti setor
            'bukti_setor' => ['nullable', 'image', 'max:5120'], // maks 5MB
            'alasan_cancel' => ['nullable', 'string'],

            // Kolom tambahan dari form
            'metode_pembayaran' => ['nullable', 'in:tunai,transfer,qris'],
            'ongkos_kirim' => ['nullable', 'string'], // dikirim sebagai angka string; diparse di controller
            'diskon' => ['nullable', 'string'], // dikirim sebagai angka string; diparse di controller

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.batch_id' => ['required', 'exists:product_batches,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.harga' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_number.size' => 'Nomor invoice harus 16 karakter (Format: INV-DDMMYY-XXXX#).',
            'invoice_number.regex' => 'Format nomor invoice tidak valid. Harus: INV-tanggal-4huruf1angka (Contoh: INV-271225-ABCD1)',
            'invoice_number.unique' => 'Nomor invoice sudah digunakan.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $items = (array) $this->input('items', []);
            foreach ($items as $idx => $item) {
                $batchId = $item['batch_id'] ?? null;
                $qty = (int) ($item['quantity'] ?? 0);
                if (!$batchId || $qty <= 0) continue;
                $batch = ProductBatch::find($batchId);
                if (!$batch) continue;
                $stok = (int) ($batch->quantity_sekarang ?? 0);
                if ($qty > $stok) {
                    $v->errors()->add("items.$idx.quantity", "Qty melebihi stok tersedia (maks ${stok}).");
                }
            }
        });
    }
}
