<?php

namespace App\Livewire\Admin;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectTechnology;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProjectForm extends Component
{
    use WithFileUploads;

    public $project;
    public $projectId;

    // Campi del form
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

    // File uploads
    public $featured_image;
    public $gallery_images = [];
    public $existing_featured_image = '';
    public $existing_gallery = [];

    // Relazioni
    public $selected_categories = [];
    public $selected_technologies = [];

    // Disponibili per select
    public $categories = [];
    public $technologies = [];

    // SEO
    public $meta_title = '';
    public $meta_description = '';
    public $meta_keywords = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'content' => 'nullable|string',
        'client' => 'nullable|string|max:255',
        'project_url' => 'nullable|url',
        'github_url' => 'nullable|url',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => ['required', Rule::in(['draft', 'published', 'featured'])],
        'is_featured' => 'boolean',
        'sort_order' => 'integer|min:0',
        'featured_image' => 'nullable|image|max:2048',
        'gallery_images.*' => 'image|max:2048',
        'selected_categories' => 'array',
        'selected_technologies' => 'array',
        'meta_title' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string|max:160',
        'meta_keywords' => 'nullable|string'
    ];

    public function mount($projectId = null)
    {
        $this->projectId = $projectId;
        $this->categories = ProjectCategory::ordered()->get();
        $this->technologies = ProjectTechnology::orderBy('name')->get();

        if ($projectId) {
            $this->loadProject();
        }
    }

    public function loadProject()
    {
        $this->project = Project::with(['categories', 'technologies', 'seo'])->findOrFail($this->projectId);

        $this->title = $this->project->title;
        $this->description = $this->project->description;
        $this->content = $this->project->content;
        $this->client = $this->project->client;
        $this->project_url = $this->project->project_url;
        $this->github_url = $this->project->github_url;
        $this->start_date = $this->project->start_date?->format('Y-m-d');
        $this->end_date = $this->project->end_date?->format('Y-m-d');
        $this->status = $this->project->status;
        $this->is_featured = $this->project->is_featured;
        $this->sort_order = $this->project->sort_order;

        $this->existing_featured_image = $this->project->featured_image;
        $this->existing_gallery = $this->project->gallery ?? [];

        $this->selected_categories = $this->project->categories->pluck('id')->toArray();
        $this->selected_technologies = $this->project->technologies->pluck('id')->toArray();

        if ($this->project->seo) {
            $this->meta_title = $this->project->seo->meta_title;
            $this->meta_description = $this->project->seo->meta_description;
            $this->meta_keywords = is_array($this->project->seo->meta_keywords)
                ? implode(', ', $this->project->seo->meta_keywords)
                : $this->project->seo->meta_keywords;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            // Crea o aggiorna il progetto
            if ($this->projectId) {
                $project = Project::findOrFail($this->projectId);
            } else {
                $project = new Project();
            }

            // Salva immagine featured
            $featuredImagePath = $this->existing_featured_image;
            if ($this->featured_image) {
                if ($this->existing_featured_image) {
                    Storage::disk('public')->delete($this->existing_featured_image);
                }
                $featuredImagePath = $this->featured_image->store('projects', 'public');
            }

            // Salva gallery
            $galleryPaths = $this->existing_gallery;
            if (!empty($this->gallery_images)) {
                foreach ($this->gallery_images as $image) {
                    $galleryPaths[] = $image->store('projects/gallery', 'public');
                }
            }

            // Dati del progetto
            $project->fill([
                'title' => $this->title,
                'description' => $this->description,
                'content' => $this->content,
                'featured_image' => $featuredImagePath,
                'gallery' => $galleryPaths,
                'client' => $this->client,
                'project_url' => $this->project_url,
                'github_url' => $this->github_url,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'status' => $this->status,
                'is_featured' => $this->is_featured,
                'sort_order' => $this->sort_order,
            ]);

            $project->save();

            // Sync relazioni
            $project->categories()->sync($this->selected_categories);
            $project->technologies()->sync($this->selected_technologies);

            // Salva SEO
            $project->seo()->updateOrCreate(
                ['project_id' => $project->id],
                [
                    'meta_title' => $this->meta_title,
                    'meta_description' => $this->meta_description,
                    'meta_keywords' => $this->meta_keywords ? explode(',', $this->meta_keywords) : null,
                ]
            );

            session()->flash('message', 'Progetto salvato con successo!');

            if (!$this->projectId) {
                return redirect()->route('admin.projects.edit', $project->id);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nel salvare il progetto: ' . $e->getMessage());
        }
    }

    public function removeGalleryImage($index)
    {
        if (isset($this->existing_gallery[$index])) {
            Storage::disk('public')->delete($this->existing_gallery[$index]);
            unset($this->existing_gallery[$index]);
            $this->existing_gallery = array_values($this->existing_gallery);
        }
    }

    public function render()
    {
        return view('livewire.admin.project-form');
    }
}
