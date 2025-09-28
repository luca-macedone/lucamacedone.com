<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'featured_image',
        'gallery_images',  // Cambiato da 'gallery' a 'gallery_images'
        'technologies',
        'client',
        'project_url',
        'github_url',
        'start_date',
        'end_date',
        'status',
        'sort_order',
        'is_featured',
        'meta_title',       // Aggiunto
        'meta_description', // Aggiunto
        'meta_keywords'     // Aggiunto
    ];

    protected $casts = [
        'gallery_images' => 'array',  // Cast automatico ad array
        'technologies' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_featured' => 'boolean'
    ];

    // Genera automaticamente lo slug dal titolo
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
            if ($project->isDirty('title')) {
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
    }

    // Relazioni
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProjectCategory::class, 'project_category_pivot');
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(ProjectTechnology::class, 'project_technology_pivot');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    public function seo(): HasOne
    {
        return $this->hasOne(ProjectSeo::class);
    }

    // Scopes
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

    // Accessors
    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }

    // Nel model Project.php, aggiungi questo accessor
    public function getGalleryImagesAttribute($value)
    {
        if (empty($value)) {
            return [];
        }

        // Se è già un array, restituiscilo
        if (is_array($value)) {
            return $value;
        }

        // Se è una stringa JSON, decodificala
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    public function getGalleryImagesUrlsAttribute()
    {
        $galleryImages = $this->gallery_images;

        if (!$galleryImages) {
            return [];
        }

        // Se è una stringa JSON, decodificala
        if (is_string($galleryImages)) {
            $galleryImages = json_decode($galleryImages, true) ?? [];
        }

        // Assicurati che sia un array
        if (!is_array($galleryImages)) {
            return [];
        }

        return collect($galleryImages)->map(function ($image) {
            return asset('storage/' . $image);
        })->toArray();
    }

    public function getExcerptAttribute()
    {
        return Str::limit(strip_tags($this->description), 150);
    }

    // Mutators
    public function setMetaKeywordsAttribute($value)
    {
        // Se è una stringa, mantienila come stringa
        // Se è un array, convertilo in stringa separata da virgole
        if (is_array($value)) {
            $this->attributes['meta_keywords'] = implode(',', $value);
        } else {
            $this->attributes['meta_keywords'] = $value;
        }
    }

    public function getMetaKeywordsArrayAttribute()
    {
        // Restituisce meta_keywords come array
        if ($this->meta_keywords) {
            return explode(',', $this->meta_keywords);
        }
        return [];
    }

    // Route model binding per slug
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
