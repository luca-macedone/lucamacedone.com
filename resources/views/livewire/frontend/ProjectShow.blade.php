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

    protected $listeners = [
        'imageClicked' => 'openImageModal',
    ];

    public function mount($slug)
    {
        // Trova il progetto tramite slug
        $this->project = Project::where('slug', $slug)
            ->where('status', 'published')
            ->with(['categories', 'technologies'])
            ->firstOrFail();
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
        $totalImages = count($this->getAllImages());
        if ($totalImages > 0) {
            $this->currentImageIndex = ($this->currentImageIndex + 1) % $totalImages;
        }
    }

    public function previousImage()
    {
        $totalImages = count($this->getAllImages());
        if ($totalImages > 0) {
            $this->currentImageIndex = ($this->currentImageIndex - 1 + $totalImages) % $totalImages;
        }
    }

    public function getAllImages()
    {
        $images = [];

        // Aggiungi featured image se presente
        if ($this->project->featured_image) {
            $images[] = $this->project->featured_image;
        }

        // Gestisci gallery_images che può essere stringa JSON o array
        if ($this->project->gallery_images) {
            $galleryImages = $this->project->gallery_images;

            // Se è una stringa JSON, decodificala
            if (is_string($galleryImages)) {
                $galleryImages = json_decode($galleryImages, true) ?? [];
            }

            // Se ora è un array, aggiungilo alle immagini
            if (is_array($galleryImages)) {
                $images = array_merge($images, $galleryImages);
            }
        }

        return $images;
    }

    public function getRelatedProjects()
    {
        // Trova progetti correlati basati sulle categorie
        $categoryIds = $this->project->categories->pluck('id');

        $related = Project::with(['categories', 'technologies'])
            ->where('status', 'published')
            ->where('id', '!=', $this->project->id)
            ->when($categoryIds->isNotEmpty(), function ($query) use ($categoryIds) {
                $query->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('project_categories.id', $categoryIds);
                });
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
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url),
            'twitter' => 'https://twitter.com/intent/tweet?url=' . urlencode($url) . '&text=' . urlencode($title),
            'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($url),
            'whatsapp' => 'https://wa.me/?text=' . urlencode($title . ' - ' . $url),
            'telegram' => 'https://t.me/share/url?url=' . urlencode($url) . '&text=' . urlencode($title),
        ];

        if (isset($shareUrls[$platform])) {
            $this->dispatch('open-share-window', [
                'url' => $shareUrls[$platform],
            ]);
        }
    }

    public function copyProjectLink()
    {
        $this->dispatch('copy-to-clipboard', [
            'text' => route('portfolio.show', $this->project->slug),
            'message' => 'Link copiato negli appunti!',
        ]);
    }

    public function render()
    {
        $metaTitle = $this->project->meta_title ?: $this->project->title;
        $metaDescription = $this->project->meta_description ?: Str::limit($this->project->description, 160);

        return view('livewire.frontend.project-show', [
            'project' => $this->project,
            'allImages' => $this->getAllImages(),
            'relatedProjects' => $this->getRelatedProjects(),
        ])->layout('layouts.guest', [
            'title' => $metaTitle,
            'description' => $metaDescription,
        ]);
    }
}
