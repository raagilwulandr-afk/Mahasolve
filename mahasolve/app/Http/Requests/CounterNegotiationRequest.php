<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CounterNegotiationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'harga_tawaran' => ['required', 'numeric', 'min:1000'],
            'pesan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'harga_tawaran.required' => 'Nominal harga penawaran wajib diisi.',
            'harga_tawaran.numeric' => 'Nominal harga penawaran harus berupa angka.',
            'harga_tawaran.min' => 'Nominal harga penawaran minimal Rp 1.000.',
        ];
    }
}
