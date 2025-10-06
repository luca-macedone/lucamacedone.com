<?php

namespace App\Livewire\Admin\Media;

use App\Models\Project;
use App\Models\ProjectImage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class GalleryManager extends Component
{
    use WithFileUploads;

    public Project $project;
    public $images = [];
    public $newImages = [];
    public $editingImage = null;

    // Campi per editing
    public $imageTitle = '';
    public $imageAltText = '';
    public $imageCaption = '';
    public $imageSortOrder = 0;

    // Upload multiplo
    public $uploadQueue = [];

    protected $rules = [
        'newImages.*' => 'image|max:4096|mimes:jpg,jpeg,png,webp',
        'imageTitle' => 'nullable|string|max:255',
        'imageAltText' => 'nullable|string|max:255',
        'imageCaption' => 'nullable|string|max:500',
        'imageSortOrder' => 'integer|min:0',
    ];

    protected $messages = [
        'newImages.*.image' => 'Il file deve essere un\'immagine.',
        'newImages.*.max' => 'L\'immagine non può superare i 4MB.',
        'newImages.*.mimes' => 'Formato non supportato. Usa: JPG, PNG, WebP.',
    ];

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->loadImages();
    }

    /**
     * Carica le immagini della galleria
     */
    public function loadImages()
    {
        $this->images = $this->project->galleryImages()
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get()
            ->toArray();
    }

    /**
     * Upload batch di immagini
     */
    public function uploadImages()
    {
        $this->validate([
            'newImages.*' => 'image|max:4096|mimes:jpg,jpeg,png,webp'
        ]);

        DB::beginTransaction();

        try {
            $maxSortOrder = ProjectImage::where('project_id', $this->project->id)
                ->max('sort_order') ?? -1;

            foreach ($this->newImages as $index => $image) {
                $path = $image->store('projects/gallery/' . $this->project->id, 'public');

                ProjectImage::create([
                    'project_id' => $this->project->id,
                    'filename' => $path,
                    'type' => 'gallery',
                    'title' => null,
                    'alt_text' => $this->project->title . ' - Immagine ' . ($index + 1),
                    'caption' => null,
                    'sort_order' => $maxSortOrder + $index + 1,
                ]);
            }

            DB::commit();

            $this->reset('newImages');
            $this->loadImages();

            session()->flash('success', count($this->newImages) . ' immagini caricate con successo.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Errore durante il caricamento: ' . $e->getMessage());
        }
    }

    /**
     * Inizia editing di un'immagine
     */
    public function editImage($imageId)
    {
        $image = ProjectImage::findOrFail($imageId);

        if ($image->project_id !== $this->project->id) {
            abort(403);
        }

        $this->editingImage = $imageId;
        $this->imageTitle = $image->title;
        $this->imageAltText = $image->alt_text;
        $this->imageCaption = $image->caption;
        $this->imageSortOrder = $image->sort_order;
    }

    /**
     * Salva modifiche immagine
     */
    public function updateImage()
    {
        $this->validate([
            'imageTitle' => 'nullable|string|max:255',
            'imageAltText' => 'nullable|string|max:255',
            'imageCaption' => 'nullable|string|max:500',
            'imageSortOrder' => 'integer|min:0',
        ]);

        $image = ProjectImage::findOrFail($this->editingImage);

        if ($image->project_id !== $this->project->id) {
            abort(403);
        }

        $image->update([
            'title' => $this->imageTitle,
            'alt_text' => $this->imageAltText,
            'caption' => $this->imageCaption,
            'sort_order' => $this->imageSortOrder,
        ]);

        $this->cancelEdit();
        $this->loadImages();

        session()->flash('success', 'Immagine aggiornata con successo.');
    }

    /**
     * Annulla editing
     */
    public function cancelEdit()
    {
        $this->reset(['editingImage', 'imageTitle', 'imageAltText', 'imageCaption', 'imageSortOrder']);
    }

    /**
     * Elimina immagine
     */
    public function deleteImage($imageId)
    {
        $image = ProjectImage::findOrFail($imageId);

        if ($image->project_id !== $this->project->id) {
            abort(403);
        }

        try {
            // Elimina file fisico
            if (Storage::disk('public')->exists($image->filename)) {
                Storage::disk('public')->delete($image->filename);
            }

            $image->delete();

            $this->loadImages();
            session()->flash('success', 'Immagine eliminata con successo.');
        } catch (\Exception $e) {
            session()->flash('error', 'Errore durante l\'eliminazione: ' . $e->getMessage());
        }
    }

    /**
     * Riordina immagini (drag & drop)
     */
    public function reorderImages($orderedIds)
    {
        try {
            foreach ($orderedIds as $index => $imageId) {
                ProjectImage::where('id', $imageId)
                    ->where('project_id', $this->project->id)
                    ->update(['sort_order' => $index]);
            }

            $this->loadImages();
            session()->flash('success', 'Ordine immagini aggiornato.');
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nel riordinamento: ' . $e->getMessage());
        }
    }

    /**
     * Genera automaticamente alt text basato su AI/Pattern
     */
    public function generateAltText($imageId)
    {
        $image = ProjectImage::findOrFail($imageId);

        // Pattern base per generazione alt text
        $altText = sprintf(
            '%s - %s %d',
            $this->project->title,
            $this->project->client ? 'per ' . $this->project->client : 'Progetto',
            array_search($imageId, array_column($this->images, 'id')) + 1
        );

        $image->update(['alt_text' => $altText]);

        $this->loadImages();
        session()->flash('success', 'Alt text generato automaticamente.');
    }

    /**
     * Imposta immagine come featured
     */
    public function setAsFeatured($imageId)
    {
        $image = ProjectImage::findOrFail($imageId);

        if ($image->project_id !== $this->project->id) {
            abort(403);
        }

        try {
            // Copia l'immagine come featured
            $newPath = 'projects/featured/' . $this->project->id . '_featured.' .
                pathinfo($image->filename, PATHINFO_EXTENSION);

            Storage::disk('public')->copy($image->filename, $newPath);

            // Rimuovi vecchia featured se esiste
            if ($this->project->featured_image && Storage::disk('public')->exists($this->project->featured_image)) {
                Storage::disk('public')->delete($this->project->featured_image);
            }

            // Aggiorna progetto
            $this->project->update(['featured_image' => $newPath]);

            session()->flash('success', 'Immagine impostata come principale.');
        } catch (\Exception $e) {
            session()->flash('error', 'Errore: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.media.gallery-manager');
    }
}
