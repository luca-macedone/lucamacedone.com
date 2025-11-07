<?php
// app/Models/ProjectImage.php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectImage extends Model
{
    use HasFactory;
    use Cacheable;

    protected $fillable = [
        'project_id',
        'filename',
        'original_name',
        'alt_text',
        'caption',
        'sort_order',
        'type'
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($image) {
            if (is_null($image->sort_order)) {
                $maxOrder = static::where('project_id', $image->project_id)
                    ->where('type', $image->type)
                    ->max('sort_order');

                $image->sort_order = ($maxOrder ?? -1) + 1;
            }
        });

        // Clear cache quando un'immagine viene modificata
        static::saved(function ($image) {
            cache()->tags(['projects', 'project_' . $image->project_id])->flush();
        });

        static::deleted(function ($image) {
            cache()->tags(['projects', 'project_' . $image->project_id])->flush();
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
     * Get full URL for the image
     */
    public function getUrlAttribute(): string
    {
        if (filter_var($this->filename, FILTER_VALIDATE_URL)) {
            return $this->filename;
        }

        return asset('storage/' . $this->filename);
    }

    /**
     * Scopes
     */
    public function scopeGallery($query)
    {
        return $query->where('type', 'gallery');
    }

    public function scopeFeatured($query)
    {
        return $query->where('type', 'featured');
    }

    public function scopeThumbnail($query)
    {
        return $query->where('type', 'thumbnail');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
