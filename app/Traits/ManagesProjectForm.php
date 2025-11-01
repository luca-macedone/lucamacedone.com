<?php

namespace App\Traits;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\ProjectSeo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

trait ManagesProjectForm
{
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

    // Relazioni
    public $selected_categories = [];
    public $selected_technologies = [];

    // SEO fields
    public $meta_title = '';
    public $meta_description = '';
    public $meta_keywords = '';
    public $og_image;

    /**
     * Regole di validazione comuni
     * 
     * @return array
     */
    protected function getBaseRules()
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
            'featured_image' => 'nullable|image|max:2048',
            'selected_categories' => 'array',
            'selected_categories.*' => 'exists:project_categories,id',
            'selected_technologies' => 'array',
            'selected_technologies.*' => 'exists:project_technologies,id',
            'meta_title' => 'nullable|max:60',
            'meta_description' => 'nullable|max:160',
            'meta_keywords' => 'nullable|max:255',
            'og_image' => 'nullable|image|max:2048',
        ];
    }

    /**
     * Messaggi di errore comuni
     * 
     * @return array
     */
    protected function getBaseMessages()
    {
        return [
            'title.required' => 'Il titolo è obbligatorio.',
            'title.min' => 'Il titolo deve avere almeno 3 caratteri.',
            'title.max' => 'Il titolo non può superare i 255 caratteri.',
            'description.required' => 'La descrizione è obbligatoria.',
            'description.min' => 'La descrizione deve avere almeno 10 caratteri.',
            'featured_image.image' => 'Il file deve essere un\'immagine.',
            'featured_image.max' => 'L\'immagine non può superare i 2MB.',
            'end_date.after_or_equal' => 'La data di fine deve essere successiva o uguale alla data di inizio.',
            'project_url.url' => 'Inserisci un URL valido per il progetto.',
            'github_url.url' => 'Inserisci un URL valido per GitHub.',
            'meta_title.max' => 'Il meta title non può superare i 60 caratteri.',
            'meta_description.max' => 'La meta description non può superare i 160 caratteri.',
            'og_image.image' => 'L\'immagine OG deve essere un file immagine.',
            'og_image.max' => 'L\'immagine OG non può superare i 2MB.',
        ];
    }

    /**
     * Prepara i dati del progetto per il salvataggio
     * 
     * @return array
     */
    protected function prepareProjectData()
    {
        return [
            'title' => $this->title,
            'slug' => Str::slug($this->title),
            'description' => $this->description,
            'content' => $this->content ?: null,
            'client' => $this->client ?: null,
            'project_url' => $this->project_url ?: null,
            'github_url' => $this->github_url ?: null,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
        ];
    }

    /**
     * Gestisce l'upload della featured image
     * 
     * @param Project $project
     * @param string|null $existingImage
     * @return string|null
     */
    protected function handleFeaturedImageUpload(Project $project, $existingImage = null)
    {
        if (!$this->featured_image) {
            return $existingImage;
        }

        // Elimina vecchia immagine se esiste
        if ($existingImage && Storage::disk('public')->exists($existingImage)) {
            Storage::disk('public')->delete($existingImage);
        }

        // Salva nuova immagine
        $path = $this->featured_image->store('projects', 'public');

        // Log dell'operazione
        Log::info("Featured image uploaded for project {$project->id}: {$path}");

        return $path;
    }

    /**
     * Gestisce l'upload di immagini della galleria
     * 
     * @param Project $project
     * @param array $images
     * @return void
     */
    protected function handleGalleryImagesUpload(Project $project, array $images)
    {
        if (empty($images)) {
            return;
        }

        $currentMaxOrder = ProjectImage::where('project_id', $project->id)
            ->max('sort_order') ?? -1;

        foreach ($images as $index => $image) {
            $imagePath = $image->store('projects/gallery', 'public');

            ProjectImage::create([
                'project_id' => $project->id,
                'filename' => $imagePath,
                'original_name' => $image->getClientOriginalName(),
                'alt_text' => $this->title . ' - Gallery Image ' . ($index + 1),
                'caption' => null,
                'sort_order' => $currentMaxOrder + $index + 1,
                'type' => 'gallery'
            ]);

            Log::info("Gallery image uploaded for project {$project->id}: {$imagePath}");
        }
    }

    /**
     * Elimina immagini dalla galleria
     * 
     * @param int $projectId
     * @param array $imageIds
     * @return void
     */
    protected function deleteGalleryImages($projectId, array $imageIds)
    {
        if (empty($imageIds)) {
            return;
        }

        $images = ProjectImage::whereIn('id', $imageIds)
            ->where('project_id', $projectId)
            ->get();

        foreach ($images as $image) {
            // Elimina file fisico
            if (Storage::disk('public')->exists($image->filename)) {
                Storage::disk('public')->delete($image->filename);
            }

            // Elimina record dal database
            $image->delete();

            Log::info("Gallery image deleted from project {$projectId}: {$image->filename}");
        }
    }

    /**
     * Gestisce i dati SEO del progetto
     * 
     * @param Project $project
     * @param bool $isUpdate
     * @return void
     */
    protected function handleSeoData(Project $project, $isUpdate = false)
    {
        // Prepara i dati SEO
        $seoData = [
            'project_id' => $project->id,
            'meta_title' => $this->meta_title ?: null,
            'meta_description' => $this->meta_description ?: null,
        ];

        // Gestione keywords - converte stringa in array JSON
        if ($this->meta_keywords) {
            $keywords = array_map('trim', explode(',', $this->meta_keywords));
            $seoData['meta_keywords'] = json_encode($keywords);
        } else {
            $seoData['meta_keywords'] = null;
        }

        // Gestione immagine OG
        if ($this->og_image) {
            $seoData['og_image'] = $this->og_image->store('projects/og', 'public');
        } elseif (!$isUpdate) {
            // In creazione, usa la featured image come fallback
            $seoData['og_image'] = $project->featured_image;
        }

        // Crea o aggiorna record SEO
        if ($isUpdate) {
            $seo = $project->seo;
            if ($seo) {
                // Se c'è una nuova immagine OG, elimina la vecchia
                if ($this->og_image && $seo->og_image && Storage::disk('public')->exists($seo->og_image)) {
                    Storage::disk('public')->delete($seo->og_image);
                }
                $seo->update($seoData);
            } else {
                ProjectSeo::create($seoData);
            }
        } else {
            // Solo se ci sono dati SEO da salvare
            if ($this->meta_title || $this->meta_description || $this->meta_keywords || $this->og_image) {
                ProjectSeo::create($seoData);
            }
        }

        Log::info("SEO data handled for project {$project->id}");
    }

    /**
     * Sincronizza le relazioni many-to-many del progetto
     * 
     * @param Project $project
     * @return void
     */
    protected function syncProjectRelationships(Project $project)
    {
        // Sincronizza categorie
        if (!empty($this->selected_categories)) {
            $project->categories()->sync($this->selected_categories);
            Log::info("Categories synced for project {$project->id}: " . implode(',', $this->selected_categories));
        } else {
            $project->categories()->detach();
        }

        // Sincronizza tecnologie
        if (!empty($this->selected_technologies)) {
            $project->technologies()->sync($this->selected_technologies);
            Log::info("Technologies synced for project {$project->id}: " . implode(',', $this->selected_technologies));
        } else {
            $project->technologies()->detach();
        }
    }

    /**
     * Valida il form con le regole base
     * 
     * @param array $additionalRules
     * @return void
     */
    protected function validateProjectForm(array $additionalRules = [])
    {
        $rules = array_merge($this->getBaseRules(), $additionalRules);
        $this->validate($rules, $this->getBaseMessages());
    }

    /**
     * Reset del form
     * 
     * @return void
     */
    protected function resetProjectForm()
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
            'sort_order',
            'featured_image',
            'selected_categories',
            'selected_technologies',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'og_image'
        ]);
    }
}
