<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            // 'priority' => 'nullable|in:low,medium,high',
            // 'categories' => 'nullable|array',
            // 'categories.*' => 'exists:categories,id',
            // 'labels' => 'nullable|array',
            // 'labels.*' => 'exists:labels,id',
        ];
    }
}
