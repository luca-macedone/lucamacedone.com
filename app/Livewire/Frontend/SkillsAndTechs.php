<?php

namespace App\Livewire\Frontend;

use App\Models\ProjectTechnology;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SkillsAndTechs extends Component
{
    public $selectedCategory = 'all';
    public $searchTerm = '';

    // Cache configuration
    private const CACHE_TTL = 3600;
    private const CACHE_PREFIX = 'luca_macedone_cache_';

    /**
     * Mount del componente
     */
    public function mount()
    {
        // Pre-carica le statistiche
        $this->getStats();
    }

    /**
     * Recupera le statistiche con cache
     */
    #[Computed(cache: true)]
    public function stats()
    {
        return ProjectTechnology::getSkillsStats();
    }

    /**
     * Recupera le categorie disponibili
     */
    #[Computed(cache: true)]
    public function categories()
    {
        $cacheKey = self::CACHE_PREFIX . 'technologies_categories_list';

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return ProjectTechnology::query()
                ->select('category')
                ->distinct()
                ->whereNotNull('category')
                ->orderBy('category')
                ->pluck('category')
                ->filter()
                ->map(function ($category) {
                    return [
                        'value' => $category,
                        'label' => ucfirst($category),
                        'count' => ProjectTechnology::where('category', $category)->count()
                    ];
                })
                ->filter(fn($cat) => $cat['count'] > 0)
                ->values();
        });
    }

    /**
     * Recupera le tecnologie filtrate
     */
    #[Computed]
    public function technologies()
    {
        // Se c'è una ricerca attiva, non usiamo la cache
        if ($this->searchTerm) {
            return $this->searchTechnologies();
        }

        // Altrimenti usiamo la cache basata sulla categoria
        if ($this->selectedCategory === 'all') {
            return ProjectTechnology::getWithProjectsCount();
        }

        return ProjectTechnology::getByCategory($this->selectedCategory);
    }

    /**
     * Recupera le tecnologie raggruppate per sezioni
     */
    #[Computed]
    public function skillsSections()
    {
        $technologies = ProjectTechnology::query()
            ->select(['id', 'name', 'category', 'icon', 'color'])
            ->withCount('projects')
            ->having('projects_count', '>', 0)
            ->orderBy('name')
            ->get();

        // Raggruppa per categoria
        return $technologies->groupBy('category');
    }

    /**
     * Cerca tecnologie senza cache
     */
    private function searchTechnologies()
    {
        $query = ProjectTechnology::query()
            ->select(['id', 'name', 'category', 'icon', 'color'])
            ->withCount('projects');

        // Filtro per categoria
        if ($this->selectedCategory !== 'all') {
            $query->where('category', $this->selectedCategory);
        }

        // Filtro per termine di ricerca
        if ($this->searchTerm) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        }

        return $query->having('projects_count', '>', 0)
            ->orderByDesc('projects_count')
            ->orderBy('name')
            ->get();
    }

    /**
     * Aggiorna la categoria selezionata
     */
    public function setCategory($category)
    {
        $this->selectedCategory = $category;
        $this->searchTerm = ''; // Reset ricerca quando si cambia categoria
        $this->dispatch('category-changed', category: $category);
    }

    /**
     * Reset dei filtri
     */
    public function resetFilters()
    {
        $this->selectedCategory = 'all';
        $this->searchTerm = '';
        $this->dispatch('filters-reset');
    }

    /**
     * Aggiorna il termine di ricerca
     */
    public function updatedSearchTerm()
    {
        // Debounce è gestito da Livewire con wire:model.live.debounce
        $this->dispatch('search-updated', term: $this->searchTerm);
    }

    /**
     * Recupera le statistiche generali
     */
    private function getStats()
    {
        return $this->stats;
    }

    /**
     * Invalida la cache quando necessario
     */
    public function refreshCache()
    {
        ProjectTechnology::clearAllCache();
        $this->dispatch('cache-refreshed');
    }

    /**
     * Render del componente
     */
    public function render()
    {
        return view('livewire.frontend.skills-and-techs', [
            'technologies' => $this->technologies,
            'categories' => $this->categories,
            'stats' => $this->stats
        ]);
    }
}
