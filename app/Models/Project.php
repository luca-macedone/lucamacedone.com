<?php
// app/Models/Project.php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Project extends Model
{
    use Cacheable;
    use HasFactory;

    // Override per invalidazione specifica
    protected function invalidateSpecificCache(): void
    {
        static::cacheForget('featured_projects');
        static::cacheForget('published_projects');
    }

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
        'is_featured'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'sort_order' => 'integer',
    ];

    protected $appends = ['status_label', 'formatted_period'];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }

            if (is_null($project->sort_order)) {
                $project->sort_order = static::max('sort_order') + 1;
            }
        });

        static::updating(function ($project) {
            if ($project->isDirty('title') && !$project->isDirty('slug')) {
                $project->slug = Str::slug($project->title);
            }
        });

        // Clear cache quando un progetto viene modificato
        static::saved(function ($project) {
            cache()->tags(['projects'])->flush();
        });

        static::deleted(function ($project) {
            cache()->tags(['projects'])->flush();
        });
    }

    /**
     * =================================================================
     * RELATIONSHIPS
     * =================================================================
     */

    /**
     * Categorie del progetto
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            ProjectCategory::class,
            'project_category_pivot',
            'project_id',
            'project_category_id'
        );
    }

    /**
     * Tecnologie utilizzate nel progetto
     */
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(
            ProjectTechnology::class,
            'project_technology_pivot',
            'project_id',
            'project_technology_id'
        );
    }

    /**
     * Immagini della galleria
     */
    public function galleryImages(): HasMany
    {
        return $this->hasMany(ProjectImage::class)
            ->where('type', 'gallery')
            ->orderBy('sort_order');
    }

    /**
     * Tutte le immagini del progetto
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)
            ->orderBy('sort_order');
    }

    /**
     * SEO metadata
     */
    public function seo(): HasOne
    {
        return $this->hasOne(ProjectSeo::class);
    }

    /**
     * =================================================================
     * QUERY SCOPES PER DIVERSI CONTESTI
     * =================================================================
     */

    /**
     * Scope per il pannello admin con tutti i dettagli
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
     */
    public function scopeForCard($query)
    {
        return $query->with([
            'categories:id,name,color',
            'technologies:id,name,icon'
        ])->select([
            'id',
            'title',
            'slug',
            'description',
            'featured_image',
            'client',
            'status',
            'is_featured',
            'sort_order',
            'created_at'
        ]);
    }

    /**
     * Scope per visualizzazione dettagliata singolo progetto
     */
    public function scopeForShow($query)
    {
        return $query->with([
            'categories',
            'technologies',
            'galleryImages',
            'seo'
        ]);
    }

    /**
     * Scope per dashboard/statistiche (nessuna relazione)
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
     * Scope per progetti in un periodo
     */
    public function scopeInPeriod($query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->where('start_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where(function ($q) use ($endDate) {
                $q->where('end_date', '<=', $endDate)
                    ->orWhereNull('end_date');
            });
        }

        return $query;
    }

    /**
     * =================================================================
     * ACCESSORS & MUTATORS
     * =================================================================
     */

    /**
     * Get status label attribute
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'published' => 'Pubblicato',
            'draft' => 'Bozza',
            default => ucfirst($this->status)
        };
    }

    /**
     * Get formatted period attribute
     */
    public function getFormattedPeriodAttribute(): string
    {
        if (!$this->start_date) {
            return '';
        }

        $start = $this->start_date->format('M Y');
        $end = $this->end_date ? $this->end_date->format('M Y') : 'Presente';

        return "{$start} - {$end}";
    }

    /**
     * Get featured image URL
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (!$this->featured_image) {
            return null;
        }

        if (filter_var($this->featured_image, FILTER_VALIDATE_URL)) {
            return $this->featured_image;
        }

        return asset('storage/' . $this->featured_image);
    }

    /**
     * Get excerpt from description or content
     */
    public function getExcerptAttribute($length = 200): string
    {
        $text = $this->description ?: strip_tags($this->content);
        return Str::limit($text, $length);
    }

    /**
     * =================================================================
     * HELPER METHODS
     * =================================================================
     */

    /**
     * Check if project is published
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if project is draft
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if project is featured
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Check if project is currently active
     */
    public function isActive(): bool
    {
        return $this->end_date === null || $this->end_date->isFuture();
    }

    /**
     * Get next project
     */
    public function getNextProject()
    {
        return static::published()
            ->where('sort_order', '>', $this->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();
    }

    /**
     * Get previous project
     */
    public function getPreviousProject()
    {
        return static::published()
            ->where('sort_order', '<', $this->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();
    }

    /**
     * Get related projects
     */
    public function getRelatedProjects($limit = 3)
    {
        $categoryIds = $this->categories->pluck('id');

        return static::published()
            ->where('id', '!=', $this->id)
            ->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('project_categories.id', $categoryIds);
            })
            ->withBasicInfo()
            ->limit($limit)
            ->get();
    }

    /**
     * Update SEO metadata
     */
    public function updateSeo(array $data)
    {
        if ($this->seo) {
            $this->seo->update($data);
        } else {
            $this->seo()->create($data);
        }
    }

    /**
     * Sync categories
     */
    public function syncCategories(array $categoryIds)
    {
        $this->categories()->sync($categoryIds);
        cache()->tags(['projects', 'categories'])->flush();
    }

    /**
     * Sync technologies
     */
    public function syncTechnologies(array $technologyIds)
    {
        $this->technologies()->sync($technologyIds);
        cache()->tags(['projects', 'technologies'])->flush();
    }
}
