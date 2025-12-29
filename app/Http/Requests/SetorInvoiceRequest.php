<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetorInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_setor' => ['required', 'in:sudah,belum'],
            'bukti_setor' => ['nullable', 'image', 'max:5120'], // max 5MB
        ];
    }
}
