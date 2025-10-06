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

class ProjectCreate extends Component
{
    use WithFileUploads;

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
    public $gallery_images = [];

    // Relazioni
    public $selected_categories = [];
    public $selected_technologies = [];

    // SEO fields
    public $meta_title = '';
    public $meta_description = '';
    public $meta_keywords = '';
    public $og_image;

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
        'gallery_images.*' => 'image|max:2048',
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
        'gallery_images.*.image' => 'Tutti i file devono essere immagini.',
        'gallery_images.*.max' => 'Le immagini non possono superare i 2MB.',
        'end_date.after_or_equal' => 'La data di fine deve essere successiva o uguale alla data di inizio.',
    ];

    public function mount()
    {
        // Inizializza con valori predefiniti se necessario
        $this->sort_order = Project::max('sort_order') + 1;
    }

    public function updatedTitle($value)
    {
        // Il parametro $value contiene il nuovo valore
        if (empty($this->meta_title) && !empty($value)) {
            $this->meta_title = Str::limit($value, 60);
        }
    }

    public function updatedDescription()
    {
        // Auto-genera meta_description se vuota
        if (empty($this->meta_description)) {
            $this->meta_description = Str::limit(strip_tags($this->description), 160);
        }
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Crea il progetto base
            $project = new Project();
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

            // Upload featured image
            if ($this->featured_image) {
                $project->featured_image = $this->featured_image->store('projects', 'public');
            }

            $project->save();

            // Gestisci gallery images nella tabella project_images
            if (!empty($this->gallery_images)) {
                foreach ($this->gallery_images as $index => $image) {
                    $imagePath = $image->store('projects/gallery', 'public');

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

            // Crea record SEO separato
            if ($this->meta_title || $this->meta_description || $this->meta_keywords) {
                $seoData = [
                    'project_id' => $project->id,
                    'meta_title' => $this->meta_title ?: Str::limit($this->title, 60),
                    'meta_description' => $this->meta_description ?: Str::limit(strip_tags($this->description), 160),
                    'meta_keywords' => $this->meta_keywords ? json_encode(array_map('trim', explode(',', $this->meta_keywords))) : null,
                ];

                // Se c'è un'immagine OG specifica, altrimenti usa la featured
                if ($this->og_image) {
                    $seoData['og_image'] = $this->og_image->store('projects/og', 'public');
                } elseif ($project->featured_image) {
                    $seoData['og_image'] = $project->featured_image;
                }

                ProjectSeo::create($seoData);
            }

            // Sincronizza relazioni many-to-many
            if (!empty($this->selected_categories)) {
                $project->categories()->sync($this->selected_categories);
            }

            if (!empty($this->selected_technologies)) {
                $project->technologies()->sync($this->selected_technologies);
            }

            DB::commit();

            session()->flash('message', 'Progetto creato con successo!');
            return redirect()->route('admin.projects.index');
        } catch (\Exception $e) {
            DB::rollback();
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
