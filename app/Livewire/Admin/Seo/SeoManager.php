<?php

namespace App\Livewire\Admin\Seo;

use App\Models\Project;
use App\Models\ProjectSeo;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SeoManager extends Component
{
    use WithFileUploads;

    public Project $project;
    public ProjectSeo $seo;

    // SEO Fields
    public $meta_title = '';
    public $meta_description = '';
    public $meta_keywords = '';
    public $og_image;
    public $existing_og_image;

    // SEO Analysis
    public $titleLength = 0;
    public $descriptionLength = 0;
    public $keywordCount = 0;

    // Preview
    public $showPreview = false;

    // AI Suggestions (placeholder per future integrazioni)
    public $suggestions = [];

    protected $rules = [
        'meta_title' => 'nullable|string|max:60',
        'meta_description' => 'nullable|string|max:160',
        'meta_keywords' => 'nullable|string|max:255',
        'og_image' => 'nullable|image|max:2048|dimensions:min_width=1200,min_height=630',
    ];

    protected $messages = [
        'meta_title.max' => 'Il titolo SEO non deve superare i 60 caratteri.',
        'meta_description.max' => 'La descrizione SEO non deve superare i 160 caratteri.',
        'og_image.image' => 'Il file deve essere un\'immagine.',
        'og_image.max' => 'L\'immagine OG non può superare i 2MB.',
        'og_image.dimensions' => 'L\'immagine OG deve essere almeno 1200x630px per Facebook/LinkedIn.',
    ];

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->seo = $project->seo ?? new ProjectSeo();

        if ($project->seo) {
            $this->meta_title = $this->seo->meta_title ?? '';
            $this->meta_description = $this->seo->meta_description ?? '';

            // Gestione keywords
            if ($this->seo->meta_keywords) {
                if (is_array($this->seo->meta_keywords)) {
                    $this->meta_keywords = implode(', ', $this->seo->meta_keywords);
                } else {
                    $decoded = json_decode($this->seo->meta_keywords, true);
                    $this->meta_keywords = is_array($decoded)
                        ? implode(', ', $decoded)
                        : $this->seo->meta_keywords;
                }
            }

            $this->existing_og_image = $this->seo->og_image;
        }

        $this->updateAnalysis();
    }

    /**
     * Aggiorna analisi SEO in tempo reale
     */
    public function updatedMetaTitle($value)
    {
        $this->titleLength = strlen($value);
        $this->generateSuggestions();
    }

    public function updatedMetaDescription($value)
    {
        $this->descriptionLength = strlen($value);
        $this->generateSuggestions();
    }

    public function updatedMetaKeywords($value)
    {
        $keywords = array_filter(array_map('trim', explode(',', $value)));
        $this->keywordCount = count($keywords);
    }

    /**
     * Aggiorna l'analisi SEO
     */
    private function updateAnalysis()
    {
        $this->titleLength = strlen($this->meta_title);
        $this->descriptionLength = strlen($this->meta_description);

        if ($this->meta_keywords) {
            $keywords = array_filter(array_map('trim', explode(',', $this->meta_keywords)));
            $this->keywordCount = count($keywords);
        }
    }

    /**
     * Genera automaticamente meta title
     */
    public function generateTitle()
    {
        $title = $this->project->title;

        // Aggiungi il cliente se presente
        if ($this->project->client) {
            $title .= ' | ' . $this->project->client;
        }

        // Tronca a 60 caratteri
        $this->meta_title = Str::limit($title, 60, '');
        $this->updatedMetaTitle($this->meta_title);
    }

    /**
     * Genera automaticamente meta description
     */
    public function generateDescription()
    {
        $description = strip_tags($this->project->description);

        // Rimuovi spazi multipli
        $description = preg_replace('/\s+/', ' ', $description);

        // Tronca a 160 caratteri
        $this->meta_description = Str::limit($description, 160, '...');
        $this->updatedMetaDescription($this->meta_description);
    }

    /**
     * Genera keywords automaticamente
     */
    public function generateKeywords()
    {
        $keywords = [];

        // Aggiungi categorie
        foreach ($this->project->categories as $category) {
            $keywords[] = strtolower($category->name);
        }

        // Aggiungi tecnologie
        foreach ($this->project->technologies as $tech) {
            $keywords[] = strtolower($tech->name);
        }

        // Aggiungi cliente se presente
        if ($this->project->client) {
            $keywords[] = strtolower($this->project->client);
        }

        // Estrai parole chiave dal titolo (escludi articoli e preposizioni)
        $stopWords = ['il', 'lo', 'la', 'i', 'gli', 'le', 'di', 'a', 'da', 'in', 'con', 'su', 'per', 'tra', 'fra', 'e', 'o'];
        $titleWords = explode(' ', strtolower($this->project->title));

        foreach ($titleWords as $word) {
            $word = trim($word);
            if (strlen($word) > 3 && !in_array($word, $stopWords) && !in_array($word, $keywords)) {
                $keywords[] = $word;
            }
        }

        // Limita a 10 keywords
        $keywords = array_slice(array_unique($keywords), 0, 10);

        $this->meta_keywords = implode(', ', $keywords);
        $this->updatedMetaKeywords($this->meta_keywords);
    }

    /**
     * Genera suggerimenti SEO
     */
    private function generateSuggestions()
    {
        $this->suggestions = [];

        // Analisi titolo
        if ($this->titleLength < 30) {
            $this->suggestions[] = [
                'type' => 'warning',
                'field' => 'title',
                'message' => 'Il titolo è troppo corto. Consigliati 50-60 caratteri.'
            ];
        } elseif ($this->titleLength > 60) {
            $this->suggestions[] = [
                'type' => 'error',
                'field' => 'title',
                'message' => 'Il titolo è troppo lungo e verrà troncato nei risultati di ricerca.'
            ];
        }

        // Analisi descrizione
        if ($this->descriptionLength < 70) {
            $this->suggestions[] = [
                'type' => 'warning',
                'field' => 'description',
                'message' => 'La descrizione è troppo corta. Consigliati 150-160 caratteri.'
            ];
        } elseif ($this->descriptionLength > 160) {
            $this->suggestions[] = [
                'type' => 'error',
                'field' => 'description',
                'message' => 'La descrizione è troppo lunga e verrà troncata.'
            ];
        }

        // Analisi keywords
        if ($this->keywordCount > 10) {
            $this->suggestions[] = [
                'type' => 'warning',
                'field' => 'keywords',
                'message' => 'Troppe parole chiave. Mantieni tra 5-10 keywords rilevanti.'
            ];
        }

        // Check presenza keyword principale nel titolo
        if ($this->meta_keywords && $this->meta_title) {
            $primaryKeyword = explode(',', $this->meta_keywords)[0] ?? '';
            if ($primaryKeyword && !str_contains(strtolower($this->meta_title), strtolower(trim($primaryKeyword)))) {
                $this->suggestions[] = [
                    'type' => 'info',
                    'field' => 'title',
                    'message' => 'Considera di includere la keyword principale nel titolo.'
                ];
            }
        }
    }

    /**
     * Salva dati SEO
     */
    public function save()
    {
        $this->validate();

        try {
            // Gestione upload OG Image
            if ($this->og_image) {
                $path = $this->og_image->store('seo/og-images', 'public');

                // Elimina vecchia immagine se esiste
                if ($this->existing_og_image && Storage::disk('public')->exists($this->existing_og_image)) {
                    Storage::disk('public')->delete($this->existing_og_image);
                }

                $this->existing_og_image = $path;
            }

            // Prepara keywords come array
            $keywords = null;
            if ($this->meta_keywords) {
                $keywords = array_map('trim', explode(',', $this->meta_keywords));
            }

            // Crea o aggiorna record SEO
            $seoData = [
                'project_id' => $this->project->id,
                'meta_title' => $this->meta_title ?: null,
                'meta_description' => $this->meta_description ?: null,
                'meta_keywords' => $keywords,
                'og_image' => $this->existing_og_image,
            ];

            if ($this->project->seo) {
                $this->project->seo->update($seoData);
            } else {
                ProjectSeo::create($seoData);
            }

            session()->flash('success', 'Dati SEO salvati con successo.');

            // Ricarica relazione
            $this->project->load('seo');
            $this->seo = $this->project->seo;
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nel salvare i dati SEO: ' . $e->getMessage());
        }
    }

    /**
     * Rimuovi immagine OG
     */
    public function removeOgImage()
    {
        if ($this->existing_og_image && Storage::disk('public')->exists($this->existing_og_image)) {
            Storage::disk('public')->delete($this->existing_og_image);
        }

        $this->existing_og_image = null;

        if ($this->project->seo) {
            $this->project->seo->update(['og_image' => null]);
        }

        session()->flash('success', 'Immagine OG rimossa.');
    }

    /**
     * Toggle preview
     */
    public function togglePreview()
    {
        $this->showPreview = !$this->showPreview;
    }

    public function render()
    {
        return view('livewire.admin.seo.seo-manager');
    }
}
