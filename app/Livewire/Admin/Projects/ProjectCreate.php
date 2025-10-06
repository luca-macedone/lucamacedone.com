<?php

namespace App\Livewire\Admin\Projects;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectTechnology;
use App\Models\ProjectImage;
use App\Models\ProjectSeo;
use App\Services\ImageService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProjectCreate extends Component
{
    use WithFileUploads;

    // Form fields
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

    // Relations
    public $selected_categories = [];
    public $selected_technologies = [];

    // SEO fields
    public $meta_title = '';
    public $meta_description = '';
    public $meta_keywords = '';
    public $og_image;

    // UI state
    public $suggestedSlug = '';
    public $slugAvailable = true;
    public $isSaving = false;

    // Cached data
    protected $categories;
    protected $technologies;

    protected function rules()
    {
        return [
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
            'featured_image' => 'nullable|image|max:' . config('projects.image.max_size', 2048),
            'gallery_images.*' => 'image|max:' . config('projects.image.max_size', 2048),
            'selected_categories' => 'array',
            'selected_categories.*' => 'exists:project_categories,id',
            'selected_technologies' => 'array',
            'selected_technologies.*' => 'exists:project_technologies,id',
            'meta_title' => 'nullable|max:60',
            'meta_description' => 'nullable|max:160',
            'meta_keywords' => 'nullable|max:255',
            'og_image' => 'nullable|image|max:' . config('projects.image.max_size', 2048),
        ];
    }

    protected $messages = [
        'title.required' => 'Il titolo è obbligatorio.',
        'title.min' => 'Il titolo deve essere almeno di 3 caratteri.',
        'description.required' => 'La descrizione è obbligatoria.',
        'description.min' => 'La descrizione deve essere almeno di 10 caratteri.',
        'featured_image.image' => 'Il file deve essere un\'immagine.',
        'featured_image.max' => 'L\'immagine non può superare i 2MB.',
        'gallery_images.*.image' => 'Tutti i file devono essere immagini.',
        'gallery_images.*.max' => 'Le immagini non possono superare i 2MB.',
        'end_date.after_or_equal' => 'La data di fine deve essere successiva o uguale alla data di inizio.',
    ];

    public function mount()
    {
        // Calcola il prossimo sort_order disponibile
        $this->sort_order = (Project::max('sort_order') ?? 0) + 1;

        // Pre-carica categorie e tecnologie
        $this->loadRelationalData();
    }

    protected function loadRelationalData()
    {
        $this->categories = Cache::remember('project_categories', 3600, function () {
            return ProjectCategory::orderBy('sort_order')->orderBy('name')->get();
        });

        $this->technologies = Cache::remember('project_technologies', 3600, function () {
            return ProjectTechnology::orderBy('category')->orderBy('name')->get();
        });
    }

    public function updatedTitle($value)
    {
        // Valida solo il campo title
        $this->validateOnly('title');

        if (!empty($value)) {
            // Genera slug suggestion
            $this->suggestedSlug = Project::generateUniqueSlug($value);
            $this->slugAvailable = true;

            // Auto-genera meta_title se vuoto
            if (empty($this->meta_title)) {
                $this->meta_title = Str::limit($value, 60);
            }
        }
    }

    public function updatedDescription($value)
    {
        // Valida solo il campo description
        $this->validateOnly('description');

        // Auto-genera meta_description se vuota
        if (!empty($value) && empty($this->meta_description)) {
            $this->meta_description = Str::limit(strip_tags($value), 160);
        }
    }

    public function updatedFeaturedImage()
    {
        $this->validateOnly('featured_image');
    }

    public function updatedGalleryImages()
    {
        $this->validateOnly('gallery_images.*');
    }

    public function save()
    {
        // Previeni doppio click
        if ($this->isSaving) {
            return;
        }

        $this->isSaving = true;

        // Validazione completa
        $this->validate();

        DB::beginTransaction();

        try {
            // Crea il progetto
            $project = $this->createProject();

            // Gestisci upload immagini
            $this->handleImageUploads($project);

            // Crea record SEO
            $this->createSeoRecord($project);

            // Sincronizza relazioni
            $this->syncRelations($project);

            DB::commit();

            // Pulisci cache
            Project::clearCache();
            Cache::forget('project_categories');
            Cache::forget('project_technologies');

            // Reset del form
            $this->resetForm();

            session()->flash('success', 'Progetto creato con successo!');

            return redirect()->route('admin.projects.edit', $project->id);
        } catch (\Exception $e) {
            DB::rollback();

            // Log dell'errore per debug
            Log::error('Errore creazione progetto', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            session()->flash('error', 'Errore nel salvataggio: ' . $e->getMessage());

            $this->isSaving = false;
            return null;
        }
    }

    protected function createProject()
    {
        $imageService = app(ImageService::class);

        $project = new Project();
        $project->title = $this->title;
        $project->slug = $this->suggestedSlug ?: Project::generateUniqueSlug($this->title);
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

        // Upload featured image con ottimizzazione
        if ($this->featured_image) {
            $project->featured_image = $imageService->processProjectImage($this->featured_image);
        }

        $project->save();

        return $project;
    }

    protected function handleImageUploads(Project $project)
    {
        if (empty($this->gallery_images)) {
            return;
        }

        $imageService = app(ImageService::class);

        foreach ($this->gallery_images as $index => $image) {
            $imagePath = $imageService->processProjectImage($image, 'projects/gallery');

            ProjectImage::create([
                'project_id' => $project->id,
                'filename' => $imagePath,
                'original_name' => $image->getClientOriginalName(),
                'alt_text' => $this->title . ' - Gallery Image ' . ($index + 1),
                'caption' => null,
                'sort_order' => $index,
                'type' => 'gallery'
            ]);
        }
    }

    protected function createSeoRecord(Project $project)
    {
        // Crea record SEO solo se ci sono dati
        if (!$this->hasSeoData() && !$this->og_image) {
            return;
        }

        $imageService = app(ImageService::class);

        $seoData = [
            'project_id' => $project->id,
            'meta_title' => $this->meta_title ?: Str::limit($this->title, 60),
            'meta_description' => $this->meta_description ?: Str::limit(strip_tags($this->description), 160),
            'meta_keywords' => null,
        ];

        // Gestione keywords
        if ($this->meta_keywords) {
            $keywords = array_map('trim', explode(',', $this->meta_keywords));
            $seoData['meta_keywords'] = json_encode($keywords);
        }

        // Gestione OG image
        if ($this->og_image) {
            $seoData['og_image'] = $imageService->processProjectImage($this->og_image, 'projects/og');
        } elseif ($project->featured_image) {
            $seoData['og_image'] = $project->featured_image;
        }

        ProjectSeo::create($seoData);
    }

    protected function syncRelations(Project $project)
    {
        if (!empty($this->selected_categories)) {
            $project->categories()->sync($this->selected_categories);
        }

        if (!empty($this->selected_technologies)) {
            $project->technologies()->sync($this->selected_technologies);
        }
    }

    protected function hasSeoData()
    {
        return $this->meta_title || $this->meta_description || $this->meta_keywords;
    }

    public function saveAsDraft()
    {
        $this->status = 'draft';
        return $this->save();
    }

    public function saveAndPublish()
    {
        $this->status = 'published';
        return $this->save();
    }

    public function removeGalleryImage($index)
    {
        if (isset($this->gallery_images[$index])) {
            unset($this->gallery_images[$index]);
            $this->gallery_images = array_values($this->gallery_images);
        }
    }

    protected function resetForm()
    {
        $this->reset([
            'title',
            'description',
            'content',
            'client',
            'project_url',
            'github_url',
            'start_date',
            'end_date',
            'status',
            'is_featured',
            'featured_image',
            'gallery_images',
            'selected_categories',
            'selected_technologies',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'og_image',
            'suggestedSlug',
            'slugAvailable'
        ]);

        // Ricalcola il prossimo sort_order
        $this->sort_order = (Project::max('sort_order') ?? 0) + 1;

        // Reset validation errors
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.projects.create', [
            'categories' => $this->categories ?? ProjectCategory::orderBy('sort_order')->orderBy('name')->get(),
            'technologies' => $this->technologies ?? ProjectTechnology::orderBy('category')->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
