<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $invoice = $this->route('invoice');
        $invoiceId = is_object($invoice) ? $invoice->id : $invoice;

        return [
            // Support untuk customer baru atau existing
            'customer_type' => ['nullable', 'in:existing,new'],
            'customer_id' => ['nullable', 'required_if:customer_type,existing', 'exists:customers,id'],
            'customer_name' => ['nullable', 'required_if:customer_type,new', 'string', 'max:255'],
            'kategori_pelanggan' => ['nullable', 'required_if:customer_type,new', 'in:Toko,Konsumen,Aplikator/Tukang'],

            'user_id' => ['required', 'exists:users,id'],
            'tanggal_invoice' => ['required', 'date'],
            'tanggal_jatuh_tempo' => ['required', 'date', 'after_or_equal:tanggal_invoice'],

            // FIX: Support format lama (16 char) dan baru (17 char)
            // Format lama: INV-YYMMDD-XXXX# (16 char) - untuk backward compatibility
            // Format baru: INV-DDMMYY-XXXX# (17 char) - untuk invoice baru
            'invoice_number' => [
                'nullable',
                'string',
                'min:16',  // Minimal 16 karakter
                'max:17',  // Maksimal 17 karakter
                'regex:/^INV-\d{6}-[A-Z]{4}\d{1}$/',  // Format: INV-DDMMYY-XXXX#
                'unique:invoices,invoice_number,' . $invoiceId
            ],

            'status_pembayaran' => ['nullable', 'in:unpaid,paid,overdue,cancelled,partial'],
            'status_setor' => ['nullable', 'in:belum,sudah'],
            'metode_pembayaran' => ['nullable', 'in:tunai,transfer,qris'],
            'ongkos_kirim' => ['nullable', 'string'],
            'diskon' => ['nullable', 'string'],
            'bukti_setor' => ['nullable', 'image', 'max:5120'],
            'alasan_cancel' => ['nullable', 'string'],
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
            'invoice_number.min' => 'Nomor invoice minimal 16 karakter.',
            'invoice_number.max' => 'Nomor invoice maksimal 17 karakter.',
            'invoice_number.regex' => 'Format nomor invoice tidak valid. Harus: INV-tanggal-4huruf1angka (Contoh: INV-271224-ABCD1)',
            'invoice_number.unique' => 'Nomor invoice sudah digunakan oleh invoice lain.',
            'tanggal_jatuh_tempo.after_or_equal' => 'Tanggal jatuh tempo harus sama atau setelah tanggal invoice.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $items = (array) $this->input('items', []);

            // Get existing items untuk hitung stok yang benar
            $invoice = $this->route('invoice');
            $existingItems = [];
            if ($invoice && is_object($invoice)) {
                foreach ($invoice->items as $item) {
                    $batchId = $item->batch_id;
                    if (!isset($existingItems[$batchId])) {
                        $existingItems[$batchId] = 0;
                    }
                    $existingItems[$batchId] += $item->quantity;
                }
            }

            foreach ($items as $idx => $item) {
                $batchId = $item['batch_id'] ?? null;
                $qty = (int) ($item['quantity'] ?? 0);
                if (!$batchId || $qty <= 0) continue;

                $batch = \App\Models\ProductBatch::find($batchId);
                if (!$batch) continue;

                // Hitung stok tersedia (stok sekarang + qty yang dipakai invoice ini sebelumnya)
                $stok = (int) ($batch->quantity_sekarang ?? 0);
                $previousQty = $existingItems[$batchId] ?? 0;
                $availableStock = $stok + $previousQty;

                if ($qty > $availableStock) {
                    $v->errors()->add("items.$idx.quantity", "Qty melebihi stok tersedia (maks {$availableStock}).");
                }
            }
        });
    }
}
