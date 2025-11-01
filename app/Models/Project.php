<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'featured_image',
        'client',
        'project_url',
        'github_url',
        'start_date',
        'end_date',
        'status',
        'sort_order',
        'is_featured',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_featured' => 'boolean'
    ];

    // RIMUOVIAMO L'EAGER LOADING GLOBALE
    // protected $with = ['categories', 'technologies'];

    /**
     * Boot method per gestire automaticamente lo slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = static::generateUniqueSlug($project->title);
            }
        });

        static::updating(function ($project) {
            if ($project->isDirty('title') && !$project->isDirty('slug')) {
                $project->slug = static::generateUniqueSlug($project->title, $project->id);
            }
        });

        // Quando un progetto viene eliminato, elimina anche i file fisici
        static::deleting(function ($project) {
            if ($project->featured_image) {
                Storage::disk('public')->delete($project->featured_image);
            }

            foreach ($project->images as $image) {
                Storage::disk('public')->delete($image->filename);
            }
        });
    }

    /**
     * =================================================================
     * QUERY SCOPES PER DIVERSI CONTESTI
     * =================================================================
     */

    /**
     * Scope per il pannello admin con tutti i dettagli
     * Uso: Project::withFullDetails()->get()
     */
    public function scopeWithFullDetails($query)
    {
        return $query->with([
            'categories' => function ($q) {
                $q->orderBy('sort_order')->orderBy('name');
            },
            'technologies' => function ($q) {
                $q->orderBy('category')->orderBy('name');
            },
            'galleryImages' => function ($q) {
                $q->orderBy('sort_order');
            },
            'seo'
        ]);
    }

    /**
     * Scope per listing pubblico con informazioni base
     * Uso: Project::withBasicInfo()->published()->get()
     */
    public function scopeWithBasicInfo($query)
    {
        return $query->with([
            'categories:id,name,slug,color',
            'technologies:id,name,icon,color'
        ]);
    }

    /**
     * Scope per card/preview (minimo indispensabile)
     * Uso: Project::forCard()->published()->limit(6)->get()
     */
    public function scopeForCard($query)
    {
        return $query->with([
            'categories:id,name,color',
            'technologies:id,name,icon'  // AGGIUNTO: carica tecnologie minime
        ])->select([
            'id',
            'title',
            'slug',
            'description',
            'featured_image',
            'client',
            'status',
            'is_featured',
            'sort_order',  // AGGIUNTO: necessario per ordinamento
            'created_at'
        ]);
    }

    /**
     * Scope per visualizzazione dettagliata singolo progetto
     * Uso: Project::forShow()->where('slug', $slug)->firstOrFail()
     */
    public function scopeForShow($query)
    {
        return $query->with([
            'categories',
            'technologies',
            'galleryImages' => function ($q) {
                $q->orderBy('sort_order');
            },
            'seo'
        ]);
    }

    /**
     * Scope per dashboard/statistiche (nessuna relazione)
     * Uso: Project::forStats()->count()
     */
    public function scopeForStats($query)
    {
        return $query->select(['id', 'status', 'is_featured', 'created_at']);
    }

    /**
     * =================================================================
     * SCOPE CONDIZIONALI COMUNI
     * =================================================================
     */

    /**
     * Scope per progetti pubblicati
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope per progetti in evidenza
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope per ordinamento predefinito
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Scope per ricerca
     */
    public function scopeSearch($query, $term)
    {
        if (!$term) return $query;

        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('content', 'like', "%{$term}%")
                ->orWhere('client', 'like', "%{$term}%");
        });
    }

    /**
     * Scope per filtrare per categoria
     */
    public function scopeInCategory($query, $categoryId)
    {
        if (!$categoryId) return $query;

        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('project_categories.id', $categoryId);
        });
    }

    /**
     * Scope per filtrare per tecnologia
     */
    public function scopeWithTechnology($query, $technologyId)
    {
        if (!$technologyId) return $query;

        return $query->whereHas('technologies', function ($q) use ($technologyId) {
            $q->where('project_technologies.id', $technologyId);
        });
    }

    /**
     * =================================================================
     * RELAZIONI
     * =================================================================
     */

    /**
     * Relazione con le categorie (con timestamps)
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProjectCategory::class, 'project_category_pivot')
            ->withTimestamps();
    }

    /**
     * Relazione con le tecnologie (con timestamps)
     */
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(ProjectTechnology::class, 'project_technology_pivot')
            ->withTimestamps();
    }

    /**
     * Relazione con le immagini della galleria
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    /**
     * Alias per compatibilità con codice esistente
     */
    public function galleryImages(): HasMany
    {
        return $this->images();
    }

    /**
     * Relazione con i dati SEO
     */
    public function seo(): HasOne
    {
        return $this->hasOne(ProjectSeo::class);
    }

    /**
     * =================================================================
     * METODI HELPER
     * =================================================================
     */

    /**
     * Genera uno slug unico per il progetto
     */
    protected static function generateUniqueSlug($title, $excludeId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Ottieni l'URL dell'immagine in evidenza
     */
    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image
            ? asset('storage/' . $this->featured_image)
            : asset('images/placeholder.jpg');
    }

    /**
     * Ottieni il tempo di lettura stimato
     */
    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200); // 200 parole al minuto
        return $minutes;
    }

    /**
     * Controlla se il progetto è nuovo (ultimi 30 giorni)
     */
    public function getIsNewAttribute()
    {
        return $this->created_at->gt(now()->subDays(30));
    }

    /**
     * Ottieni lo stato formattato
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'published' => '<span class="badge badge-success">Pubblicato</span>',
            'draft' => '<span class="badge badge-warning">Bozza</span>',
            'archived' => '<span class="badge badge-secondary">Archiviato</span>',
            default => '<span class="badge badge-light">Sconosciuto</span>'
        };
    }
}
