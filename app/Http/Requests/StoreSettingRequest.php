<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('whatsapp'))) {
            $phone = preg_replace('/[\s()+-]/', '', $this->input('whatsapp'));
            if (str_starts_with($phone, '0')) {
                $phone = '62'.substr($phone, 1);
            }
            $this->merge(['whatsapp' => $phone ?: null]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:160'],
            'email' => ['nullable', 'email', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'regex:/^[1-9][0-9]{7,14}$/D'],
            'about' => ['nullable', 'string', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return ['whatsapp.regex' => 'Nomor WhatsApp harus valid, misalnya 081234567890 atau +6281234567890.'];
    }
}
