<?php

namespace App\Livewire\Admin;

use App\Models\ProjectCategory;
use Livewire\Component;
use Illuminate\Support\Str;

class CategoryForm extends Component
{
    public $category;
    public $categoryId;

    public $name = '';
    public $description = '';
    public $color = '#3B82F6';
    public $sort_order = 0;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'color' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
        'sort_order' => 'integer|min:0',
    ];

    public function mount($categoryId = null)
    {
        $this->categoryId = $categoryId;

        if ($categoryId) {
            $this->loadCategory();
        }
    }

    public function loadCategory()
    {
        $this->category = ProjectCategory::findOrFail($this->categoryId);

        $this->name = $this->category->name;
        $this->description = $this->category->description;
        $this->color = $this->category->color;
        $this->sort_order = $this->category->sort_order;
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->categoryId) {
                $category = ProjectCategory::findOrFail($this->categoryId);
            } else {
                $category = new ProjectCategory();
            }

            $category->fill([
                'name' => $this->name,
                'description' => $this->description,
                'color' => $this->color,
                'sort_order' => $this->sort_order,
            ]);

            $category->save();

            session()->flash('message', 'Categoria salvata con successo!');

            if (!$this->categoryId) {
                return redirect()->route('admin.categories.edit', $category->id);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Errore nel salvare la categoria: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.category-form');
    }
}
