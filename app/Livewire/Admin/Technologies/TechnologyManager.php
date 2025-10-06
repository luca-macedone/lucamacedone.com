<?php

namespace App\Livewire\Admin\Technologies;

use App\Models\ProjectTechnology;
use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class TechnologyManager extends Component
{
    use WithPagination;

    // Filtri e ricerca
    public $search = '';
    public $categoryFilter = '';
    public $perPage = 15;

    // Form fields
    public $showForm = false;
    public $editingId = null;
    public $name = '';
    public $category = '';
    public $icon = '';
    public $color = '#6B7280';
    public $newCategory = '';
    public $useNewCategory = false;

    // Selezione multipla
    public $selectedTechnologies = [];
    public $selectAll = false;

    // Categorie disponibili
    public $availableCategories = [];

    // Ordinamento
    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $rules = [
        'name' => 'required|string|max:255',
        'category' => 'required_if:useNewCategory,false|nullable|string|max:100',
        'newCategory' => 'required_if:useNewCategory,true|nullable|string|max:100',
        'icon' => 'nullable|string|max:255',
        'color' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
    ];

    protected $messages = [
        'name.required' => 'Il nome è obbligatorio.',
        'name.max' => 'Il nome non può superare i 255 caratteri.',
        'category.required_if' => 'Seleziona una categoria o creane una nuova.',
        'newCategory.required_if' => 'Inserisci il nome della nuova categoria.',
        'color.regex' => 'Il colore deve essere in formato esadecimale valido.',
    ];

    public function mount()
    {
        $this->loadCategories();
    }

    /**
     * Carica categorie disponibili
     */
    public function loadCategories()
    {
        // Categorie da config
        $configCategories = config('projects.technologies.categories', [
            'Frontend',
            'Backend',
            'Database',
            'Framework',
            'Tool',
            'Cloud',
            'Mobile',
            'Design',
            'Testing',
            'DevOps',
            'AI/ML'
        ]);

        // Categorie esistenti nel database
        $dbCategories = ProjectTechnology::distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->toArray();

        // Unisci e ordina
        $this->availableCategories = collect($configCategories)
            ->merge($dbCategories)
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Toggle form creazione/modifica
     */
    public function toggleForm($editId = null)
    {
        if ($editId) {
            $this->edit($editId);
        } else {
            $this->showForm = !$this->showForm;
            if (!$this->showForm) {
                $this->resetForm();
            }
        }
    }

    /**
     * Reset form
     */
    public function resetForm()
    {
        $this->reset(['name', 'category', 'icon', 'color', 'newCategory', 'useNewCategory', 'editingId']);
        $this->color = '#6B7280';
        $this->showForm = false;
    }

    /**
     * Crea nuova tecnologia
     */
    public function create()
    {
        $this->validate();

        try {
            $categoryToUse = $this->useNewCategory ? $this->newCategory : $this->category;

            ProjectTechnology::create([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'category' => $categoryToUse,
                'icon' => $this->icon ?: null,
                'color' => $this->color,
            ]);

            session()->flash('success', 'Tecnologia creata con successo.');

            $this->resetForm();
            $this->loadCategories();
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nella creazione: ' . $e->getMessage());
        }
    }

    /**
     * Modifica tecnologia esistente
     */
    public function edit($id)
    {
        $technology = ProjectTechnology::findOrFail($id);

        $this->editingId = $id;
        $this->name = $technology->name;
        $this->category = $technology->category;
        $this->icon = $technology->icon;
        $this->color = $technology->color ?: '#6B7280';
        $this->showForm = true;
        $this->useNewCategory = false;
    }

    /**
     * Aggiorna tecnologia
     */
    public function update()
    {
        $this->validate();

        try {
            $technology = ProjectTechnology::findOrFail($this->editingId);

            $categoryToUse = $this->useNewCategory ? $this->newCategory : $this->category;

            $technology->update([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'category' => $categoryToUse,
                'icon' => $this->icon ?: null,
                'color' => $this->color,
            ]);

            session()->flash('success', 'Tecnologia aggiornata con successo.');

            $this->resetForm();
            $this->loadCategories();
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nell\'aggiornamento: ' . $e->getMessage());
        }
    }

    /**
     * Elimina tecnologia
     */
    public function delete($id)
    {
        try {
            $technology = ProjectTechnology::findOrFail($id);

            // Verifica se utilizzata
            if ($technology->projects()->count() > 0) {
                session()->flash('error', 'Impossibile eliminare: tecnologia utilizzata in ' .
                    $technology->projects()->count() . ' progetti.');
                return;
            }

            $technology->delete();
            session()->flash('success', 'Tecnologia eliminata con successo.');
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nell\'eliminazione: ' . $e->getMessage());
        }
    }

    /**
     * Elimina tecnologie selezionate
     */
    public function deleteSelected()
    {
        try {
            $technologies = ProjectTechnology::whereIn('id', $this->selectedTechnologies)->get();

            $deleted = 0;
            $skipped = 0;

            foreach ($technologies as $tech) {
                if ($tech->projects()->count() == 0) {
                    $tech->delete();
                    $deleted++;
                } else {
                    $skipped++;
                }
            }

            $message = "Eliminate $deleted tecnologie.";
            if ($skipped > 0) {
                $message .= " $skipped non eliminate perché in uso.";
            }

            session()->flash('success', $message);
            $this->selectedTechnologies = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nell\'eliminazione: ' . $e->getMessage());
        }
    }

    /**
     * Toggle selezione tutte
     */
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedTechnologies = $this->getTechnologies()
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedTechnologies = [];
        }
    }

    /**
     * Ordina per campo
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Toggle categoria nuova/esistente
     */
    public function toggleCategoryMode()
    {
        $this->useNewCategory = !$this->useNewCategory;
        if ($this->useNewCategory) {
            $this->category = '';
        } else {
            $this->newCategory = '';
        }
    }

    /**
     * Ottieni query tecnologie
     */
    private function getTechnologies()
    {
        return ProjectTechnology::withCount('projects')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('category', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category', $this->categoryFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    /**
     * Reset filtri
     */
    public function resetFilters()
    {
        $this->reset(['search', 'categoryFilter']);
        $this->resetPage();
    }

    /**
     * Esporta tecnologie
     */
    public function export()
    {
        $technologies = $this->getTechnologies()->get();

        $csv = "Nome,Categoria,Colore,Progetti\n";
        foreach ($technologies as $tech) {
            $csv .= sprintf(
                "%s,%s,%s,%d\n",
                $tech->name,
                $tech->category ?: 'N/A',
                $tech->color ?: 'N/A',
                $tech->projects_count
            );
        }

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'tecnologie_' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render()
    {
        $technologies = $this->getTechnologies()->paginate($this->perPage);

        $stats = [
            'total' => ProjectTechnology::count(),
            'with_projects' => ProjectTechnology::has('projects')->count(),
            'categories' => ProjectTechnology::distinct()->whereNotNull('category')->count('category'),
        ];

        return view('livewire.admin.technologies.technology-manager', [
            'technologies' => $technologies,
            'stats' => $stats,
        ]);
    }
}
