<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isProvider();
    }

    public function rules(): array
    {
        return [
            'nama_layanan' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'string', 'max:100'],
            'harga' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
            'estimasi_pengerjaan' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_layanan.required' => 'Nama layanan wajib diisi.',
            'kategori.required' => 'Kategori layanan wajib dipilih.',
            'harga.required' => 'Harga layanan wajib diisi.',
            'harga.numeric' => 'Harga layanan harus berupa angka.',
            'harga.min' => 'Harga layanan minimal Rp 0.',
        ];
    }
}
