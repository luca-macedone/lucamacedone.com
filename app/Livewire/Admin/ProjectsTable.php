<?php

namespace App\Livewire\Admin;

use App\Models\Project;
use App\Models\ProjectCategory;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectsTable extends Component
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
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
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
                    \Storage::disk('public')->delete($project->featured_image);
                }

                if ($project->gallery) {
                    foreach ($project->gallery as $image) {
                        \Storage::disk('public')->delete($image);
                    }
                }

                $project->delete();
            }

            session()->flash('message', count($this->selectedProjects) . ' progetti eliminati con successo');
            $this->selectedProjects = [];
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nell\'eliminazione: ' . $e->getMessage());
        }
    }

    public function bulkUpdateStatus($status)
    {
        if (empty($this->selectedProjects)) {
            session()->flash('error', 'Nessun progetto selezionato');
            return;
        }

        try {
            Project::whereIn('id', $this->selectedProjects)->update(['status' => $status]);

            session()->flash('message', count($this->selectedProjects) . " progetti aggiornati a: {$status}");
            $this->selectedProjects = [];
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nell\'aggiornamento: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Per tabelle admin usa withFullDetails
        $query = Project::withFullDetails();

        // Applica filtri...
        if ($this->search) {
            $query->search($this->search);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->categoryFilter) {
            $query->inCategory($this->categoryFilter);
        }

        // Ordinamento
        $query->orderBy($this->sortBy, $this->sortDirection);

        $projects = $query->paginate($this->perPage);
        $categories = ProjectCategory::ordered()->get();

        return view('livewire.admin.projects-table', compact('projects', 'categories'));
    }
}
