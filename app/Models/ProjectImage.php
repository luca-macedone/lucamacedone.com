<?php
// File: app/Models/ProjectImage.php

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
     * Boot method per gestire l'eliminazione dei file
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
     * Scope per tipo di immagine
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

    /**
     * Ottieni l'URL completo dell'immagine
     */
    public function getUrlAttribute()
    {
        return $this->filename
            ? asset('storage/' . $this->filename)
            : asset('images/placeholder.jpg');
    }

    /**
     * Ottieni le dimensioni del file in formato leggibile
     */
    public function getFileSizeAttribute()
    {
        if ($this->filename && Storage::disk('public')->exists($this->filename)) {
            $bytes = Storage::disk('public')->size($this->filename);
            $units = ['B', 'KB', 'MB', 'GB'];
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.2f", $bytes / pow(1024, $factor)) . ' ' . $units[$factor];
        }
        return '0 B';
    }
}
