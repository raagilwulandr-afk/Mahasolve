<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isMahasiswa();
    }

    public function rules(): array
    {
        return [
            'rate' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rate.required' => 'Rating bintang wajib dipilih (1-5).',
            'rate.integer' => 'Rating bintang harus berupa angka bulat 1 sampai 5.',
            'rate.min' => 'Rating bintang minimal 1 bintang.',
            'rate.max' => 'Rating bintang maksimal 5 bintang.',
        ];
    }
}
