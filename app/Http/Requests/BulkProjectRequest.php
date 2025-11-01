<?php

use Illuminate\Foundation\Http\FormRequest;

class BulkProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:projects,id',
            'publish' => 'nullable|boolean',
            'feature' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'ids.required' => 'Seleziona almeno un progetto.',
            'ids.array' => 'La selezione dei progetti non è valida.',
            'ids.*.exists' => 'Uno o più progetti selezionati non esistono.',
        ];
    }
}
