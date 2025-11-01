<?php

use Illuminate\Foundation\Http\FormRequest;

class ReorderProjectRequest extends FormRequest
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
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'ids.required' => 'L\'ordine dei progetti è richiesto.',
            'ids.array' => 'Il formato dell\'ordine non è valido.',
            'ids.*.exists' => 'Uno o più progetti nell\'ordinamento non esistono.',
        ];
    }
}
