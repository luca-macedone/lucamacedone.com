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
use Illuminate\Support\Facades\Log;

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
        $this->project = $project->load([
            'categories',
            'technologies',
            'galleryImages',
            'seo'
        ]);

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

            // Gestione corretta delle keywords
            if ($project->seo->meta_keywords) {
                if (is_array($project->seo->meta_keywords)) {
                    $this->meta_keywords = implode(', ', $project->seo->meta_keywords);
                } else {
                    // Se è salvato come JSON string nel database
                    $decoded = json_decode($project->seo->meta_keywords, true);
                    $this->meta_keywords = is_array($decoded) ? implode(', ', $decoded) : '';
                }
            }

            $this->existing_og_image = $project->seo->og_image;
        }
    }

    // FIX: Aggiunto parametro $value mancante
    public function updatedTitle($value)
    {
        // Auto-genera meta_title se vuoto e il titolo non è vuoto
        if (empty($this->meta_title) && !empty($value)) {
            $this->meta_title = Str::limit($value, 60);
        }
    }

    // FIX: Aggiunto parametro $value mancante
    public function updatedDescription($value)
    {
        // Auto-genera meta_description se vuota e la descrizione non è vuota
        if (empty($this->meta_description) && !empty($value)) {
            $this->meta_description = Str::limit(strip_tags($value), 160);
        }
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Aggiorna i dati base del progetto
            $this->project->update([
                'title' => $this->title,
                'slug' => $this->project->slug, // Mantieni lo slug esistente o genera nuovo se il titolo è cambiato
                'description' => $this->description,
                'content' => $this->content,
                'client' => $this->client,
                'project_url' => $this->project_url,
                'github_url' => $this->github_url,
                'start_date' => $this->start_date ?: null,
                'end_date' => $this->end_date ?: null,
                'status' => $this->status,
                'is_featured' => $this->is_featured,
                'sort_order' => $this->sort_order,
            ]);

            // Gestisci featured image
            if ($this->featured_image) {
                // Elimina vecchia immagine se esiste
                if ($this->existing_featured_image) {
                    Storage::disk('public')->delete($this->existing_featured_image);
                }
                $this->project->featured_image = $this->featured_image->store('projects', 'public');
                $this->project->save();
            }

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
                'meta_title' => $this->meta_title ?: null,
                'meta_description' => $this->meta_description ?: null,
            ];

            // Converti keywords string in array JSON
            if ($this->meta_keywords) {
                $keywords = array_map('trim', explode(',', $this->meta_keywords));
                $seoData['meta_keywords'] = json_encode($keywords);
            } else {
                $seoData['meta_keywords'] = null;
            }

            // Gestisci OG image
            if ($this->og_image) {
                // Elimina vecchia OG image se diversa da featured image
                if ($this->existing_og_image && $this->existing_og_image !== $this->project->featured_image) {
                    Storage::disk('public')->delete($this->existing_og_image);
                }
                $seoData['og_image'] = $this->og_image->store('projects/og', 'public');
            } else {
                // Mantieni l'esistente o usa featured come fallback
                $seoData['og_image'] = $this->existing_og_image ?: $this->project->featured_image;
            }

            // Crea o aggiorna record SEO
            ProjectSeo::updateOrCreate(
                ['project_id' => $this->project->id],
                $seoData
            );

            // Sincronizza relazioni many-to-many con timestamps
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

            Log::error('Errore aggiornamento progetto: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

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

    public function removeExistingGalleryImage($imageId)
    {
        $this->images_to_delete[] = $imageId;
        $this->existing_gallery_images = array_filter($this->existing_gallery_images, function ($img) use ($imageId) {
            return $img['id'] !== $imageId;
        });
    }

    public function removeNewGalleryImage($index)
    {
        unset($this->new_gallery_images[$index]);
        $this->new_gallery_images = array_values($this->new_gallery_images);
    }

    public function render()
    {
        return view('livewire.admin.projects.edit', [
            'categories' => ProjectCategory::orderBy('name')->get(),
            'technologies' => ProjectTechnology::orderBy('category')->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
