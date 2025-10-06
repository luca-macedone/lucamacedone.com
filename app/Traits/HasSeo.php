<?php

namespace App\Traits;

use App\Models\ProjectSeo;
use Illuminate\Support\Str;

trait HasSeo
{
    /**
     * Relazione con i dati SEO
     */
    public function seo()
    {
        return $this->hasOne(ProjectSeo::class);
    }

    /**
     * Genera dati SEO automatici basati sul modello
     */
    public function generateSeoData(): array
    {
        return [
            'meta_title' => $this->generateMetaTitle(),
            'meta_description' => $this->generateMetaDescription(),
            'meta_keywords' => $this->generateMetaKeywords(),
        ];
    }

    /**
     * Genera meta title automatico
     */
    protected function generateMetaTitle(): string
    {
        if ($this->seo && $this->seo->meta_title) {
            return $this->seo->meta_title;
        }

        return Str::limit($this->title, 60);
    }

    /**
     * Genera meta description automatica
     */
    protected function generateMetaDescription(): string
    {
        if ($this->seo && $this->seo->meta_description) {
            return $this->seo->meta_description;
        }

        $description = $this->description ?? $this->content ?? '';
        return Str::limit(strip_tags($description), 160);
    }

    /**
     * Genera meta keywords automatiche
     */
    protected function generateMetaKeywords(): array
    {
        $keywords = [];

        // Se esistono keywords salvate, usale
        if ($this->seo && $this->seo->meta_keywords) {
            if (is_array($this->seo->meta_keywords)) {
                return $this->seo->meta_keywords;
            }
            if (is_string($this->seo->meta_keywords)) {
                return array_map('trim', explode(',', $this->seo->meta_keywords));
            }
        }

        // Genera keywords dalle relazioni se esistono
        if (method_exists($this, 'categories') && $this->relationLoaded('categories')) {
            $keywords = array_merge($keywords, $this->categories->pluck('name')->toArray());
        }

        if (method_exists($this, 'technologies') && $this->relationLoaded('technologies')) {
            $keywords = array_merge($keywords, $this->technologies->pluck('name')->toArray());
        }

        // Estrai parole chiave dal titolo (solo parole > 3 caratteri)
        if (isset($this->title)) {
            $titleWords = explode(' ', $this->title);
            $significantWords = array_filter($titleWords, fn($word) => strlen($word) > 3);
            $keywords = array_merge($keywords, $significantWords);
        }

        return array_unique($keywords);
    }

    /**
     * Crea o aggiorna i dati SEO
     */
    public function updateSeo(array $data): void
    {
        if (!$this->seo) {
            $this->seo()->create($data);
        } else {
            $this->seo->update($data);
        }
    }

    /**
     * Genera tutti i meta tags HTML
     */
    public function getMetaTagsHtml(): string
    {
        $seoData = $this->generateSeoData();
        $tags = [];

        // Title
        if (!empty($seoData['meta_title'])) {
            $tags[] = '<title>' . e($seoData['meta_title']) . '</title>';
            $tags[] = '<meta property="og:title" content="' . e($seoData['meta_title']) . '">';
        }

        // Description
        if (!empty($seoData['meta_description'])) {
            $tags[] = '<meta name="description" content="' . e($seoData['meta_description']) . '">';
            $tags[] = '<meta property="og:description" content="' . e($seoData['meta_description']) . '">';
        }

        // Keywords
        if (!empty($seoData['meta_keywords'])) {
            $keywordsString = is_array($seoData['meta_keywords'])
                ? implode(', ', $seoData['meta_keywords'])
                : $seoData['meta_keywords'];
            $tags[] = '<meta name="keywords" content="' . e($keywordsString) . '">';
        }

        // OG Image
        if ($this->seo && $this->seo->og_image) {
            $imageUrl = asset('storage/' . $this->seo->og_image);
            $tags[] = '<meta property="og:image" content="' . $imageUrl . '">';
        } elseif (isset($this->featured_image) && $this->featured_image) {
            $imageUrl = asset('storage/' . $this->featured_image);
            $tags[] = '<meta property="og:image" content="' . $imageUrl . '">';
        }

        // Type
        $tags[] = '<meta property="og:type" content="website">';

        // URL (se siamo in un contesto web)
        if (request()) {
            $tags[] = '<meta property="og:url" content="' . request()->url() . '">';
        }

        return implode("\n", $tags);
    }

    /**
     * Ottieni keywords come stringa
     */
    public function getKeywordsStringAttribute(): string
    {
        $keywords = $this->generateMetaKeywords();
        return implode(', ', $keywords);
    }

    /**
     * Imposta keywords da stringa
     */
    public function setKeywordsFromString(string $string): void
    {
        $keywords = array_map('trim', explode(',', $string));
        $keywords = array_filter($keywords); // Rimuovi elementi vuoti

        $this->updateSeo([
            'meta_keywords' => json_encode($keywords)
        ]);
    }

    /**
     * Controlla se ha dati SEO personalizzati
     */
    public function hasCustomSeo(): bool
    {
        return $this->seo && (
            $this->seo->meta_title ||
            $this->seo->meta_description ||
            $this->seo->meta_keywords ||
            $this->seo->og_image
        );
    }
}
