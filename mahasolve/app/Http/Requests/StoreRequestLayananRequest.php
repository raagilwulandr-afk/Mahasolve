<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequestLayananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isMahasiswa();
    }

    public function rules(): array
    {
        return [
            'detail_kebutuhan' => ['required', 'string', 'min:5'],
            'kategori' => ['required', 'string', 'max:100'],
            'harga_awal' => ['nullable', 'numeric', 'min:0'],
            'deadline' => ['nullable', 'date', 'after:today'],
            'kriteria_output' => ['nullable', 'string'],
            'id_provider' => ['nullable', 'exists:provider,id_provider'],
            'lokasi_jemput' => ['nullable', 'string', 'max:255'],
            'lokasi_tujuan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'detail_kebutuhan.required' => 'Detail kebutuhan jasa wajib diisi.',
            'detail_kebutuhan.min' => 'Detail kebutuhan jasa minimal 5 karakter.',
            'kategori.required' => 'Kategori layanan wajib dipilih.',
            'deadline.after' => 'Batas waktu pengerjaan harus tanggal sesudah hari ini.',
        ];
    }
}
