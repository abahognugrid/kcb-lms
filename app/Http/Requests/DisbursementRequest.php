<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisbursementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'msisdn' => ['required', 'string', 'regex:/^256[0-9]{9}$/'],
            'amount' => ['required', 'numeric', 'min:1'],
            'reference' => ['required', 'string', 'unique:transactions,reference']
        ];
    }

    public function messages(): array
    {
        return [
            'msisdn.regex' => 'The phone number must be in the format 256XXXXXXXXX',
        ];
    }
}