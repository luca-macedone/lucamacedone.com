<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Lista progetti con filtri e paginazione
     */
    public function index(Request $request)
    {
        $query = Project::with(['categories', 'technologies'])
            ->orderBy('created_at', 'desc');

        // Filtri
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('project_categories.id', $request->category);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('client', 'like', "%{$search}%");
            });
        }

        $projects = $query->paginate(15)->withQueryString();

        // Dati per filtri
        $categories = ProjectCategory::ordered()->get();
        $stats = [
            'total' => Project::count(),
            'published' => Project::where('status', 'published')->count(),
            'draft' => Project::where('status', 'draft')->count(),
            'featured' => Project::where('is_featured', true)->count(),
        ];

        return view('livewire.admin.projects.index', compact('projects', 'categories', 'stats'));
    }

    /**
     * Form creazione nuovo progetto
     */
    public function create()
    {
        return view('livewire.admin.projects.create');
    }

    /**
     * Form modifica progetto esistente
     */
    public function edit(Project $project)
    {
        return view('livewire.admin.projects.edit', compact('project'));
    }

    /**
     * Elimina progetto
     */
    public function destroy(Project $project)
    {
        try {
            // Elimina immagini associate
            if ($project->featured_image) {
                Storage::disk('public')->delete($project->featured_image);
            }

            if ($project->gallery) {
                foreach ($project->gallery as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            // Elimina relazioni
            $project->categories()->detach();
            $project->technologies()->detach();
            $project->images()->delete();
            $project->seo()->delete();

            // Elimina progetto
            $project->delete();

            return redirect()->route('admin.projects.index')
                ->with('success', 'Progetto eliminato con successo');
        } catch (\Exception $e) {
            return redirect()->route('admin.projects.index')
                ->with('error', 'Errore nell\'eliminare il progetto: ' . $e->getMessage());
        }
    }

    /**
     * Eliminazione multipla progetti
     */
    public function bulkDelete(Request $request)
    {
        $projectIds = $request->input('project_ids', []);

        if (empty($projectIds)) {
            return response()->json(['error' => 'Nessun progetto selezionato'], 400);
        }

        try {
            $projects = Project::whereIn('id', $projectIds)->get();

            foreach ($projects as $project) {
                // Pulisci immagini
                if ($project->featured_image) {
                    Storage::disk('public')->delete($project->featured_image);
                }

                if ($project->gallery) {
                    foreach ($project->gallery as $image) {
                        Storage::disk('public')->delete($image);
                    }
                }

                // Elimina relazioni
                $project->categories()->detach();
                $project->technologies()->detach();
                $project->images()->delete();
                $project->seo()->delete();
            }

            Project::whereIn('id', $projectIds)->delete();

            return response()->json([
                'success' => true,
                'message' => count($projectIds) . ' progetti eliminati con successo'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Errore nell\'eliminazione: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Pubblicazione multipla progetti
     */
    public function bulkPublish(Request $request)
    {
        $projectIds = $request->input('project_ids', []);
        $status = $request->input('status', 'published');

        if (empty($projectIds)) {
            return response()->json(['error' => 'Nessun progetto selezionato'], 400);
        }

        try {
            Project::whereIn('id', $projectIds)->update(['status' => $status]);

            return response()->json([
                'success' => true,
                'message' => count($projectIds) . " progetti aggiornati a '{$status}'"
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Errore nell\'aggiornamento: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Evidenzia progetti
     */
    public function bulkFeature(Request $request)
    {
        $projectIds = $request->input('project_ids', []);
        $featured = $request->input('featured', true);

        if (empty($projectIds)) {
            return response()->json(['error' => 'Nessun progetto selezionato'], 400);
        }

        try {
            Project::whereIn('id', $projectIds)->update(['is_featured' => $featured]);

            $message = $featured
                ? count($projectIds) . ' progetti messi in evidenza'
                : count($projectIds) . ' progetti rimossi dall\'evidenza';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Errore nell\'aggiornamento: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Toggle status progetto (AJAX)
     */
    public function toggleStatus(Project $project)
    {
        try {
            $newStatus = $project->status === 'published' ? 'draft' : 'published';
            $project->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => "Progetto {$newStatus}"
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Toggle featured progetto (AJAX)
     */
    public function toggleFeatured(Project $project)
    {
        try {
            $project->update(['is_featured' => !$project->is_featured]);

            return response()->json([
                'success' => true,
                'is_featured' => $project->is_featured,
                'message' => $project->is_featured ? 'Progetto messo in evidenza' : 'Progetto rimosso dall\'evidenza'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Riordina progetti (drag & drop)
     */
    public function reorder(Request $request)
    {
        $projectIds = $request->input('project_ids', []);

        try {
            foreach ($projectIds as $index => $projectId) {
                Project::where('id', $projectId)->update(['sort_order' => $index]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ordine progetti aggiornato'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Search progetti per autocomplete (AJAX)
     */
    public function searchProjects(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $projects = Project::where('title', 'like', "%{$query}%")
            ->orWhere('client', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'title', 'client', 'status'])
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'text' => $project->title . ($project->client ? " ({$project->client})" : ''),
                    'status' => $project->status
                ];
            });

        return response()->json($projects);
    }

    /**
     * Progetti recenti per dashboard
     */
    public function recentProjects()
    {
        $projects = Project::with(['categories'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'title', 'status', 'is_featured', 'created_at', 'featured_image']);

        return response()->json($projects);
    }
}
