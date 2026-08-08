<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isProvider();
    }

    public function rules(): array
    {
        return [
            'status_pesanan' => ['required', 'string'],
            'pesan_progress' => ['nullable', 'string', 'max:1000'],
            'dokumen' => ['nullable', 'string', 'max:500'],
            'file_dokumen' => ['nullable', 'file', 'max:10240'], // 10MB max
        ];
    }

    public function messages(): array
    {
        return [
            'status_pesanan.required' => 'Status pesanan wajib dipilih.',
            'file_dokumen.max' => 'Ukuran file dokumen deliverable maksimal 10MB.',
        ];
    }
}
