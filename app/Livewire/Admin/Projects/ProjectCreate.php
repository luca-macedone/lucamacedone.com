<?php

namespace App\Livewire\Admin\Projects;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectTechnology;
use App\Traits\ManagesProjectForm;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectCreate extends Component
{
    use WithFileUploads, ManagesProjectForm;

    // Proprietà specifiche per la creazione
    public $gallery_images = [];

    /**
     * Regole di validazione aggiuntive per la creazione
     */
    protected function getCreateRules()
    {
        return [
            'gallery_images.*' => 'image|max:2048',
        ];
    }

    /**
     * Messaggi di errore aggiuntivi per la creazione
     */
    protected function getCreateMessages()
    {
        return [
            'gallery_images.*.image' => 'Tutti i file devono essere immagini.',
            'gallery_images.*.max' => 'Le immagini non possono superare i 2MB.',
        ];
    }

    /**
     * Inizializzazione del componente
     */
    public function mount()
    {
        // Inizializza con valori predefiniti
        $this->sort_order = Project::max('sort_order') + 1 ?? 0;
    }

    /**
     * Salva il nuovo progetto
     */
    public function save()
    {
        // Valida con regole base + regole specifiche per create
        $this->validateProjectForm($this->getCreateRules());

        DB::beginTransaction();

        try {
            // Crea il progetto
            $project = Project::create($this->prepareProjectData());

            // Gestisci featured image
            if ($this->featured_image) {
                $project->featured_image = $this->handleFeaturedImageUpload($project);
                $project->save();
            }

            // Carica immagini galleria
            $this->handleGalleryImagesUpload($project, $this->gallery_images);

            // Gestisci dati SEO
            $this->handleSeoData($project, false);

            // Sincronizza relazioni
            $this->syncProjectRelationships($project);

            DB::commit();

            Log::info("Project created successfully: {$project->id}");
            session()->flash('message', 'Progetto creato con successo!');

            return redirect()->route('admin.projects.index');
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Errore creazione progetto: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Errore nel salvataggio: ' . $e->getMessage());
        }
    }

    /**
     * Salva come bozza
     */
    public function saveAsDraft()
    {
        $this->status = 'draft';
        $this->save();
    }

    /**
     * Salva e pubblica
     */
    public function saveAndPublish()
    {
        $this->status = 'published';
        $this->save();
    }

    /**
     * Rimuovi immagine dalla galleria
     */
    public function removeGalleryImage($index)
    {
        unset($this->gallery_images[$index]);
        $this->gallery_images = array_values($this->gallery_images);
    }

    /**
     * Render del componente
     */
    public function render()
    {
        return view('livewire.admin.projects.create', [
            'categories' => ProjectCategory::orderBy('name')->get(),
            'technologies' => ProjectTechnology::orderBy('category')->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
