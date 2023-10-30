<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:tasks,id'],
            'name' => ['required', 'string', 'max:255'],
            'phase_id' => ['required', 'integer', 'exists:phases,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'due_date' => ['required', 'date', 'after:now'], // Ensures that due_date is after the current date
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'due_date.after' => 'The due date must be a date after the current date.',
        ];
    }
}
