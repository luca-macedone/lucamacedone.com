<?php

namespace App\Livewire\Admin\Projects;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectTechnology;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectCreate extends Component
{
    use WithFileUploads;

    // Campi base
    public $title = '';
    public $description = '';
    public $content = '';
    public $client = '';
    public $project_url = '';
    public $github_url = '';
    public $start_date = '';
    public $end_date = '';
    public $status = 'draft';
    public $is_featured = false;
    public $sort_order = 0;

    // Upload files
    public $featured_image;
    public $gallery_images = [];

    // Relazioni
    public $selected_categories = [];
    public $selected_technologies = [];

    // SEO
    public $meta_title = '';
    public $meta_description = '';
    public $meta_keywords = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|max:1000',
        'content' => 'nullable|string',
        'client' => 'nullable|string|max:255',
        'project_url' => 'nullable|url',
        'github_url' => 'nullable|url',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'required|in:draft,published,featured',
        'is_featured' => 'boolean',
        'sort_order' => 'integer|min:0',
        'featured_image' => 'nullable|image|max:2048',
        'gallery_images.*' => 'nullable|image|max:2048',
        'selected_categories' => 'array',
        'selected_technologies' => 'array',
        'meta_title' => 'nullable|string|max:60',
        'meta_description' => 'nullable|string|max:160',
        'meta_keywords' => 'nullable|string|max:255',
    ];

    protected $messages = [
        'title.required' => 'Il titolo è obbligatorio.',
        'description.required' => 'La descrizione è obbligatoria.',
        'featured_image.image' => 'Il file deve essere un\'immagine.',
        'featured_image.max' => 'L\'immagine non può superare i 2MB.',
        'gallery_images.*.image' => 'Tutti i file devono essere immagini.',
        'gallery_images.*.max' => 'Le immagini non possono superare i 2MB.',
        'end_date.after_or_equal' => 'La data di fine deve essere successiva o uguale alla data di inizio.',
    ];

    public function mount()
    {
        // Imposta valori default
        $this->sort_order = Project::max('sort_order') + 1 ?? 0;
    }

    public function updatedTitle()
    {
        // Auto-genera meta_title se vuoto
        if (empty($this->meta_title)) {
            $this->meta_title = Str::limit($this->title, 60);
        }
    }

    public function updatedDescription()
    {
        // Auto-genera meta_description se vuota
        if (empty($this->meta_description)) {
            $this->meta_description = Str::limit($this->description, 160);
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $project = new Project();

            // Dati base
            $project->title = $this->title;
            $project->slug = $this->generateUniqueSlug($this->title);
            $project->description = $this->description;
            $project->content = $this->content;
            $project->client = $this->client;
            $project->project_url = $this->project_url;
            $project->github_url = $this->github_url;
            $project->start_date = $this->start_date ?: null;
            $project->end_date = $this->end_date ?: null;
            $project->status = $this->status;
            $project->is_featured = $this->is_featured;
            $project->sort_order = $this->sort_order;

            // SEO
            $project->meta_title = $this->meta_title ?: Str::limit($this->title, 60);
            $project->meta_description = $this->meta_description ?: Str::limit($this->description, 160);
            $project->meta_keywords = $this->meta_keywords;

            // Upload featured image
            if ($this->featured_image) {
                $project->featured_image = $this->featured_image->store('projects', 'public');
            }

            // Upload gallery images
            if (!empty($this->gallery_images)) {
                $galleryPaths = [];
                foreach ($this->gallery_images as $image) {
                    $galleryPaths[] = $image->store('projects/gallery', 'public');
                }
                $project->gallery_images = json_encode($galleryPaths);
            }

            $project->save();

            // Sincronizza relazioni
            if (!empty($this->selected_categories)) {
                $project->categories()->sync($this->selected_categories);
            }

            if (!empty($this->selected_technologies)) {
                $project->technologies()->sync($this->selected_technologies);
            }

            session()->flash('message', 'Progetto creato con successo!');

            // Redirect alla lista progetti
            return redirect()->route('admin.projects.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nel salvataggio: ' . $e->getMessage());
        }
    }

    public function saveAsDraft()
    {
        $this->status = 'draft';
        $this->save();
    }

    public function saveAndPublish()
    {
        $this->status = 'published';
        $this->save();
    }

    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (Project::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function removeGalleryImage($index)
    {
        unset($this->gallery_images[$index]);
        $this->gallery_images = array_values($this->gallery_images);
    }

    public function render()
    {
        return view('livewire.admin.projects.create', [
            'categories' => ProjectCategory::orderBy('name')->get(),
            'technologies' => ProjectTechnology::orderBy('category')->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
