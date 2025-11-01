<?php

use Illuminate\Foundation\Http\FormRequest;

class BulkProjectRequest extends FormRequest
{
    public function rules()
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:projects,id',
            'publish' => 'boolean',
            'feature' => 'boolean',
        ];
    }
}
