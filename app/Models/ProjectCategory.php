<?php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class ProjectCategory extends Model
{
    use HasFactory;
    use Cacheable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'sort_order'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        // Invalida cache quando la categoria viene modificata
        static::saved(function ($category) {
            static::clearCategoriesCache();
        });

        static::deleted(function ($category) {
            static::clearCategoriesCache();
        });
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_category_pivot');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Pulisce tutte le cache relative alle categorie
     * Compatibile con driver file (non usa tags)
     */
    public static function clearCategoriesCache(): void
    {
        $cacheKeys = [
            'all_categories',
            'categories_with_projects',
            'categories_ordered',
            'portfolio_categories',
        ];

        $prefix = static::$cachePrefix ?? 'luca_macedone_cache_';

        foreach ($cacheKeys as $key) {
            \Cache::forget($prefix . $key);
        }
    }
}
