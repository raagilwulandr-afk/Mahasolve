<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'metode_pembayaran' => ['required', 'string', 'max:50'],
            'bukti_pembayaran' => ['nullable', 'file', 'image', 'max:5120'], // max 5MB
        ];
    }

    public function messages(): array
    {
        return [
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'bukti_pembayaran.image' => 'Berkas bukti pembayaran harus berupa gambar (jpg, png, webp).',
            'bukti_pembayaran.max' => 'Ukuran berkas bukti pembayaran maksimal 5MB.',
        ];
    }
}
