<?php

namespace App\Livewire\Frontend;

use App\Models\Project;
use Livewire\Component;
use Illuminate\Support\Str;

class ProjectShow extends Component
{
    public Project $project;
    public $currentImageIndex = 0;
    public $showImageModal = false;
    public $allImages = [];

    protected $listeners = [
        'imageClicked' => 'openImageModal'
    ];

    public function mount($slug)
    {
        $this->project = Project::forShow()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $this->allImages = $this->getAllImages();
    }

    public function openImageModal($index = 0)
    {
        $this->currentImageIndex = $index;
        $this->showImageModal = true;
    }

    public function closeImageModal()
    {
        $this->showImageModal = false;
    }

    public function nextImage()
    {
        $totalImages = count($this->allImages);
        if ($totalImages > 0) {
            $this->currentImageIndex = ($this->currentImageIndex + 1) % $totalImages;
        }
    }

    public function previousImage()
    {
        $totalImages = count($this->allImages);
        if ($totalImages > 0) {
            $this->currentImageIndex = ($this->currentImageIndex - 1 + $totalImages) % $totalImages;
        }
    }

    public function getAllImages()
    {
        $images = [];

        // Aggiungi featured image se presente
        if ($this->project->featured_image) {
            $images[] = [
                'url' => asset('storage/' . $this->project->featured_image),
                'alt' => $this->project->title . ' - Featured Image',
                'caption' => null
            ];
        }

        // Aggiungi immagini dalla galleria (dalla tabella project_images)
        foreach ($this->project->galleryImages as $image) {
            $images[] = [
                'url' => asset('storage/' . $image->filename),
                'alt' => $image->alt_text ?? $this->project->title . ' - Gallery',
                'caption' => $image->caption
            ];
        }

        return $images;
    }

    public function getRelatedProjects()
    {
        // Trova progetti correlati basati sulle categorie
        $categoryIds = $this->project->categories->pluck('id');

        if ($categoryIds->isEmpty()) {
            // Se non ci sono categorie, prendi progetti recenti
            return Project::with(['categories', 'technologies'])
                ->where('status', 'published')
                ->where('id', '!=', $this->project->id)
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get();
        }

        $related = Project::with(['categories', 'technologies'])
            ->where('status', 'published')
            ->where('id', '!=', $this->project->id)
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('project_categories.id', $categoryIds);
            })
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        // Se non ci sono abbastanza progetti correlati, aggiungi altri progetti recenti
        if ($related->count() < 4) {
            $additionalProjects = Project::with(['categories', 'technologies'])
                ->where('status', 'published')
                ->where('id', '!=', $this->project->id)
                ->whereNotIn('id', $related->pluck('id'))
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(4 - $related->count())
                ->get();

            $related = $related->merge($additionalProjects);
        }

        return $related;
    }

    public function shareProject($platform)
    {
        $url = route('portfolio.show', $this->project->slug);
        $title = $this->project->title;
        $description = $this->project->description;

        $shareUrls = [
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($url),
            'twitter' => "https://twitter.com/intent/tweet?url=" . urlencode($url) . "&text=" . urlencode($title),
            'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode($url),
            'whatsapp' => "https://wa.me/?text=" . urlencode($title . ' - ' . $url),
            'telegram' => "https://t.me/share/url?url=" . urlencode($url) . "&text=" . urlencode($title),
            'email' => "mailto:?subject=" . urlencode($title) . "&body=" . urlencode($description . "\n\n" . $url),
        ];

        if (isset($shareUrls[$platform])) {
            if ($platform === 'email') {
                // Per email, usa redirect invece di JS
                return redirect($shareUrls[$platform]);
            } else {
                // Per altri platform, apri in popup
                $this->dispatch('open-share-window', [
                    'url' => $shareUrls[$platform]
                ]);
            }
        }
    }

    public function copyProjectLink()
    {
        $this->dispatch('copy-to-clipboard', [
            'text' => route('portfolio.show', $this->project->slug),
            'message' => 'Link copiato negli appunti!'
        ]);
    }

    public function render()
    {
        // Usa i dati SEO dalla tabella project_seo se disponibili
        $metaTitle = $this->project->seo?->meta_title
            ?? $this->project->meta_title
            ?? $this->project->title . ' - Portfolio';

        $metaDescription = $this->project->seo?->meta_description
            ?? $this->project->meta_description
            ?? Str::limit(strip_tags($this->project->description), 160);

        $metaKeywords = $this->project->seo?->keywords_string ?? '';

        $ogImage = $this->project->seo?->og_image
            ? asset('storage/' . $this->project->seo->og_image)
            : ($this->project->featured_image
                ? asset('storage/' . $this->project->featured_image)
                : null);

        return view('livewire.frontend.project-show', [
            'project' => $this->project,
            'allImages' => $this->allImages,
            'relatedProjects' => $this->getRelatedProjects(),
        ])->layout('layouts.guest', [
            'title' => $metaTitle,
            'description' => $metaDescription,
            'keywords' => $metaKeywords,
            'ogImage' => $ogImage,
        ]);
    }
}
