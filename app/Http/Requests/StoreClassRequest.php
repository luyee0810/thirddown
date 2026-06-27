<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['single', 'regular'])],

            // Single class
            'session_date' => ['required_if:type,single', 'nullable', 'date'],

            // Regular class
            'start_date' => ['required_if:type,regular', 'nullable', 'date'],
            'end_date' => ['required_if:type,regular', 'nullable', 'date', 'after_or_equal:start_date'],
            'weekdays' => ['required_if:type,regular', 'array'],
            'weekdays.*' => ['integer', 'between:0,6'],

            // Times apply to both
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
        ];
    }
}
