<?php

namespace App\Livewire\Admin;

use App\Models\ProjectTechnology;
use Livewire\Component;
use Illuminate\Support\Str;

class TechnologyForm extends Component
{
    public $technology;
    public $technologyId;

    public $name = '';
    public $category = '';
    public $icon = '';
    public $color = '#6B7280';

    // Categorie predefinite
    public $availableCategories = [
        'Frontend',
        'Backend',
        'Database',
        'Framework',
        'Tool',
        'Cloud',
        'Mobile',
        'Design',
        'Testing',
        'DevOps'
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'category' => 'nullable|string|max:100',
        'icon' => 'nullable|string|max:255',
        'color' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
    ];

    public function mount($technologyId = null)
    {
        $this->technologyId = $technologyId;

        if ($technologyId) {
            $this->loadTechnology();
        }
    }

    public function loadTechnology()
    {
        $this->technology = ProjectTechnology::findOrFail($this->technologyId);

        $this->name = $this->technology->name;
        $this->category = $this->technology->category;
        $this->icon = $this->technology->icon;
        $this->color = $this->technology->color;
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->technologyId) {
                $technology = ProjectTechnology::findOrFail($this->technologyId);
            } else {
                $technology = new ProjectTechnology();
            }

            $technology->fill([
                'name' => $this->name,
                'category' => $this->category ?: null,
                'icon' => $this->icon ?: null,
                'color' => $this->color,
            ]);

            $technology->save();

            session()->flash('message', 'Tecnologia salvata con successo!');

            if (!$this->technologyId) {
                return redirect()->route('admin.technologies.edit', $technology->id);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nel salvare la tecnologia: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.technology-form');
    }
}
