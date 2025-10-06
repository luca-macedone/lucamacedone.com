<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Traits\HasSeo;

class Project extends Model
{
    use HasFactory, HasSeo;

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

    // RIMOSSO: protected $with = ['categories', 'technologies'];
    // Usare eager loading selettivo invece

    /**
     * Boot method per gestire automaticamente slug e cache
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

        // Invalidare cache quando si salva o elimina
        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function ($project) {
            static::clearCache();

            // Elimina file fisici
            if ($project->featured_image) {
                Storage::disk('public')->delete($project->featured_image);
            }

            foreach ($project->images as $image) {
                Storage::disk('public')->delete($image->filename);
                // Elimina anche thumbnail se esiste
                $thumbPath = str_replace('projects/', 'projects/thumbs/', $image->filename);
                Storage::disk('public')->delete($thumbPath);
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
     * Relazione con tutte le immagini
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
        return $query->orderBy('sort_order')
            ->orderBy('created_at', 'desc');
    }

    public function scopeWithFullData($query)
    {
        return $query->with(['categories', 'technologies', 'images', 'seo']);
    }

    /**
     * Scope per filtrare per categoria
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('project_categories.id', $categoryId);
        });
    }

    /**
     * Scope per filtrare per tecnologia
     */
    public function scopeWithTechnology($query, $technologyId)
    {
        return $query->whereHas('technologies', function ($q) use ($technologyId) {
            $q->where('project_technologies.id', $technologyId);
        });
    }

    /**
     * Ottieni progetti in evidenza con cache
     */
    public static function getFeaturedProjects($limit = 6)
    {
        return Cache::remember('featured_projects_' . $limit, config('projects.cache.ttl', 3600), function () use ($limit) {
            return static::with(['categories', 'technologies'])
                ->published()
                ->featured()
                ->ordered()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Ottieni progetti recenti con cache
     */
    public static function getRecentProjects($limit = 4)
    {
        return Cache::remember('recent_projects_' . $limit, config('projects.cache.ttl', 3600), function () use ($limit) {
            return static::with(['categories', 'technologies'])
                ->published()
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
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
     * Pulisci cache
     */
    public static function clearCache()
    {
        Cache::forget('featured_projects_4');
        Cache::forget('featured_projects_6');
        Cache::forget('recent_projects_4');
        Cache::forget('recent_projects_6');
        Cache::tags(['projects'])->flush();
    }

    /**
     * Accessors
     */
    public function getFeaturedImageUrlAttribute()
    {
        if ($this->featured_image) {
            return asset('storage/' . $this->featured_image);
        }
        return asset('images/placeholder-project.jpg');
    }

    public function getFeaturedImageThumbUrlAttribute()
    {
        if ($this->featured_image) {
            $thumbPath = str_replace('projects/', 'projects/thumbs/', $this->featured_image);
            if (Storage::disk('public')->exists($thumbPath)) {
                return asset('storage/' . $thumbPath);
            }
        }
        return $this->featured_image_url;
    }

    public function getGalleryUrlsAttribute()
    {
        return $this->galleryImages->map(function ($image) {
            return [
                'url' => asset('storage/' . $image->filename),
                'thumb' => $this->getImageThumbUrl($image->filename),
                'alt' => $image->alt_text,
                'caption' => $image->caption,
            ];
        });
    }

    private function getImageThumbUrl($filename)
    {
        $thumbPath = str_replace('projects/', 'projects/thumbs/', $filename);
        if (Storage::disk('public')->exists($thumbPath)) {
            return asset('storage/' . $thumbPath);
        }
        return asset('storage/' . $filename);
    }

    /**
     * SEO Accessors con fallback
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
        return $this->generateKeywordsString();
    }

    private function generateKeywordsString()
    {
        $keywords = [];
        $keywords = array_merge($keywords, $this->categories->pluck('name')->toArray());
        $keywords = array_merge($keywords, $this->technologies->pluck('name')->toArray());
        return implode(', ', array_unique($keywords));
    }

    /**
     * Controlla se il progetto è completo
     */
    public function getIsCompleteAttribute()
    {
        return $this->end_date && $this->end_date->isPast();
    }

    /**
     * Ottieni la durata del progetto
     */
    public function getDurationInDaysAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date);
        }
        return null;
    }

    public function getDurationInMonthsAttribute()
    {
        if ($this->start_date && $this->end_date) {
            $months = $this->start_date->diffInMonths($this->end_date);
            return $months > 0 ? $months : 1;
        }
        return null;
    }

    /**
     * Ottieni tecnologie raggruppate per categoria
     */
    public function getTechnologiesByCategoryAttribute()
    {
        return $this->technologies->groupBy('category');
    }

    /**
     * Ottieni progetti correlati
     */
    public function getRelatedProjects($limit = 4)
    {
        $categoryIds = $this->categories->pluck('id');

        $query = static::with(['categories', 'technologies'])
            ->published()
            ->where('id', '!=', $this->id);

        if ($categoryIds->isNotEmpty()) {
            $query->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('project_categories.id', $categoryIds);
            });
        }

        return $query->ordered()
            ->limit($limit)
            ->get();
    }

    /**
     * Verifica se il progetto può essere pubblicato
     */
    public function canBePublished()
    {
        return !empty($this->title)
            && !empty($this->description)
            && !empty($this->slug);
    }

    /**
     * Pubblica il progetto
     */
    public function publish()
    {
        if ($this->canBePublished()) {
            $this->status = 'published';
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Metti in bozza il progetto
     */
    public function draft()
    {
        $this->status = 'draft';
        $this->save();
    }
}
