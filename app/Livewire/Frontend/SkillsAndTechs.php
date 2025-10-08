<?php

namespace App\Livewire\Frontend;

use App\Models\ProjectTechnology;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Attributes\Computed;

class SkillsAndTechs extends Component
{
    public $showAll = false;
    public $maxItemsPerSection = 8;

    /**
     * Ottieni le tecnologie raggruppate per sezione
     */
    #[Computed]
    public function skillsSections(): array
    {
        // Cache query per performance
        $technologies = cache()->remember('skills_technologies', 3600, function () {
            return ProjectTechnology::query()
                ->select('id', 'name', 'category', 'icon', 'color')
                ->withCount('projects') // Conta i progetti associati
                ->orderBy('projects_count', 'desc') // Ordina per utilizzo
                ->orderBy('name')
                ->get();
        });

        // Mappatura categorie alle sezioni
        $categoryMapping = [
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

        // Inizializza le sezioni
        $sections = [
            'Frontend' => collect(),
            'Backend' => collect(),
            'Tools & Cloud' => collect(),
            'Concepts' => collect(),
        ];

        // Raggruppa le tecnologie per sezione
        $technologies->each(function ($tech) use (&$sections, $categoryMapping) {
            $section = $categoryMapping[$tech->category] ?? 'Concepts';
            $sections[$section][] = $tech;
        });

        // Converti in collezioni e rimuovi duplicati
        foreach ($sections as $key => $items) {
            $sections[$key] = collect($items)
                ->unique('name')
                ->sortByDesc('projects_count') // Priorità a quelle più usate
                ->values();

            // Limita il numero di elementi se non in modalità "show all"
            if (!$this->showAll && $sections[$key]->count() > $this->maxItemsPerSection) {
                $sections[$key] = $sections[$key]->take($this->maxItemsPerSection);
            }
        }

        return $sections;
    }

    /**
     * Toggle visualizzazione completa
     */
    public function toggleShowAll()
    {
        $this->showAll = !$this->showAll;
    }

    /**
     * Verifica se ci sono tecnologie nascoste
     */
    #[Computed]
    public function hasHiddenItems(): bool
    {
        foreach ($this->skillsSections as $section) {
            if ($section->count() > $this->maxItemsPerSection) {
                return true;
            }
        }
        return false;
    }

    /**
     * Ottieni statistiche delle tecnologie
     */
    #[Computed]
    public function stats(): array
    {
        return cache()->remember('skills_stats', 3600, function () {
            return [
                'total' => ProjectTechnology::count(),
                'with_projects' => ProjectTechnology::has('projects')->count(),
                'categories' => ProjectTechnology::distinct()->whereNotNull('category')->count('category'),
            ];
        });
    }

    public function render()
    {
        return view('livewire.frontend.skills-and-techs');
    }
}
