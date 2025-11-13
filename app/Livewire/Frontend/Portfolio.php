<?php

namespace App\Livewire\Frontend;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class Portfolio extends Component
{
    use WithPagination;

    // Cache configuration
    private const CACHE_TTL = 3600; // 1 ora
    private const CACHE_PREFIX = 'luca_macedone_cache_';

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
        // Usa withBasicInfo per il frontend pubblico
        $query = Project::withBasicInfo()
            ->published();

        // Applica ricerca
        if ($this->search) {
            $query->search($this->search);
        }

        // Filtro categoria
        if ($this->categoryFilter && $this->categoryFilter !== 'all') {
            $query->inCategory($this->categoryFilter);
        }

        // Ordinamento
        switch ($this->sortBy) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name':
                $query->orderBy('title', 'asc');
                break;
            case 'featured':
                $query->featured()->ordered();
                break;
            default: // 'recent'
                $query->latest();
                break;
        }

        return $query->paginate($this->perPage);
    }

    public function getFeaturedProjects()
    {
        $cacheKey = self::CACHE_PREFIX . 'portfolio_featured_projects';

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return Project::with(['categories', 'technologies'])
                ->where('status', 'published')
                ->where('is_featured', true)
                ->orderBy('sort_order', 'asc')
                ->limit(6)
                ->get();
        });
    }

    public function getStats()
    {
        $cacheKey = self::CACHE_PREFIX . 'portfolio_stats';

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return [
                'total_projects' => Project::where('status', 'published')->count(),
                'categories_count' => ProjectCategory::whereHas('projects', function ($query) {
                    $query->where('status', 'published');
                })->count(),
                'featured_count' => Project::where('status', 'published')
                    ->where('is_featured', true)
                    ->count(),
            ];
        });
    }

    public function getCategories()
    {
        $cacheKey = self::CACHE_PREFIX . 'portfolio_categories';

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return ProjectCategory::has('projects')
                ->withCount('projects')
                ->ordered()
                ->get();
        });
    }

    public function render()
    {
        return view('livewire.frontend.portfolio', [
            'projects' => $this->getProjects(),
            'categories' => $this->getCategories(),
        ])->layout('layouts.guest');
    }
}
