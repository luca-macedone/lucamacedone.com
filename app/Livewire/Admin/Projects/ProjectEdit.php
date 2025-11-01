<?php

namespace App\Livewire\Admin\Projects;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectTechnology;
use App\Models\ProjectImage;
use App\Traits\ManagesProjectForm;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectEdit extends Component
{
    use WithFileUploads, ManagesProjectForm;

    // Oggetto progetto
    public Project $project;

    // Proprietà specifiche per la modifica
    public $new_gallery_images = [];
    public $existing_featured_image;
    public $existing_gallery_images = [];
    public $images_to_delete = [];
    public $existing_og_image;

    /**
     * Regole di validazione aggiuntive per la modifica
     */
    protected function getEditRules()
    {
        return [
            'new_gallery_images.*' => 'image|max:2048',
        ];
    }

    /**
     * Messaggi di errore aggiuntivi per la modifica
     */
    protected function getEditMessages()
    {
        return [
            'new_gallery_images.*.image' => 'Tutti i file devono essere immagini.',
            'new_gallery_images.*.max' => 'Le immagini non possono superare i 2MB.',
        ];
    }

    /**
     * Inizializzazione del componente con i dati del progetto
     */
    public function mount(Project $project)
    {
        $this->project = $project;

        // Carica i dati del progetto nelle proprietà del form
        $this->loadProjectData();

        // Carica dati SEO se esistono
        $this->loadSeoData();

        // Carica immagini esistenti
        $this->loadExistingImages();
    }

    /**
     * Carica i dati del progetto nel form
     */
    protected function loadProjectData()
    {
        $this->title = $this->project->title;
        $this->description = $this->project->description;
        $this->content = $this->project->content ?? '';
        $this->client = $this->project->client ?? '';
        $this->project_url = $this->project->project_url ?? '';
        $this->github_url = $this->project->github_url ?? '';
        $this->start_date = $this->project->start_date ? $this->project->start_date->format('Y-m-d') : '';
        $this->end_date = $this->project->end_date ? $this->project->end_date->format('Y-m-d') : '';
        $this->status = $this->project->status;
        $this->is_featured = $this->project->is_featured;
        $this->sort_order = $this->project->sort_order;

        // Carica relazioni
        $this->selected_categories = $this->project->categories->pluck('id')->toArray();
        $this->selected_technologies = $this->project->technologies->pluck('id')->toArray();
    }

    /**
     * Carica i dati SEO se esistono
     */
    protected function loadSeoData()
    {
        if ($this->project->seo) {
            $seo = $this->project->seo;
            $this->meta_title = $seo->meta_title ?? '';
            $this->meta_description = $seo->meta_description ?? '';

            // Decodifica keywords da JSON
            if ($seo->meta_keywords) {
                $keywords = json_decode($seo->meta_keywords, true);
                $this->meta_keywords = is_array($keywords) ? implode(', ', $keywords) : '';
            }

            $this->existing_og_image = $seo->og_image;
        }
    }

    /**
     * Carica le immagini esistenti
     */
    protected function loadExistingImages()
    {
        // Immagine in evidenza
        $this->existing_featured_image = $this->project->featured_image;

        // Immagini galleria
        $this->existing_gallery_images = $this->project->images()
            ->where('type', 'gallery')
            ->orderBy('sort_order')
            ->get()
            ->map(function ($image) {
                return [
                    'id' => $image->id,
                    'filename' => $image->filename,
                    'alt_text' => $image->alt_text,
                    'caption' => $image->caption,
                ];
            })
            ->toArray();
    }

    /**
     * Salva le modifiche al progetto
     */
    public function save()
    {
        // Valida con regole base + regole specifiche per edit
        $this->validateProjectForm($this->getEditRules());

        DB::beginTransaction();

        try {
            // Aggiorna i dati del progetto
            $this->project->update($this->prepareProjectData());

            // Gestisci featured image
            if ($this->featured_image) {
                $this->project->featured_image = $this->handleFeaturedImageUpload(
                    $this->project,
                    $this->existing_featured_image
                );
                $this->project->save();
            }

            // Elimina immagini marcate per l'eliminazione
            $this->deleteGalleryImages($this->project->id, $this->images_to_delete);

            // Aggiungi nuove immagini alla galleria
            $this->handleGalleryImagesUpload($this->project, $this->new_gallery_images);

            // Gestisci dati SEO
            $this->handleSeoData($this->project, true);

            // Sincronizza relazioni
            $this->syncProjectRelationships($this->project);

            DB::commit();

            Log::info("Project updated successfully: {$this->project->id}");
            session()->flash('message', 'Progetto aggiornato con successo!');

            // Emit evento per refresh eventuale lista
            $this->dispatch('projectUpdated', $this->project->id);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Errore aggiornamento progetto: ' . $e->getMessage(), [
                'project_id' => $this->project->id,
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Errore nell\'aggiornamento: ' . $e->getMessage());
        }
    }

    /**
     * Salva e chiude
     */
    public function saveAndClose()
    {
        $this->save();

        if (!session()->has('error')) {
            return redirect()->route('admin.projects.index');
        }
    }

    /**
     * Toggle dello status del progetto
     */
    public function toggleStatus()
    {
        $this->status = $this->status === 'published' ? 'draft' : 'published';
        $this->save();
    }

    /**
     * Toggle del flag featured
     */
    public function toggleFeatured()
    {
        $this->is_featured = !$this->is_featured;
        $this->save();
    }

    /**
     * Rimuovi immagine esistente dalla galleria
     */
    public function removeExistingGalleryImage($imageId)
    {
        // Aggiungi all'array delle immagini da eliminare
        $this->images_to_delete[] = $imageId;

        // Rimuovi dall'array delle immagini esistenti per l'UI
        $this->existing_gallery_images = array_filter(
            $this->existing_gallery_images,
            function ($img) use ($imageId) {
                return $img['id'] !== $imageId;
            }
        );
    }

    /**
     * Rimuovi nuova immagine dalla galleria
     */
    public function removeNewGalleryImage($index)
    {
        unset($this->new_gallery_images[$index]);
        $this->new_gallery_images = array_values($this->new_gallery_images);
    }

    /**
     * Render del componente
     */
    public function render()
    {
        return view('livewire.admin.projects.edit', [
            'categories' => ProjectCategory::orderBy('name')->get(),
            'technologies' => ProjectTechnology::orderBy('category')->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
