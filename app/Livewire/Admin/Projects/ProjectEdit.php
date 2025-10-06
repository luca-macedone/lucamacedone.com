<?php

namespace App\Livewire\Admin\Projects;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectTechnology;
use App\Models\ProjectImage;
use App\Models\ProjectSeo;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectEdit extends Component
{
    use WithFileUploads;

    // Oggetto progetto
    public Project $project;

    // Campi base del progetto
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
    public $new_gallery_images = [];

    // Immagini esistenti
    public $existing_featured_image;
    public $existing_gallery_images = [];
    public $images_to_delete = [];

    // Relazioni
    public $selected_categories = [];
    public $selected_technologies = [];

    // SEO fields
    public $meta_title = '';
    public $meta_description = '';
    public $meta_keywords = '';
    public $og_image;
    public $existing_og_image;

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'description' => 'required|min:10',
        'content' => 'nullable',
        'client' => 'nullable|max:255',
        'project_url' => 'nullable|url|max:255',
        'github_url' => 'nullable|url|max:255',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'required|in:draft,published,featured',
        'is_featured' => 'boolean',
        'sort_order' => 'integer|min:0',
        'featured_image' => 'nullable|image|max:2048',
        'new_gallery_images.*' => 'image|max:2048',
        'selected_categories' => 'array',
        'selected_categories.*' => 'exists:project_categories,id',
        'selected_technologies' => 'array',
        'selected_technologies.*' => 'exists:project_technologies,id',
        'meta_title' => 'nullable|max:60',
        'meta_description' => 'nullable|max:160',
        'meta_keywords' => 'nullable|max:255',
        'og_image' => 'nullable|image|max:2048',
    ];

    protected $messages = [
        'title.required' => 'Il titolo è obbligatorio.',
        'description.required' => 'La descrizione è obbligatoria.',
        'featured_image.image' => 'Il file deve essere un\'immagine.',
        'featured_image.max' => 'L\'immagine non può superare i 2MB.',
        'new_gallery_images.*.image' => 'Tutti i file devono essere immagini.',
        'new_gallery_images.*.max' => 'Le immagini non possono superare i 2MB.',
        'end_date.after_or_equal' => 'La data di fine deve essere successiva o uguale alla data di inizio.',
    ];

    public function mount(Project $project)
    {
        // Carica il progetto con tutte le relazioni necessarie
        $this->project = $project->load(['categories', 'technologies', 'galleryImages', 'seo']);

        // Popola i campi base
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

        // Immagini esistenti
        $this->existing_featured_image = $project->featured_image;
        $this->existing_gallery_images = $project->galleryImages->toArray();

        // Relazioni esistenti
        $this->selected_categories = $project->categories->pluck('id')->toArray();
        $this->selected_technologies = $project->technologies->pluck('id')->toArray();

        // SEO data
        if ($project->seo) {
            $this->meta_title = $project->seo->meta_title;
            $this->meta_description = $project->seo->meta_description;
            $this->meta_keywords = $project->seo->keywords_string; // Converte array in stringa
            $this->existing_og_image = $project->seo->og_image;
        } else {
            // Se non esistono dati SEO, usa valori di default
            $this->meta_title = Str::limit($project->title, 60);
            $this->meta_description = Str::limit(strip_tags($project->description), 160);
        }
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
            $this->meta_description = Str::limit(strip_tags($this->description), 160);
        }
    }

    public function removeExistingGalleryImage($imageId)
    {
        // Aggiungi l'ID dell'immagine da eliminare
        $this->images_to_delete[] = $imageId;

        // Rimuovi dall'array delle immagini esistenti per l'interfaccia
        $this->existing_gallery_images = array_filter(
            $this->existing_gallery_images,
            fn($img) => $img['id'] != $imageId
        );
    }

    public function removeNewGalleryImage($index)
    {
        unset($this->new_gallery_images[$index]);
        $this->new_gallery_images = array_values($this->new_gallery_images);
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Aggiorna i campi base del progetto
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

            // Gestisci featured image
            if ($this->featured_image) {
                // Elimina vecchia immagine se esiste
                if ($this->existing_featured_image) {
                    Storage::disk('public')->delete($this->existing_featured_image);
                }
                // Salva nuova immagine
                $this->project->featured_image = $this->featured_image->store('projects', 'public');
            }

            $this->project->save();

            // Gestisci eliminazione immagini della galleria
            if (!empty($this->images_to_delete)) {
                $imagesToDelete = ProjectImage::whereIn('id', $this->images_to_delete)
                    ->where('project_id', $this->project->id)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    // Elimina file fisico
                    if (Storage::disk('public')->exists($image->filename)) {
                        Storage::disk('public')->delete($image->filename);
                    }
                    // Elimina record dal database
                    $image->delete();
                }
            }

            // Aggiungi nuove immagini alla galleria
            if (!empty($this->new_gallery_images)) {
                $currentMaxOrder = ProjectImage::where('project_id', $this->project->id)
                    ->max('sort_order') ?? -1;

                foreach ($this->new_gallery_images as $index => $image) {
                    $imagePath = $image->store('projects/gallery', 'public');

                    ProjectImage::create([
                        'project_id' => $this->project->id,
                        'filename' => $imagePath,
                        'original_name' => $image->getClientOriginalName(),
                        'alt_text' => $this->title . ' - Gallery Image',
                        'caption' => null,
                        'sort_order' => $currentMaxOrder + $index + 1,
                        'type' => 'gallery'
                    ]);
                }
            }

            // Gestisci dati SEO
            $seoData = [
                'meta_title' => $this->meta_title ?: Str::limit($this->title, 60),
                'meta_description' => $this->meta_description ?: Str::limit(strip_tags($this->description), 160),
                'meta_keywords' => null,
            ];

            // Converti keywords string in array
            if ($this->meta_keywords) {
                $seoData['meta_keywords'] = json_encode(array_map('trim', explode(',', $this->meta_keywords)));
            }

            // Gestisci OG image
            if ($this->og_image) {
                // Elimina vecchia OG image se diversa da featured image
                if ($this->existing_og_image && $this->existing_og_image !== $this->project->featured_image) {
                    Storage::disk('public')->delete($this->existing_og_image);
                }
                $seoData['og_image'] = $this->og_image->store('projects/og', 'public');
            } elseif (!$this->existing_og_image && $this->project->featured_image) {
                // Usa featured image come fallback per OG image
                $seoData['og_image'] = $this->project->featured_image;
            }

            // Crea o aggiorna record SEO
            ProjectSeo::updateOrCreate(
                ['project_id' => $this->project->id],
                $seoData
            );

            // Sincronizza relazioni many-to-many
            $this->project->categories()->sync($this->selected_categories);
            $this->project->technologies()->sync($this->selected_technologies);

            DB::commit();

            // Reset campi upload dopo il salvataggio
            $this->featured_image = null;
            $this->new_gallery_images = [];
            $this->og_image = null;
            $this->images_to_delete = [];

            // Ricarica le immagini della galleria
            $this->existing_gallery_images = $this->project->fresh()->galleryImages->toArray();

            session()->flash('message', 'Progetto aggiornato con successo!');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Errore nell\'aggiornamento: ' . $e->getMessage());
        }
    }

    public function saveAndClose()
    {
        $this->save();

        if (!session()->has('error')) {
            return redirect()->route('admin.projects.index');
        }
    }

    public function toggleStatus()
    {
        $this->status = $this->status === 'published' ? 'draft' : 'published';
        $this->save();
    }

    public function toggleFeatured()
    {
        $this->is_featured = !$this->is_featured;
        $this->save();
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

    public function reorderGalleryImages($orderedIds)
    {
        // Riordina le immagini della galleria
        foreach ($orderedIds as $order => $imageId) {
            ProjectImage::where('id', $imageId)
                ->where('project_id', $this->project->id)
                ->update(['sort_order' => $order]);
        }

        // Ricarica le immagini nell'ordine aggiornato
        $this->existing_gallery_images = $this->project->fresh()->galleryImages->toArray();
    }

    public function render()
    {
        return view('livewire.admin.projects.edit', [
            'categories' => ProjectCategory::orderBy('name')->get(),
            'technologies' => ProjectTechnology::orderBy('category')->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
