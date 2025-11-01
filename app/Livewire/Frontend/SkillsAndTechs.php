<?php

namespace App\Livewire\Frontend;

use App\Models\ProjectTechnology;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\Computed;

class SkillsAndTechs extends Component
{
    public $showAll = false;
    public $maxItemsPerSection = 8;

    /**
     * Durata cache in secondi (1 ora)
     */
    private const CACHE_TTL = 3600;

    /**
     * Ottieni le tecnologie raggruppate per sezione
     */
    #[Computed]
    public function skillsSections(): array
    {
        // Usa cache con tag per facilitare l'invalidazione
        $cacheKey = 'skills_technologies';
        $cacheTags = ['skills_technologies', 'project_technologies'];

        $technologies = $this->getCachedTechnologies($cacheKey, $cacheTags);

        // Mappatura categorie alle sezioni
        $categoryMapping = $this->getCategoryMapping();

        // Inizializza le sezioni
        $sections = $this->initializeSections();

        // Raggruppa le tecnologie per sezione
        $technologies->each(function ($tech) use (&$sections, $categoryMapping) {
            $section = $categoryMapping[$tech->category] ?? 'Concepts';
            $sections[$section][] = $tech;
        });

        // Processa e limita le sezioni
        return $this->processSections($sections);
    }

    /**
     * Recupera le tecnologie dalla cache o dal database
     */
    private function getCachedTechnologies(string $cacheKey, array $cacheTags): Collection
    {
        try {
            // Prova prima con i tag se supportati
            if ($this->supportsCacheTags()) {
                return Cache::tags($cacheTags)->remember($cacheKey, self::CACHE_TTL, function () {
                    return $this->fetchTechnologies();
                });
            }
        } catch (\Exception $e) {
            // Fallback senza tag se non supportati
        }

        // Fallback: cache semplice senza tag
        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return $this->fetchTechnologies();
        });
    }

    /**
     * Fetch delle tecnologie dal database
     */
    private function fetchTechnologies(): Collection
    {
        return ProjectTechnology::query()
            ->select('id', 'name', 'category', 'icon', 'color')
            ->withCount('projects')
            ->having('projects_count', '>', 0) // Solo tecnologie usate
            ->orderBy('projects_count', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Mappatura delle categorie
     */
    private function getCategoryMapping(): array
    {
        return [
            'Frontend' => 'Frontend',
            'Backend' => 'Backend',
            'Database' => 'Backend',
            'Tool' => 'Tools & Cloud',
            'Cloud' => 'Tools & Cloud',
            'DevOps' => 'Tools & Cloud',
            'Testing' => 'Concepts',
            'Design' => 'Concepts',
            'AI/ML' => 'Concepts',
            'Framework' => 'Backend',
            'Mobile' => 'Frontend',
        ];
    }

    /**
     * Inizializza le sezioni vuote
     */
    private function initializeSections(): array
    {
        return [
            'Frontend' => collect(),
            'Backend' => collect(),
            'Tools & Cloud' => collect(),
            'Concepts' => collect(),
        ];
    }

    /**
     * Processa e limita le sezioni
     */
    private function processSections(array $sections): array
    {
        foreach ($sections as $key => $items) {
            $sections[$key] = collect($items)
                ->unique('name')
                ->sortByDesc('projects_count')
                ->values();

            // Limita elementi se non in modalità "show all"
            if (!$this->showAll && $sections[$key]->count() > $this->maxItemsPerSection) {
                $sections[$key] = $sections[$key]->take($this->maxItemsPerSection);
            }
        }

        return $sections;
    }

    /**
     * Toggle visualizzazione completa
     */
    public function toggleShowAll(): void
    {
        $this->showAll = !$this->showAll;

        // Invalida la cache computed property
        unset($this->skillsSections);
    }

    /**
     * Verifica se ci sono tecnologie nascoste
     */
    #[Computed]
    public function hasHiddenItems(): bool
    {
        // Conta tecnologie totali senza limitazioni
        $totalTechnologies = ProjectTechnology::has('projects')->count();

        // Conta tecnologie mostrate
        $shownTechnologies = collect($this->skillsSections)
            ->map->count()
            ->sum();

        return $totalTechnologies > $shownTechnologies;
    }

    /**
     * Ottieni statistiche delle tecnologie
     */
    #[Computed]
    public function stats(): array
    {
        $cacheKey = 'skills_stats';
        $cacheTags = ['skills_stats', 'project_technologies'];

        try {
            if ($this->supportsCacheTags()) {
                return Cache::tags($cacheTags)->remember($cacheKey, self::CACHE_TTL, function () {
                    return $this->calculateStats();
                });
            }
        } catch (\Exception $e) {
            // Fallback senza tag
        }

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return $this->calculateStats();
        });
    }

    /**
     * Calcola le statistiche
     */
    private function calculateStats(): array
    {
        return [
            'total' => ProjectTechnology::count(),
            'with_projects' => ProjectTechnology::has('projects')->count(),
            'categories' => ProjectTechnology::distinct()
                ->whereNotNull('category')
                ->count('category'),
            'most_used' => ProjectTechnology::withCount('projects')
                ->orderBy('projects_count', 'desc')
                ->first(),
        ];
    }

    /**
     * Verifica se il driver cache supporta i tag
     */
    private function supportsCacheTags(): bool
    {
        $driver = config('cache.default');
        return in_array($driver, ['redis', 'memcached', 'dynamodb', 'array']);
    }

    /**
     * Forza il refresh della cache (utile per testing)
     */
    public function refreshCache(): void
    {
        if ($this->supportsCacheTags()) {
            Cache::tags(['skills_technologies', 'skills_stats'])->flush();
        } else {
            Cache::forget('skills_technologies');
            Cache::forget('skills_stats');
        }

        // Invalida computed properties
        unset($this->skillsSections);
        unset($this->stats);

        $this->dispatch('cache-refreshed');
    }

    public function render()
    {
        return view('livewire.frontend.skills-and-techs');
    }
}
