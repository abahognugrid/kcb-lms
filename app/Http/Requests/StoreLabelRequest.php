<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLabelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $labelId = $this->label ? $this->label->id : null;
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('labels', 'name')->ignore($labelId)
            ],
            'is_visible' => 'sometimes|boolean'
        ];
    }
}
