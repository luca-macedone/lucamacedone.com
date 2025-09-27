<?php

namespace App\Livewire\Frontend;

use App\Models\Project;
use App\Models\ProjectCategory;
use Livewire\Component;
use Livewire\WithPagination;

class Portfolio extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $sortBy = 'sort_order';
    public $sortDirection = 'asc';
    public $perPage = 12;

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'sortBy' => ['except' => 'sort_order'],
    ];

    public function mount()
    {
        // Inizializza eventuali filtri da URL
    }

    public function updating($propertyName)
    {
        if (in_array($propertyName, ['search', 'categoryFilter', 'sortBy'])) {
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
        $this->resetPage();
    }

    public function filterByCategory($categoryId)
    {
        $this->categoryFilter = $categoryId;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->categoryFilter = '';
        $this->sortBy = 'sort_order';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    public function getProjects()
    {
        $query = Project::with(['categories', 'technologies'])
            ->where('status', 'published') // Solo progetti pubblicati
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('client', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->categoryFilter, function ($query) {
                $query->whereHas('categories', function ($q) {
                    $q->where('project_categories.id', $this->categoryFilter);
                });
            });

        // Ordinamento speciale per progetti in evidenza
        if ($this->sortBy === 'featured') {
            $query->orderBy('is_featured', 'desc')
                ->orderBy('sort_order', 'asc');
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query->paginate($this->perPage);
    }

    public function getFeaturedProjects()
    {
        return Project::with(['categories', 'technologies'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->orderBy('sort_order', 'asc')
            ->limit(6)
            ->get();
    }

    public function getStats()
    {
        return [
            'total_projects' => Project::where('status', 'published')->count(),
            'categories_count' => ProjectCategory::whereHas('projects', function ($query) {
                $query->where('status', 'published');
            })->count(),
            'featured_count' => Project::where('status', 'published')
                ->where('is_featured', true)
                ->count(),
        ];
    }

    public function render()
    {
        return view('livewire.frontend.portfolio', [
            'projects' => $this->getProjects(),
            'featuredProjects' => $this->getFeaturedProjects(),
            'categories' => ProjectCategory::withCount(['projects' => function ($query) {
                $query->where('status', 'published');
            }])
                ->having('projects_count', '>', 0)
                ->orderBy('name')
                ->get(),
            'stats' => $this->getStats(),
        ])->layout('layouts.guest');
    }
}
