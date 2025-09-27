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
        $this->project = $project;

        // Popola i campi con i dati esistenti
        $this->title = $project->title;
        $this->description = $project->description;
        $this->content = $project->content;
        $this->client = $project->client;
        $this->project_url = $project->project_url;
        $this->github_url = $project->github_url;
        $this->start_date = $project->start_date;
        $this->end_date = $project->end_date;
        $this->status = $project->status;
        $this->is_featured = $project->is_featured;
        $this->sort_order = $project->sort_order;
        $this->meta_title = $project->meta_title;
        $this->meta_description = $project->meta_description;
        $this->meta_keywords = $project->meta_keywords;

        // Immagini esistenti
        $this->existing_featured_image = $project->featured_image;
        $this->existing_gallery = $project->gallery_images ?? [];

        // Relazioni esistenti
        $this->selected_categories = $project->categories->pluck('id')->toArray();
        $this->selected_technologies = $project->technologies->pluck('id')->toArray();
    }

    public function update()
    {
        $this->validate();

        try {
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
            $this->project->start_date = $this->start_date;
            $this->project->end_date = $this->end_date;
            $this->project->status = $this->status;
            $this->project->is_featured = $this->is_featured;
            $this->project->sort_order = $this->sort_order;
            $this->project->meta_title = $this->meta_title;
            $this->project->meta_description = $this->meta_description;
            $this->project->meta_keywords = $this->meta_keywords;

            // Gestisci featured image
            if ($this->featured_image) {
                // Elimina vecchia immagine
                if ($this->existing_featured_image) {
                    Storage::disk('public')->delete($this->existing_featured_image);
                }
                // Salva nuova immagine
                $this->project->featured_image = $this->featured_image->store('projects', 'public');
            }

            // Gestisci gallery images
            if (!empty($this->gallery_images)) {
                $newGalleryPaths = [];
                foreach ($this->gallery_images as $image) {
                    $newGalleryPaths[] = $image->store('projects/gallery', 'public');
                }

                // Mantieni le immagini esistenti e aggiungi le nuove
                $allGalleryImages = array_merge($this->existing_gallery, $newGalleryPaths);
                $this->project->gallery_images = $allGalleryImages;
            }

            $this->project->save();

            // Sincronizza relazioni
            $this->project->categories()->sync($this->selected_categories);
            $this->project->technologies()->sync($this->selected_technologies);

            $this->dispatch('project-updated', [
                'message' => 'Progetto aggiornato con successo!'
            ]);

            // Reset file uploads
            $this->featured_image = null;
            $this->gallery_images = [];
        } catch (\Exception $e) {
            $this->dispatch('project-error', [
                'message' => 'Errore nell\'aggiornamento: ' . $e->getMessage()
            ]);
        }
    }

    public function delete()
    {
        try {
            // Elimina file associati
            if ($this->project->featured_image) {
                Storage::disk('public')->delete($this->project->featured_image);
            }

            if ($this->project->gallery_images) {
                foreach ($this->project->gallery_images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $this->project->delete();

            $this->dispatch('project-deleted', [
                'message' => 'Progetto eliminato con successo!'
            ]);

            return redirect()->route('admin.projects.index');
        } catch (\Exception $e) {
            $this->dispatch('project-error', [
                'message' => 'Errore nell\'eliminazione: ' . $e->getMessage()
            ]);
        }
    }

    public function removeFeaturedImage()
    {
        if ($this->existing_featured_image) {
            Storage::disk('public')->delete($this->existing_featured_image);
            $this->project->update(['featured_image' => null]);
            $this->existing_featured_image = '';

            $this->dispatch('image-removed', [
                'message' => 'Immagine rimossa con successo!'
            ]);
        }
    }

    public function removeGalleryImage($index)
    {
        if (isset($this->existing_gallery[$index])) {
            Storage::disk('public')->delete($this->existing_gallery[$index]);
            unset($this->existing_gallery[$index]);
            $this->existing_gallery = array_values($this->existing_gallery);

            $this->project->update(['gallery_images' => $this->existing_gallery]);

            $this->dispatch('image-removed', [
                'message' => 'Immagine rimossa dalla galleria!'
            ]);
        }
    }

    public function removeNewGalleryImage($index)
    {
        unset($this->gallery_images[$index]);
        $this->gallery_images = array_values($this->gallery_images);
    }

    public function toggleStatus()
    {
        $this->status = $this->status === 'published' ? 'draft' : 'published';

        $this->project->update(['status' => $this->status]);

        $this->dispatch('status-changed', [
            'message' => "Status cambiato a: {$this->status}"
        ]);
    }

    public function toggleFeatured()
    {
        $this->is_featured = !$this->is_featured;

        $this->project->update(['is_featured' => $this->is_featured]);

        $message = $this->is_featured ? 'Progetto messo in evidenza' : 'Progetto rimosso dall\'evidenza';
        $this->dispatch('featured-changed', [
            'message' => $message
        ]);
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

    public function render()
    {
        return view('livewire.admin.projects.project-edit', [
            'categories' => ProjectCategory::ordered()->get(),
            'technologies' => ProjectTechnology::ordered()->get(),
        ])->layout('layouts.app');
    }
}
