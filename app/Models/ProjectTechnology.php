<?php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProjectTechnology extends Model
{
    use Cacheable;

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
        'cache_updated_at' => 'datetime'
    ];

    // Cache configuration
    private const CACHE_TTL = 3600; // 1 ora
    private const CACHE_PREFIX = 'luca_macedone_cache_';

    /**
     * Boot del modello - gestisce eventi e cache
     */
    protected static function boot()
    {
        parent::boot();

        // Invalida cache quando viene salvato
        static::saved(function ($model) {
            $model->clearRelatedCache();
        });

        // Invalida cache quando viene eliminato  
        static::deleted(function ($model) {
            $model->clearRelatedCache();
        });

        // Aggiorna il contatore dei progetti dopo il salvataggio
        static::saved(function ($model) {
            $model->updateProjectsCount();
        });
    }

    /**
     * Relazione con i progetti
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_technology_pivot');
    }

    /**
     * Pulisce tutte le cache correlate
     */
    public function clearRelatedCache(): void
    {
        $cacheKeys = [
            self::CACHE_PREFIX . 'skills_technologies',
            self::CACHE_PREFIX . 'skills_stats',
            self::CACHE_PREFIX . 'skills_sections',
            self::CACHE_PREFIX . 'admin_technologies_types',
            self::CACHE_PREFIX . 'technology_' . $this->id,
            self::CACHE_PREFIX . 'technologies_by_category_' . Str::slug($this->category),
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Pulisce anche la cache delle categorie
        $this->clearCategoriesCache();
    }

    /**
     * Pulisce la cache delle categorie
     */
    private function clearCategoriesCache(): void
    {
        $categories = self::distinct('category')->pluck('category');

        foreach ($categories as $category) {
            Cache::forget(self::CACHE_PREFIX . 'technologies_category_' . Str::slug($category));
        }

        Cache::forget(self::CACHE_PREFIX . 'technologies_categories_list');
    }

    /**
     * Recupera tutte le tecnologie con conteggio progetti (con cache)
     */
    public static function getWithProjectsCount()
    {
        $cacheKey = self::CACHE_PREFIX . 'skills_technologies';

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return self::query()
                ->select(['id', 'name', 'category', 'icon', 'color'])
                ->withCount('projects')
                ->having('projects_count', '>', 0)
                ->orderByDesc('projects_count')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Recupera tecnologie per categoria (con cache)
     */
    public static function getByCategory(string $category)
    {
        $cacheKey = self::CACHE_PREFIX . 'technologies_category_' . Str::slug($category);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($category) {
            return self::where('category', $category)
                ->withCount('projects')
                ->orderByDesc('projects_count')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Recupera statistiche sulle competenze (con cache)
     */
    public static function getSkillsStats(): array
    {
        $cacheKey = self::CACHE_PREFIX . 'skills_stats';

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            $totalProjects = Project::published()->count();
            $totalTechnologies = self::count();
            $totalClients = Project::published()
                ->whereNotNull('client')
                ->where('client', '!=', '')
                ->distinct('client')
                ->count();

            return [
                'total_projects' => $totalProjects,
                'total_technologies' => $totalTechnologies,
                'total_clients' => $totalClients,
                'last_updated' => now()
            ];
        });
    }

    /**
     * Aggiorna il contatore dei progetti in cache
     */
    public function updateProjectsCount(): void
    {
        $count = $this->projects()->count();

        $this->update([
            'projects_count_cache' => $count,
            'cache_updated_at' => now()
        ]);
    }

    /**
     * Recupera una singola tecnologia (con cache)
     */
    public static function findCached($id)
    {
        $cacheKey = self::CACHE_PREFIX . 'technology_' . $id;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            return self::with('projects')->find($id);
        });
    }

    /**
     * Invalida tutta la cache delle tecnologie
     */
    public static function clearAllCache(): void
    {
        $patterns = [
            'skills_technologies',
            'skills_stats',
            'skills_sections',
            'admin_technologies_types',
            'technology_*',
            'technologies_*'
        ];

        foreach ($patterns as $pattern) {
            // Per cache driver file/database, dobbiamo iterare manualmente
            // Non possiamo usare wildcards come con Redis
            self::clearCacheByPattern($pattern);
        }
    }

    /**
     * Helper per pulire cache con pattern
     */
    private static function clearCacheByPattern(string $pattern): void
    {
        $prefix = self::CACHE_PREFIX;

        if (str_contains($pattern, '*')) {
            // Per pattern con wildcard, dobbiamo gestire manualmente
            // Recupera tutte le chiavi conosciute e le invalida
            if ($pattern === 'technology_*') {
                $ids = self::pluck('id');
                foreach ($ids as $id) {
                    Cache::forget($prefix . 'technology_' . $id);
                }
            } elseif ($pattern === 'technologies_*') {
                $categories = self::distinct('category')->pluck('category');
                foreach ($categories as $category) {
                    Cache::forget($prefix . 'technologies_category_' . Str::slug($category));
                }
                Cache::forget($prefix . 'technologies_categories_list');
            }
        } else {
            Cache::forget($prefix . $pattern);
        }
    }

    /**
     * Scope per tecnologie pubblicate
     */
    public function scopePublished($query)
    {
        return $query->whereHas('projects', function ($q) {
            $q->where('status', 'published');
        });
    }

    /**
     * Accessor per il nome formattato
     */
    public function getFormattedNameAttribute(): string
    {
        return ucfirst($this->name);
    }

    /**
     * Accessor per il colore con fallback
     */
    public function getColorAttribute($value): string
    {
        return $value ?: '#6b7280'; // Grigio di default
    }
}
