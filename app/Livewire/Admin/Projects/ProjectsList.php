<?php

namespace App\Livewire\Admin\Projects;

use App\Models\Project;
use App\Models\ProjectCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class ProjectsList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $categoryFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 15;
    public $selectedProjects = [];
    public $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $listeners = [
        'projectDeleted' => 'refreshProjects',
        'projectUpdated' => 'refreshProjects'
    ];

    public function mount()
    {
        // Reset selezioni quando si monta il componente
        $this->selectedProjects = [];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedProjects = $this->getProjects()->pluck('id')->toArray();
        } else {
            $this->selectedProjects = [];
        }
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortBy = $field;
    }

    /**
     * Toggle status del progetto
     */
    public function toggleStatus($projectId)
    {
        try {
            $project = Project::findOrFail($projectId);
            $newStatus = $project->status === 'published' ? 'draft' : 'published';
            $project->update(['status' => $newStatus]);

            session()->flash('message', "Status progetto aggiornato a: {$newStatus}");
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nell\'aggiornare lo status: ' . $e->getMessage());
        }
    }

    /**
     * Toggle featured del progetto
     */
    public function toggleFeatured($projectId)
    {
        try {
            $project = Project::findOrFail($projectId);
            $project->update(['is_featured' => !$project->is_featured]);

            $message = $project->is_featured ? 'Progetto messo in evidenza' : 'Progetto rimosso dall\'evidenza';
            session()->flash('message', $message);
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nell\'aggiornare l\'evidenza: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un singolo progetto
     */
    public function deleteProject($projectId)
    {
        try {
            $project = Project::findOrFail($projectId);

            // Elimina file associati
            if ($project->featured_image) {
                Storage::disk('public')->delete($project->featured_image);
            }

            if ($project->gallery_images) {
                $galleryImages = is_string($project->gallery_images)
                    ? json_decode($project->gallery_images, true)
                    : $project->gallery_images;

                if (is_array($galleryImages)) {
                    foreach ($galleryImages as $image) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }

            $project->delete();

            session()->flash('message', 'Progetto eliminato con successo');
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nell\'eliminazione: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete - Elimina progetti selezionati
     */
    public function bulkDelete()
    {
        if (empty($this->selectedProjects)) {
            session()->flash('error', 'Nessun progetto selezionato');
            return;
        }

        try {
            $projects = Project::whereIn('id', $this->selectedProjects)->get();

            foreach ($projects as $project) {
                // Elimina file associati
                if ($project->featured_image) {
                    Storage::disk('public')->delete($project->featured_image);
                }

                if ($project->gallery_images) {
                    $galleryImages = is_string($project->gallery_images)
                        ? json_decode($project->gallery_images, true)
                        : $project->gallery_images;

                    if (is_array($galleryImages)) {
                        foreach ($galleryImages as $image) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                }

                $project->delete();
            }

            $count = count($this->selectedProjects);
            $this->selectedProjects = [];
            $this->selectAll = false;

            session()->flash('message', "Eliminati {$count} progetti con successo");
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nell\'eliminazione multipla: ' . $e->getMessage());
        }
    }

    /**
     * Bulk publish - Pubblica progetti selezionati
     */
    public function bulkPublish()
    {
        if (empty($this->selectedProjects)) {
            session()->flash('error', 'Nessun progetto selezionato');
            return;
        }

        try {
            Project::whereIn('id', $this->selectedProjects)
                ->update(['status' => 'published']);

            $count = count($this->selectedProjects);
            $this->selectedProjects = [];
            $this->selectAll = false;

            session()->flash('message', "{$count} progetti pubblicati con successo");
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nella pubblicazione: ' . $e->getMessage());
        }
    }

    /**
     * Bulk feature - Metti in evidenza progetti selezionati
     */
    public function bulkFeature()
    {
        if (empty($this->selectedProjects)) {
            session()->flash('error', 'Nessun progetto selezionato');
            return;
        }

        try {
            Project::whereIn('id', $this->selectedProjects)
                ->update(['is_featured' => true]);

            $count = count($this->selectedProjects);
            $this->selectedProjects = [];
            $this->selectAll = false;

            session()->flash('message', "{$count} progetti messi in evidenza");
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nell\'evidenziazione: ' . $e->getMessage());
        }
    }

    /**
     * Refresh della lista progetti
     */
    public function refreshProjects()
    {
        // Forza il refresh della pagina corrente
        $this->resetPage();
    }

    /**
     * Ottieni progetti con scope appropriato per admin
     */
    private function getProjects()
    {
        // Usa withFullDetails per admin - carica tutto
        $query = Project::withFullDetails();

        // Applica filtri di ricerca
        if ($this->search) {
            $query->search($this->search);
        }

        // Filtro stato
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Filtro categoria
        if ($this->categoryFilter) {
            $query->inCategory($this->categoryFilter);
        }

        // Applica ordinamento
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    /**
     * Ottieni statistiche progetti (senza relazioni)
     */
    private function getStats()
    {
        return [
            'total' => Project::forStats()->count(),
            'published' => Project::forStats()->published()->count(),
            'draft' => Project::forStats()->where('status', 'draft')->count(),
            'featured' => Project::forStats()->featured()->count(),
        ];
    }

    /**
     * Render del componente
     */
    public function render()
    {
        return view('livewire.admin.projects.index', [
            'projects' => $this->getProjects(),
            'categories' => ProjectCategory::orderBy('name')->get(),
            'stats' => $this->getStats(),
        ])->layout('layouts.app');
    }
}
