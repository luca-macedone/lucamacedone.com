<?php

namespace App\Livewire\Admin\Projects;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectTechnology;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectEdit extends Component
{
    use WithFileUploads;

    public Project $project;

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

    // Upload files
    public $featured_image;
    public $gallery_images = [];
    public $existing_featured_image = '';
    public $existing_gallery = [];

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

    public function mount(Project $project)
    {
        // Carica le relazioni se non sono già caricate
        $this->project = $project->load(['categories', 'technologies']);

        // Popola i campi con i dati esistenti
        $this->title = $project->title;
        $this->description = $project->description;
        $this->content = $project->content;
        $this->client = $project->client;
        $this->project_url = $project->project_url;
        $this->github_url = $project->github_url;
        $this->start_date = $project->start_date ? $project->start_date->format('Y-m-d') : '';
        $this->end_date = $project->end_date ? $project->end_date->format('Y-m-d') : '';
        $this->status = $project->status;
        $this->is_featured = $project->is_featured;
        $this->sort_order = $project->sort_order;

        // SEO
        $this->meta_title = $project->meta_title;
        $this->meta_description = $project->meta_description;
        $this->meta_keywords = $project->meta_keywords;

        // Immagini esistenti
        $this->existing_featured_image = $project->featured_image;

        $this->existing_gallery = [];

        // Gestione gallery (potrebbe essere JSON)
        if ($project->gallery_images) {
            if (is_string($project->gallery_images)) {
                $this->existing_gallery = json_decode($project->gallery_images, true) ?? [];
            } elseif (is_array($project->gallery_images)) {
                $this->existing_gallery = $project->gallery_images;
            }
        }

        // Relazioni esistenti (con controllo null-safe)
        $this->selected_categories = $project->categories ? $project->categories->pluck('id')->toArray() : [];
        $this->selected_technologies = $project->technologies ? $project->technologies->pluck('id')->toArray() : [];
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
            // Aggiorna i campi base
            $this->project->title = $this->title;

            // Aggiorna slug solo se il titolo è cambiato
            if ($this->project->isDirty('title')) {
                $this->project->slug = $this->generateUniqueSlug($this->title, $this->project->id);
            }

            $this->project->description = $this->description;
            $this->project->content = $this->content;
            $this->project->client = $this->client;
            $this->project->project_url = $this->project_url;
            $this->project->github_url = $this->github_url;
            $this->project->start_date = $this->start_date ?: null;
            $this->project->end_date = $this->end_date ?: null;
            $this->project->status = $this->status;
            $this->project->is_featured = $this->is_featured;
            $this->project->sort_order = $this->sort_order;

            // SEO
            $this->project->meta_title = $this->meta_title ?: Str::limit($this->title, 60);
            $this->project->meta_description = $this->meta_description ?: Str::limit($this->description, 160);
            $this->project->meta_keywords = $this->meta_keywords;

            // Gestisci featured image
            if ($this->featured_image) {
                // Elimina vecchia immagine
                if ($this->existing_featured_image) {
                    Storage::disk('public')->delete($this->existing_featured_image);
                }
                // Salva nuova immagine
                $this->project->featured_image = $this->featured_image->store('projects', 'public');
                $this->existing_featured_image = $this->project->featured_image;
            }

            // Gestisci gallery images
            if (!empty($this->gallery_images)) {
                $newGalleryPaths = [];
                foreach ($this->gallery_images as $image) {
                    $newGalleryPaths[] = $image->store('projects/gallery', 'public');
                }

                // Assicurati che existing_gallery sia un array
                if (!is_array($this->existing_gallery)) {
                    $this->existing_gallery = [];
                }

                // Mantieni le immagini esistenti e aggiungi le nuove
                $allGalleryImages = array_merge($this->existing_gallery, $newGalleryPaths);
                $this->project->gallery_images = json_encode($allGalleryImages);
                $this->existing_gallery = $allGalleryImages;

                // Reset upload field
                $this->gallery_images = [];
            }

            $this->project->save();

            // Sincronizza relazioni
            $this->project->categories()->sync($this->selected_categories);
            $this->project->technologies()->sync($this->selected_technologies);

            session()->flash('message', 'Progetto aggiornato con successo!');
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nell\'aggiornamento: ' . $e->getMessage());
        }
    }

    public function toggleStatus()
    {
        $this->status = $this->status === 'published' ? 'draft' : 'published';
        $this->project->update(['status' => $this->status]);

        $message = $this->status === 'published' ? 'Progetto pubblicato' : 'Progetto impostato come bozza';
        session()->flash('message', $message);
    }

    public function toggleFeatured()
    {
        $this->is_featured = !$this->is_featured;
        $this->project->update(['is_featured' => $this->is_featured]);

        $message = $this->is_featured ? 'Progetto messo in evidenza' : 'Progetto rimosso dall\'evidenza';
        session()->flash('message', $message);
    }

    private function generateUniqueSlug($title, $excludeId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        $query = Project::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;

            $query = Project::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    public function removeExistingGalleryImage($index)
    {
        if (isset($this->existing_gallery[$index])) {
            // Elimina fisicamente il file
            Storage::disk('public')->delete($this->existing_gallery[$index]);

            // Rimuovi dall'array
            unset($this->existing_gallery[$index]);
            $this->existing_gallery = array_values($this->existing_gallery);

            // Aggiorna nel database
            $this->project->gallery_images = json_encode($this->existing_gallery);
            $this->project->save();

            session()->flash('message', 'Immagine rimossa dalla galleria');
        }
    }

    public function removeFeaturedImage()
    {
        if ($this->existing_featured_image) {
            // Elimina fisicamente il file
            Storage::disk('public')->delete($this->existing_featured_image);

            // Aggiorna nel database
            $this->project->featured_image = null;
            $this->project->save();

            // Reset variabile locale
            $this->existing_featured_image = '';

            session()->flash('message', 'Immagine in evidenza rimossa');
        }
    }

    public function removeGalleryImage($index)
    {
        unset($this->gallery_images[$index]);
        $this->gallery_images = array_values($this->gallery_images);
    }

    public function render()
    {
        return view('livewire.admin.projects.edit', [
            'categories' => ProjectCategory::orderBy('name')->get(),
            'technologies' => ProjectTechnology::orderBy('category')->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
