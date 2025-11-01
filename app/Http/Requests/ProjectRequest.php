<?php

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
        $projectId = $this->route('project') ?? $this->route('id');

        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('projects')->ignore($projectId)
            ],
            'description' => 'required|string',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|image|max:2048',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|max:2048',
            'client' => 'nullable|string|max:255',
            'project_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:draft,published',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:project_categories,id',
            'technologies' => 'nullable|array',
            'technologies.*' => 'exists:project_technologies,id',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Il titolo del progetto è obbligatorio.',
            'slug.unique' => 'Questo slug è già utilizzato da un altro progetto.',
            'description.required' => 'La descrizione del progetto è obbligatoria.',
            'end_date.after_or_equal' => 'La data di fine deve essere successiva alla data di inizio.',
            'featured_image.max' => 'L\'immagine principale non può superare i 2MB.',
            'gallery_images.*.max' => 'Ogni immagine della galleria non può superare i 2MB.',
        ];
    }
}
