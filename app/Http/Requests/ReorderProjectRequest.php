<?php

use Illuminate\Foundation\Http\FormRequest;

class ReorderProjectRequest extends FormRequest
{
    public function rules()
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:projects,id',
        ];
    }
}
