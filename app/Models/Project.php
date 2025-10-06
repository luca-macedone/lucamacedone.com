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

    protected $with = ['categories', 'technologies']; // Carica sempre le relazioni

    /**
     * Boot method per gestire automaticamente lo slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);

                // Assicurati che lo slug sia unico
                $originalSlug = $project->slug;
                $counter = 1;
                while (static::where('slug', $project->slug)->exists()) {
                    $project->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });

        static::updating(function ($project) {
            if ($project->isDirty('title') && !$project->isDirty('slug')) {
                $project->slug = Str::slug($project->title);

                // Assicurati che lo slug sia unico (escludendo il record corrente)
                $originalSlug = $project->slug;
                $counter = 1;
                while (static::where('slug', $project->slug)
                    ->where('id', '!=', $project->id)
                    ->exists()
                ) {
                    $project->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });

        // Quando un progetto viene eliminato, elimina anche i record correlati
        static::deleting(function ($project) {
            // Le immagini vengono eliminate automaticamente grazie a onDelete('cascade')
            // Ma dobbiamo eliminare i file fisici
            if ($project->featured_image) {
                Storage::disk('public')->delete($project->featured_image);
            }

            foreach ($project->images as $image) {
                Storage::disk('public')->delete($image->filename);
            }
        });
    }

    /**
     * Relazione con le categorie
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProjectCategory::class, 'project_category_pivot');
    }

    /**
     * Relazione con le tecnologie
     */
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(ProjectTechnology::class, 'project_technology_pivot');
    }

    /**
     * Relazione con le immagini della galleria
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    /**
     * Relazione per ottenere solo le immagini della galleria
     */
    public function galleryImages(): HasMany
    {
        return $this->hasMany(ProjectImage::class)
            ->where('type', 'gallery')
            ->orderBy('sort_order');
    }

    /**
     * Relazione con i dati SEO
     */
    public function seo(): HasOne
    {
        return $this->hasOne(ProjectSeo::class);
    }

    /**
     * Scopes per query comuni
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    public function scopeWithFullData($query)
    {
        return $query->with(['categories', 'technologies', 'images', 'seo']);
    }

    /**
     * Accessors per compatibilità e convenienza
     */
    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image
            ? asset('storage/' . $this->featured_image)
            : asset('images/placeholder-project.jpg'); // Placeholder di default
    }

    /**
     * Ottieni tutte le URL delle immagini della galleria
     */
    public function getGalleryUrlsAttribute()
    {
        return $this->galleryImages->map(function ($image) {
            return [
                'url' => asset('storage/' . $image->filename),
                'alt' => $image->alt_text,
                'caption' => $image->caption,
            ];
        });
    }

    /**
     * Ottieni i dati SEO con fallback
     */
    public function getMetaTitleAttribute()
    {
        return $this->seo?->meta_title ?: Str::limit($this->title, 60);
    }

    public function getMetaDescriptionAttribute()
    {
        return $this->seo?->meta_description ?: Str::limit(strip_tags($this->description), 160);
    }

    public function getMetaKeywordsAttribute()
    {
        if ($this->seo && $this->seo->meta_keywords) {
            return is_array($this->seo->meta_keywords)
                ? implode(', ', $this->seo->meta_keywords)
                : $this->seo->meta_keywords;
        }

        // Genera keywords automatici basati su categorie e tecnologie
        $keywords = [];
        $keywords = array_merge($keywords, $this->categories->pluck('name')->toArray());
        $keywords = array_merge($keywords, $this->technologies->pluck('name')->toArray());
        return implode(', ', $keywords);
    }

    /**
     * Controlla se il progetto è completo
     */
    public function getIsCompleteAttribute()
    {
        return $this->end_date && $this->end_date->isPast();
    }

    /**
     * Ottieni la durata del progetto in giorni
     */
    public function getDurationInDaysAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date);
        }
        return null;
    }

    /**
     * Ottieni un array di tecnologie raggruppate per categoria
     */
    public function getTechnologiesByCategoryAttribute()
    {
        return $this->technologies->groupBy('category');
    }
}
