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
        'original_name',
        'alt_text',
        'caption',
        'sort_order',
        'type',
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

        // Quando un'immagine viene eliminata, elimina anche il file fisico
        static::deleting(function ($image) {
            if (Storage::disk('public')->exists($image->filename)) {
                Storage::disk('public')->delete($image->filename);
            }

            // Elimina anche la thumbnail se esiste
            $thumbPath = str_replace('projects/', 'projects/thumbs/', $image->filename);
            if (Storage::disk('public')->exists($thumbPath)) {
                Storage::disk('public')->delete($thumbPath);
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
     * Ottieni l'URL completo dell'immagine
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->filename);
    }

    /**
     * Ottieni l'URL della thumbnail
     */
    public function getThumbUrlAttribute()
    {
        $thumbPath = str_replace('projects/', 'projects/thumbs/', $this->filename);

        if (Storage::disk('public')->exists($thumbPath)) {
            return asset('storage/' . $thumbPath);
        }

        // Se la thumbnail non esiste, ritorna l'immagine originale
        return $this->url;
    }

    /**
     * Scope per ottenere solo le immagini della galleria
     */
    public function scopeGallery($query)
    {
        return $query->where('type', 'gallery');
    }

    /**
     * Scope per ordinamento
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }
}
