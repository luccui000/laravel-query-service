<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LucQLRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'table' => 'required',
            'order_by' => 'nullable|string|max:30',
            'order_direction' => 'nullable|in:ASC,DESC',
            'limit' => 'nullable|numeric|min:1',
            'page' => 'nullable|numeric|min:1',
            'search' => 'nullable',
            'map' => 'nullable'
        ];
    }
}
