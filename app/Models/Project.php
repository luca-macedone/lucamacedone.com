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

    // Eager loading necessario per le relazioni
    protected $with = ['categories', 'technologies'];

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
     * Genera slug univoco
     */
    public static function generateUniqueSlug($title, $excludeId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        $query = static::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;

            $query = static::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    /**
     * Accessors per compatibilità e convenienza
     */
    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image
            ? asset('storage/' . $this->featured_image)
            : asset('images/placeholder-project.jpg');
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
     * Ottieni keywords come stringa (per compatibilità con ProjectEdit)
     */
    public function getKeywordsStringAttribute()
    {
        if ($this->seo && $this->seo->meta_keywords) {
            if (is_array($this->seo->meta_keywords)) {
                return implode(', ', $this->seo->meta_keywords);
            }
            // Se è già salvato come JSON string nel database
            $decoded = json_decode($this->seo->meta_keywords, true);
            if (is_array($decoded)) {
                return implode(', ', $decoded);
            }
            return $this->seo->meta_keywords;
        }
        return '';
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
