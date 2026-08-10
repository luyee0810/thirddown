<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * An empty credits box means "leave it alone", not "set it to null".
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('credits') && trim((string) $this->input('credits')) === '') {
            $this->request->remove('credits');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'max:50'],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'parent_email' => ['nullable', 'email', 'max:255'],
            'parent_phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            // Credits may go negative when a student trains past their balance.
            'credits' => ['nullable', 'integer', 'min:-9999', 'max:9999'],
        ];
    }
}
