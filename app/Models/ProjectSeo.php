<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectSeo extends Model
{
    use HasFactory;

    protected $table = 'project_seo';

    protected $fillable = [
        'project_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
    ];

    protected $casts = [
        'meta_keywords' => 'array', // Cast automatico JSON to array
    ];

    /**
     * Relazione con il progetto
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Ottieni l'URL completo dell'immagine OG
     */
    public function getOgImageUrlAttribute()
    {
        return $this->og_image
            ? asset('storage/' . $this->og_image)
            : null;
    }

    /**
     * Ottieni keywords come stringa (per form edit)
     */
    public function getKeywordsStringAttribute()
    {
        if ($this->meta_keywords) {
            return is_array($this->meta_keywords)
                ? implode(', ', $this->meta_keywords)
                : $this->meta_keywords;
        }
        return '';
    }

    /**
     * Imposta keywords da stringa
     */
    public function setKeywordsFromString($string)
    {
        if ($string) {
            $this->meta_keywords = array_map('trim', explode(',', $string));
        } else {
            $this->meta_keywords = null;
        }
    }
}
