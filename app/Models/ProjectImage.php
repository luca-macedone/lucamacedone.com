<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProjectImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'filename',
        'type',
        'title',
        'alt_text',
        'caption',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Boot method per gestire eliminazione file
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            // Elimina il file fisico quando il record viene eliminato
            if ($image->filename && Storage::disk('public')->exists($image->filename)) {
                Storage::disk('public')->delete($image->filename);
            }
        });
    }

    /**
     * Relazione con il progetto
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Scope per tipo immagine
     */
    public function scopeGallery($query)
    {
        return $query->where('type', 'gallery');
    }

    public function scopeFeatured($query)
    {
        return $query->where('type', 'featured');
    }

    /**
     * Ottieni URL completo dell'immagine
     */
    public function getUrlAttribute()
    {
        return $this->filename
            ? asset('storage/' . $this->filename)
            : asset('images/placeholder.jpg');
    }

    /**
     * Ottieni dimensioni dell'immagine
     */
    public function getDimensionsAttribute()
    {
        if ($this->filename && Storage::disk('public')->exists($this->filename)) {
            $path = Storage::disk('public')->path($this->filename);
            if (file_exists($path)) {
                list($width, $height) = getimagesize($path);
                return [
                    'width' => $width,
                    'height' => $height,
                    'ratio' => $width / $height
                ];
            }
        }
        return null;
    }

    /**
     * Ottieni dimensione del file
     */
    public function getSizeAttribute()
    {
        if ($this->filename && Storage::disk('public')->exists($this->filename)) {
            return Storage::disk('public')->size($this->filename);
        }
        return 0;
    }

    /**
     * Ottieni dimensione formattata
     */
    public function getFormattedSizeAttribute()
    {
        $size = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . ' ' . $units[$i];
    }
}
