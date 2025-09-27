<?php

namespace App\Livewire\Admin\Projects;

use App\Models\Project;
use App\Models\ProjectCategory;
use Livewire\Component;
use Livewire\WithPagination;

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

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
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

    public function updating($propertyName)
    {
        if (in_array($propertyName, ['search', 'statusFilter', 'categoryFilter'])) {
            $this->resetPage();
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

    public function toggleProjectSelection($projectId)
    {
        if (in_array($projectId, $this->selectedProjects)) {
            $this->selectedProjects = array_diff($this->selectedProjects, [$projectId]);
        } else {
            $this->selectedProjects[] = $projectId;
        }
    }

    public function selectAllProjects()
    {
        $this->selectedProjects = $this->getProjects()->pluck('id')->toArray();
    }

    public function clearSelection()
    {
        $this->selectedProjects = [];
    }

    public function deleteProject($projectId)
    {
        try {
            $project = Project::findOrFail($projectId);

            // Elimina file associati
            if ($project->featured_image) {
                \Storage::disk('public')->delete($project->featured_image);
            }
            if ($project->gallery_images) {
                foreach ($project->gallery_images as $image) {
                    \Storage::disk('public')->delete($image);
                }
            }

            $project->delete();

            $this->dispatch('project-deleted', [
                'message' => 'Progetto eliminato con successo'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('project-error', [
                'message' => 'Errore nell\'eliminazione: ' . $e->getMessage()
            ]);
        }
    }

    public function bulkDelete()
    {
        if (empty($this->selectedProjects)) {
            return;
        }

        try {
            $projects = Project::whereIn('id', $this->selectedProjects)->get();

            foreach ($projects as $project) {
                // Elimina file
                if ($project->featured_image) {
                    \Storage::disk('public')->delete($project->featured_image);
                }
                if ($project->gallery_images) {
                    foreach ($project->gallery_images as $image) {
                        \Storage::disk('public')->delete($image);
                    }
                }
                $project->delete();
            }

            $count = count($this->selectedProjects);
            $this->selectedProjects = [];

            $this->dispatch('projects-bulk-deleted', [
                'message' => "Eliminati {$count} progetti con successo"
            ]);
        } catch (\Exception $e) {
            $this->dispatch('project-error', [
                'message' => 'Errore nell\'eliminazione multipla: ' . $e->getMessage()
            ]);
        }
    }

    public function refreshProjects()
    {
        // Forza il refresh della pagina corrente
        $this->resetPage();
    }

    public function getProjects()
    {
        $query = Project::with(['categories', 'technologies']);

        // Filtri
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('client', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->categoryFilter) {
            $query->whereHas('categories', function ($q) {
                $q->where('project_categories.id', $this->categoryFilter);
            });
        }

        // Ordinamento
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function getStats()
    {
        return [
            'total' => Project::count(),
            'published' => Project::where('status', 'published')->count(),
            'draft' => Project::where('status', 'draft')->count(),
            'featured' => Project::where('is_featured', true)->count(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.projects.index', [
            'projects' => $this->getProjects(),
            'categories' => ProjectCategory::ordered()->get(),
            'stats' => $this->getStats(),
        ])->layout('layouts.app');
    }
}
