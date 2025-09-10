<?php

namespace App\Http\Controllers;

use App\Models\ProjectCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Lista categorie
     */
    public function index()
    {
        $categories = ProjectCategory::withCount('projects')
            ->ordered()
            ->get();

        $stats = [
            'total' => ProjectCategory::count(),
            'with_projects' => ProjectCategory::has('projects')->count(),
            'empty' => ProjectCategory::doesntHave('projects')->count(),
        ];

        return view('admin.categories.index', compact('categories', 'stats'));
    }

    /**
     * Form creazione categoria
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Form modifica categoria
     */
    public function edit(ProjectCategory $category)
    {
        $category->load('projects:id,title,status');
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Elimina categoria
     */
    public function destroy(ProjectCategory $category)
    {
        try {
            // Verifica se la categoria ha progetti associati
            if ($category->projects()->count() > 0) {
                return redirect()->route('admin.categories.index')
                    ->with('error', 'Impossibile eliminare la categoria: contiene ' . $category->projects()->count() . ' progetti');
            }

            $category->delete();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Categoria eliminata con successo');
        } catch (\Exception $e) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Errore nell\'eliminare la categoria: ' . $e->getMessage());
        }
    }

    /**
     * Riordina categorie (drag & drop)
     */
    public function reorder(Request $request)
    {
        $categoryIds = $request->input('category_ids', []);

        try {
            foreach ($categoryIds as $index => $categoryId) {
                ProjectCategory::where('id', $categoryId)->update(['sort_order' => $index]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ordine categorie aggiornato'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Search categorie per autocomplete (AJAX)
     */
    public function searchCategories(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $categories = ProjectCategory::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->withCount('projects')
            ->limit(10)
            ->get(['id', 'name', 'color'])
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'text' => $category->name,
                    'color' => $category->color,
                    'projects_count' => $category->projects_count
                ];
            });

        return response()->json($categories);
    }
}
