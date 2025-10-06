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
    public $technologyTypeFilter = ''; // Rinominato per chiarezza
    public $perPage = 15;

    // Form fields
    public $showForm = false;
    public $editingId = null;
    public $name = '';
    public $category = ''; // Questo è il TIPO di tecnologia (Frontend, Backend, etc.)
    public $icon = '';
    public $color = '#6B7280';
    public $newCategory = '';
    public $useNewCategory = false;

    // Selezione multipla
    public $selectedTechnologies = [];
    public $selectAll = false;

    // TIPI di tecnologie disponibili (NON categorie progetti!)
    public $availableTechnologyTypes = [];

    // Ordinamento
    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100', // Categoria/tipo tecnologia è opzionale
            'newCategory' => 'required_if:useNewCategory,true|nullable|string|max:100',
            'icon' => 'nullable|string|max:255',
            'color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ];
    }

    protected $messages = [
        'name.required' => 'Il nome della tecnologia è obbligatorio.',
        'name.max' => 'Il nome non può superare i 255 caratteri.',
        'newCategory.required_if' => 'Inserisci il nome del nuovo tipo di tecnologia.',
        'color.required' => 'Il colore è obbligatorio.',
        'color.regex' => 'Il colore deve essere in formato esadecimale valido (es. #FF5733).',
    ];

    public function mount()
    {
        $this->loadTechnologyTypes();
    }

    /**
     * Carica i TIPI di tecnologie disponibili (NON le categorie dei progetti!)
     * Esempi: Frontend, Backend, Database, Framework, etc.
     */
    public function loadTechnologyTypes()
    {
        // Tipi di tecnologie predefiniti (questi NON sono ProjectCategory!)
        $predefinedTypes = [
            'Frontend',
            'Backend',
            'Database',
            'Framework',
            'CMS',
            'Tool',
            'Cloud/Hosting',
            'Mobile',
            'Design',
            'Testing',
            'DevOps',
            'AI/ML',
            'Version Control',
            'API',
            'Other'
        ];

        // Tipi esistenti nel database (campo 'category' della tabella project_technologies)
        $existingTypes = ProjectTechnology::distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->toArray();

        // Unisci e ordina
        $this->availableTechnologyTypes = collect($predefinedTypes)
            ->merge($existingTypes)
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
            // Se sta creando un nuovo tipo di tecnologia
            $technologyType = $this->useNewCategory ? $this->newCategory : $this->category;

            ProjectTechnology::create([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'category' => $technologyType ?: null, // Tipo di tecnologia (opzionale)
                'icon' => $this->icon ?: null,
                'color' => $this->color,
            ]);

            session()->flash('success', 'Tecnologia "' . $this->name . '" creata con successo.');

            $this->resetForm();
            $this->loadTechnologyTypes(); // Ricarica i tipi se ne è stato aggiunto uno nuovo

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
        $this->category = $technology->category; // Tipo di tecnologia
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

            $technologyType = $this->useNewCategory ? $this->newCategory : $this->category;

            $technology->update([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'category' => $technologyType ?: null,
                'icon' => $this->icon ?: null,
                'color' => $this->color,
            ]);

            session()->flash('success', 'Tecnologia "' . $this->name . '" aggiornata con successo.');

            $this->resetForm();
            $this->loadTechnologyTypes();
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

            // Verifica se utilizzata in progetti
            if ($technology->projects()->count() > 0) {
                session()->flash('error', 'Impossibile eliminare "' . $technology->name . '": utilizzata in ' .
                    $technology->projects()->count() . ' progetti.');
                return;
            }

            $name = $technology->name;
            $technology->delete();
            session()->flash('success', 'Tecnologia "' . $name . '" eliminata con successo.');
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
            $skippedNames = [];

            foreach ($technologies as $tech) {
                if ($tech->projects()->count() == 0) {
                    $tech->delete();
                    $deleted++;
                } else {
                    $skipped++;
                    $skippedNames[] = $tech->name;
                }
            }

            $message = "Eliminate $deleted tecnologie.";
            if ($skipped > 0) {
                $message .= " $skipped non eliminate perché in uso: " . implode(', ', array_slice($skippedNames, 0, 3));
                if (count($skippedNames) > 3) {
                    $message .= ' e altre';
                }
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
     * Toggle modalità nuovo tipo di tecnologia
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
     * Ottieni query tecnologie con filtri
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
            ->when($this->technologyTypeFilter, function ($query) {
                $query->where('category', $this->technologyTypeFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    /**
     * Reset filtri
     */
    public function resetFilters()
    {
        $this->reset(['search', 'technologyTypeFilter']);
        $this->resetPage();
    }

    /**
     * Esporta tecnologie in CSV
     */
    public function export()
    {
        $technologies = $this->getTechnologies()->get();

        $csv = "Nome,Tipo,Colore,Progetti Associati\n";
        foreach ($technologies as $tech) {
            $csv .= sprintf(
                "%s,%s,%s,%d\n",
                $tech->name,
                $tech->category ?: 'Non categorizzata',
                $tech->color ?: 'Default',
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
            'types' => ProjectTechnology::distinct()->whereNotNull('category')->count('category'),
        ];

        return view('livewire.admin.technologies.technology-manager', [
            'technologies' => $technologies,
            'stats' => $stats,
        ])->layout('layouts.app');
    }
}
