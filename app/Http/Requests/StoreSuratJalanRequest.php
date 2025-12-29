<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Invoice;

class StoreSuratJalanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // 🔥 Deteksi apakah ini update atau create
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $suratJalanId = $this->route('surat_jalan') ? $this->route('surat_jalan')->id : null;

        // 🔥 Rules berbeda untuk CREATE dan UPDATE
        if ($isUpdate) {
            // SAAT UPDATE: validasi field yang bisa diubah termasuk tanggal
            return [
                'tanggal' => 'required|date',
                'status' => 'required|in:belum dikirim,sudah dikirim,cancel',
                'bukti_pengiriman' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png',
                    'max:2048'
                ],
                'alasan_cancel' => 'nullable|required_if:status,cancel|string|max:500',
            ];
        }

        // SAAT CREATE: validasi semua field
        return [
            'nomor_surat_jalan' => [
                'required',
                'string',
                'max:255',
                'unique:surat_jalans,nomor_surat_jalan'
            ],
            'customer_id' => 'required|exists:customers,id',
            'invoice_id' => 'required|exists:invoices,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:belum dikirim,sudah dikirim,cancel',
            'bukti_pengiriman' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('status') === 'sudah dikirim' && !$this->hasFile('bukti_pengiriman')) {
                        $fail('Bukti pengiriman wajib diisi jika status "Sudah Dikirim".');
                    }
                },
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048'
            ],
            'alasan_cancel' => 'nullable|required_if:status,cancel|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_surat_jalan.required' => 'Nomor surat jalan wajib diisi.',
            'nomor_surat_jalan.unique' => 'Nomor surat jalan sudah digunakan.',
            'nomor_surat_jalan.max' => 'Nomor surat jalan maksimal 255 karakter.',
            'customer_id.required' => 'Customer wajib dipilih.',
            'customer_id.exists' => 'Customer tidak ditemukan.',
            'invoice_id.required' => 'Invoice wajib dipilih.',
            'invoice_id.exists' => 'Invoice tidak ditemukan.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'status.required' => 'Status pengiriman wajib dipilih.',
            'status.in' => 'Status pengiriman tidak valid.',
            'bukti_pengiriman.image' => 'Bukti pengiriman harus berupa gambar.',
            'bukti_pengiriman.mimes' => 'Format bukti pengiriman harus JPG, PNG, atau JPEG.',
            'bukti_pengiriman.max' => 'Ukuran bukti pengiriman maksimal 2MB.',
            'alasan_cancel.required_if' => 'Alasan pembatalan wajib diisi jika status dibatalkan.',
            'alasan_cancel.max' => 'Alasan pembatalan maksimal 500 karakter.',
        ];
    }

    public function withValidator($validator)
    {
        // 🔥 Hanya jalankan validasi tanggal invoice saat CREATE
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        if (!$isUpdate) {
            $validator->after(function ($v) {
                $invoiceId = $this->input('invoice_id');
                $tanggalSj = $this->input('tanggal');
                if (!$invoiceId || !$tanggalSj) return;

                $invoice = Invoice::find($invoiceId);
                if (!$invoice) return;

                $invoiceDate = $invoice->tanggal_invoice;
                if ($invoiceDate && strtotime($tanggalSj) < strtotime($invoiceDate)) {
                    $v->errors()->add('tanggal', 'Tanggal surat jalan tidak boleh kurang dari tanggal invoice (' . $invoiceDate . ').');
                }
            });
        }
    }
}
