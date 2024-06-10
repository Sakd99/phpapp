<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrdersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'productId' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'customerId' => 'required|integer|exists:users,id'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
