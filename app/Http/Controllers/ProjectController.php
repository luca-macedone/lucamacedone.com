<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Toggle project status (AJAX)
     */
    public function toggleStatus(Project $project)
    {
        try {
            $newStatus = $project->status === 'published' ? 'draft' : 'published';
            $project->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => "Status aggiornato a: {$newStatus}",
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nell\'aggiornamento dello status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle project featured status (AJAX)
     */
    public function toggleFeatured(Project $project)
    {
        try {
            $project->update(['is_featured' => !$project->is_featured]);

            $message = $project->is_featured ? 'Progetto messo in evidenza' : 'Progetto rimosso dall\'evidenza';

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_featured' => $project->is_featured
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nell\'aggiornamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete project (AJAX)
     */
    public function destroy(Project $project)
    {
        try {
            // Elimina file associati
            if ($project->featured_image) {
                Storage::disk('public')->delete($project->featured_image);
            }

            if ($project->gallery_images) {
                foreach ($project->gallery_images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $project->delete();

            return response()->json([
                'success' => true,
                'message' => 'Progetto eliminato con successo'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nell\'eliminazione: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete projects
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'project_ids' => 'required|array',
            'project_ids.*' => 'exists:projects,id'
        ]);

        try {
            $projects = Project::whereIn('id', $request->project_ids)->get();
            $count = 0;

            foreach ($projects as $project) {
                // Elimina file associati
                if ($project->featured_image) {
                    Storage::disk('public')->delete($project->featured_image);
                }

                if ($project->gallery_images) {
                    foreach ($project->gallery_images as $image) {
                        Storage::disk('public')->delete($image);
                    }
                }

                $project->delete();
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "Eliminati {$count} progetti con successo"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nell\'eliminazione multipla: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk publish/unpublish projects
     */
    public function bulkPublish(Request $request)
    {
        $request->validate([
            'project_ids' => 'required|array',
            'project_ids.*' => 'exists:projects,id',
            'status' => 'required|in:draft,published'
        ]);

        try {
            $updated = Project::whereIn('id', $request->project_ids)
                ->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => "Aggiornati {$updated} progetti a status: {$request->status}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nell\'aggiornamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk feature/unfeature projects
     */
    public function bulkFeature(Request $request)
    {
        $request->validate([
            'project_ids' => 'required|array',
            'project_ids.*' => 'exists:projects,id',
            'featured' => 'required|boolean'
        ]);

        try {
            $updated = Project::whereIn('id', $request->project_ids)
                ->update(['is_featured' => $request->featured]);

            $action = $request->featured ? 'messi in evidenza' : 'rimossi dall\'evidenza';

            return response()->json([
                'success' => true,
                'message' => "Aggiornati {$updated} progetti: {$action}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nell\'aggiornamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder projects
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'projects' => 'required|array',
            'projects.*.id' => 'required|exists:projects,id',
            'projects.*.sort_order' => 'required|integer|min:0'
        ]);

        try {
            foreach ($request->projects as $projectData) {
                Project::where('id', $projectData['id'])
                    ->update(['sort_order' => $projectData['sort_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ordine progetti aggiornato con successo'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nel riordinamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get projects for mobile/external use
     */
    public function apiIndex(Request $request)
    {
        $query = Project::with(['categories', 'technologies'])
            ->where('status', 'published');

        if ($request->has('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $projects = $query->orderBy('sort_order', 'asc')
            ->paginate($request->get('per_page', 12));

        return response()->json($projects);
    }

    /**
     * API: Get single project
     */
    public function apiShow(Project $project)
    {
        if ($project->status !== 'published') {
            return response()->json(['message' => 'Project not found'], 404);
        }

        return response()->json($project->load(['categories', 'technologies']));
    }
}
