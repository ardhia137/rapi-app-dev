<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'exists:invoices,id'],
            'surat_jalan_id' => ['nullable', 'exists:surat_jalans,id'],
            'tanggal_pembayaran' => ['required', 'date'],
            'jumlah_bayar' => ['required', 'numeric', 'min:1'],
            'metode_pembayaran' => ['required', 'in:cash,transfer,other'],
            'bukti_pembayaran' => ['nullable', 'string'],
        ];
    }
}
