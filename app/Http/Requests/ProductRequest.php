<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:10000'],
            'price' => ['bail', 'required', 'integer', 'min:1', 'max:999999999999', 'regex:/^[0-9]+$/D'],
            'discount_price' => ['bail', 'nullable', 'integer', 'min:1', 'max:999999999999', 'regex:/^[0-9]+$/D', 'lt:price'],
            'image' => [$this->isMethod('post') ? 'required' : 'nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
