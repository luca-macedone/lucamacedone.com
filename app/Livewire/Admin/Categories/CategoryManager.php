<?php

namespace App\Livewire\Admin\Categories;

use App\Models\ProjectCategory;
use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class CategoryManager extends Component
{
    use WithPagination;

    // Filtri e ricerca
    public $search = '';
    public $perPage = 15;

    // Form fields
    public $showForm = false;
    public $editingId = null;
    public $name = '';
    public $description = '';
    public $color = '#3B82F6';
    public $sort_order = 0;

    // Selezione multipla
    public $selectedCategories = [];
    public $selectAll = false;

    // Ordinamento
    public $sortField = 'sort_order';
    public $sortDirection = 'asc';

    // Drag & Drop
    public $isDragging = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'color' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
        'sort_order' => 'integer|min:0',
    ];

    protected $messages = [
        'name.required' => 'Il nome è obbligatorio.',
        'name.max' => 'Il nome non può superare i 255 caratteri.',
        'description.max' => 'La descrizione non può superare i 500 caratteri.',
        'color.regex' => 'Il colore deve essere in formato esadecimale valido.',
    ];

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
        $this->reset(['name', 'description', 'color', 'sort_order', 'editingId']);
        $this->color = '#3B82F6';
        $this->showForm = false;
        $this->sort_order = ProjectCategory::max('sort_order') + 1 ?? 0;
    }

    /**
     * Crea nuova categoria
     */
    public function create()
    {
        $this->validate();

        try {
            ProjectCategory::create([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'description' => $this->description,
                'color' => $this->color,
                'sort_order' => $this->sort_order,
            ]);

            session()->flash('success', 'Categoria creata con successo.');
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nella creazione: ' . $e->getMessage());
        }
    }

    /**
     * Modifica categoria esistente
     */
    public function edit($id)
    {
        $category = ProjectCategory::findOrFail($id);

        $this->editingId = $id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->color = $category->color ?: '#3B82F6';
        $this->sort_order = $category->sort_order;
        $this->showForm = true;
    }

    /**
     * Aggiorna categoria
     */
    public function update()
    {
        $this->validate();

        try {
            $category = ProjectCategory::findOrFail($this->editingId);

            $category->update([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'description' => $this->description,
                'color' => $this->color,
                'sort_order' => $this->sort_order,
            ]);

            session()->flash('success', 'Categoria aggiornata con successo.');
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nell\'aggiornamento: ' . $e->getMessage());
        }
    }

    /**
     * Elimina categoria
     */
    public function delete($id)
    {
        try {
            $category = ProjectCategory::findOrFail($id);

            // Verifica se utilizzata
            if ($category->projects()->count() > 0) {
                session()->flash('error', 'Impossibile eliminare: categoria utilizzata in ' .
                    $category->projects()->count() . ' progetti.');
                return;
            }

            $category->delete();
            session()->flash('success', 'Categoria eliminata con successo.');
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nell\'eliminazione: ' . $e->getMessage());
        }
    }

    /**
     * Elimina categorie selezionate
     */
    public function deleteSelected()
    {
        try {
            $categories = ProjectCategory::whereIn('id', $this->selectedCategories)->get();

            $deleted = 0;
            $skipped = 0;

            foreach ($categories as $cat) {
                if ($cat->projects()->count() == 0) {
                    $cat->delete();
                    $deleted++;
                } else {
                    $skipped++;
                }
            }

            $message = "Eliminate $deleted categorie.";
            if ($skipped > 0) {
                $message .= " $skipped non eliminate perché in uso.";
            }

            session()->flash('success', $message);
            $this->selectedCategories = [];
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
            $this->selectedCategories = $this->getCategories()
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedCategories = [];
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
     * Riordina categorie (drag & drop)
     */
    public function reorderCategories($orderedIds)
    {
        try {
            foreach ($orderedIds as $index => $categoryId) {
                ProjectCategory::where('id', $categoryId)->update(['sort_order' => $index]);
            }

            session()->flash('success', 'Ordine categorie aggiornato.');
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nel riordinamento: ' . $e->getMessage());
        }
    }

    /**
     * Ottieni query categorie
     */
    private function getCategories()
    {
        return ProjectCategory::withCount('projects')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    /**
     * Reset filtri
     */
    public function resetFilters()
    {
        $this->reset(['search']);
        $this->resetPage();
    }

    /**
     * Genera colore casuale
     */
    public function generateRandomColor()
    {
        $colors = [
            '#EF4444', // red
            '#F59E0B', // amber
            '#10B981', // emerald
            '#3B82F6', // blue
            '#8B5CF6', // violet
            '#EC4899', // pink
            '#6366F1', // indigo
            '#14B8A6', // teal
        ];

        $this->color = $colors[array_rand($colors)];
    }

    /**
     * Clona categoria
     */
    public function clone($id)
    {
        try {
            $category = ProjectCategory::findOrFail($id);

            $newCategory = $category->replicate();
            $newCategory->name = $category->name . ' (Copia)';
            $newCategory->slug = Str::slug($newCategory->name);
            $newCategory->sort_order = ProjectCategory::max('sort_order') + 1;
            $newCategory->save();

            session()->flash('success', 'Categoria clonata con successo.');
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nella clonazione: ' . $e->getMessage());
        }
    }

    /**
     * Esporta categorie
     */
    public function export()
    {
        $categories = $this->getCategories()->get();

        $csv = "Nome,Descrizione,Colore,Ordine,Progetti\n";
        foreach ($categories as $cat) {
            $csv .= sprintf(
                '"%s","%s","%s",%d,%d' . "\n",
                str_replace('"', '""', $cat->name),
                str_replace('"', '""', $cat->description ?: ''),
                $cat->color ?: '',
                $cat->sort_order,
                $cat->projects_count
            );
        }

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'categorie_' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Unisci categorie
     */
    public function mergeCategories($fromId, $toId)
    {
        try {
            $fromCategory = ProjectCategory::findOrFail($fromId);
            $toCategory = ProjectCategory::findOrFail($toId);

            // Sposta tutti i progetti dalla categoria origine a quella destinazione
            $fromCategory->projects()->each(function ($project) use ($toCategory) {
                $project->categories()->detach($fromCategory->id);
                if (!$project->categories->contains($toCategory->id)) {
                    $project->categories()->attach($toCategory->id);
                }
            });

            // Elimina categoria origine
            $fromCategory->delete();

            session()->flash('success', "Categoria '{$fromCategory->name}' unita a '{$toCategory->name}'.");
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nell\'unione: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $categories = $this->getCategories()->paginate($this->perPage);

        $stats = [
            'total' => ProjectCategory::count(),
            'with_projects' => ProjectCategory::has('projects')->count(),
            'empty' => ProjectCategory::doesntHave('projects')->count(),
        ];

        return view('livewire.admin.categories.category-manager', [
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }
}
