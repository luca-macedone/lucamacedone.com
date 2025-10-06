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
     * Genera meta tags HTML
     */
    public function generateMetaTags(): string
    {
        $tags = [];

        if ($this->meta_title) {
            $tags[] = '<title>' . e($this->meta_title) . '</title>';
            $tags[] = '<meta property="og:title" content="' . e($this->meta_title) . '">';
        }

        if ($this->meta_description) {
            $tags[] = '<meta name="description" content="' . e($this->meta_description) . '">';
            $tags[] = '<meta property="og:description" content="' . e($this->meta_description) . '">';
        }

        if ($this->meta_keywords && count($this->meta_keywords) > 0) {
            $keywords = is_array($this->meta_keywords)
                ? implode(', ', $this->meta_keywords)
                : $this->meta_keywords;
            $tags[] = '<meta name="keywords" content="' . e($keywords) . '">';
        }

        if ($this->og_image) {
            $tags[] = '<meta property="og:image" content="' . $this->og_image_url . '">';
        }

        return implode("\n    ", $tags);
    }

    /**
     * Ottieni keywords come stringa
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
