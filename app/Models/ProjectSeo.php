<?php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectSeo extends Model
{
    use HasFactory;
    use Cacheable;

    protected $table = 'project_seo';

    protected $fillable = [
        'project_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image'
    ];

    protected $casts = [
        'meta_keywords' => 'array',
    ];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache quando SEO viene modificato
        static::saved(function ($seo) {
            cache()->tags(['projects', 'project_' . $seo->project_id, 'seo'])->flush();
        });
    }

    /**
     * Relationships
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get OG Image URL
     */
    public function getOgImageUrlAttribute(): ?string
    {
        if (!$this->og_image) {
            return $this->project->featured_image_url;
        }

        if (filter_var($this->og_image, FILTER_VALIDATE_URL)) {
            return $this->og_image;
        }

        return asset('storage/' . $this->og_image);
    }

    /**
     * Get formatted keywords as string
     */
    public function getKeywordsStringAttribute(): string
    {
        if (is_array($this->meta_keywords)) {
            return implode(', ', $this->meta_keywords);
        }

        return $this->meta_keywords ?? '';
    }

    /**
     * Generate default meta title if not set
     */
    public function getComputedMetaTitleAttribute(): string
    {
        return $this->meta_title ?: $this->project->title . ' | ' . config('app.name');
    }

    /**
     * Generate default meta description if not set
     */
    public function getComputedMetaDescriptionAttribute(): string
    {
        return $this->meta_description ?: $this->project->excerpt;
    }
}
