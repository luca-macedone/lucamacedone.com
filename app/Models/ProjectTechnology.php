<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class ProjectTechnology extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'category',
        'projects_count_cache',
        'cache_updated_at'
    ];

    protected $casts = [
        'projects_count_cache' => 'integer',
        'cache_updated_at' => 'datetime',
    ];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($technology) {
            if (empty($technology->slug)) {
                $technology->slug = Str::slug($technology->name);
            }

            if (empty($technology->color)) {
                $technology->color = '#' . substr(md5($technology->name), 0, 6);
            }
        });

        // Clear cache quando una tecnologia viene modificata
        static::saved(function ($technology) {
            cache()->tags(['technologies', 'projects'])->flush();
        });

        static::deleted(function ($technology) {
            cache()->tags(['technologies', 'projects'])->flush();
        });
    }

    /**
     * Relationships
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            Project::class,
            'project_technology_pivot',
            'project_technology_id',
            'project_id'
        );
    }

    /**
     * Scopes
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeWithProjectCount($query)
    {
        return $query->withCount('projects');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('category')
            ->orderBy('name');
    }

    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('projects_count_cache', 'desc')
            ->limit($limit);
    }

    /**
     * Get icon class or URL
     */
    public function getIconClassAttribute(): string
    {
        // Se l'icona è una classe (es. "fab fa-laravel")
        if (strpos($this->icon, 'fa-') !== false || strpos($this->icon, 'icon-') !== false) {
            return $this->icon;
        }

        // Se l'icona è un URL
        if (filter_var($this->icon, FILTER_VALIDATE_URL)) {
            return 'url:' . $this->icon;
        }

        // Se l'icona è un path locale
        if ($this->icon && file_exists(public_path($this->icon))) {
            return 'url:' . asset($this->icon);
        }

        // Default icon
        return 'fas fa-code';
    }

    /**
     * Update cached project count
     */
    public function updateProjectCount()
    {
        $this->update([
            'projects_count_cache' => $this->projects()->count(),
            'cache_updated_at' => now()
        ]);
    }

    /**
     * Get projects count (from cache or fresh)
     */
    public function getProjectsCountAttribute(): int
    {
        // Se il cache è vecchio di più di 24 ore, aggiorna
        if (!$this->cache_updated_at || $this->cache_updated_at->diffInHours(now()) > 24) {
            $this->updateProjectCount();
            $this->refresh();
        }

        return $this->projects_count_cache;
    }

    /**
     * Get color with transparency
     */
    public function getColorWithOpacityAttribute($opacity = 0.1): string
    {
        $hex = str_replace('#', '', $this->color);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return "rgba($r, $g, $b, $opacity)";
    }

    /**
     * Group technologies by category
     */
    public static function groupedByCategory()
    {
        return static::orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');
    }
}
