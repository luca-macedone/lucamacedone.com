<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use BulkProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use ProjectRequest;
use ReorderProjectRequest;

class ProjectController extends Controller
{
    protected ProjectService $projectService;

    /**
     * Create a new controller instance.
     *
     * @param ProjectService $projectService
     */
    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;

        // Apply middleware if needed
        $this->middleware('auth');
        $this->middleware('can:manage-projects');
    }

    /**
     * Display a listing of the projects.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'is_published', 'is_featured']);
        $projects = $this->projectService->getAdminProjects($filters);

        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.projects.create');
    }

    /**
     * Store a newly created project.
     *
     * @param ProjectRequest $request
     * @return RedirectResponse
     */
    public function store(ProjectRequest $request): RedirectResponse
    {
        try {
            $project = $this->projectService->create($request->validated());

            return redirect()
                ->route('admin.projects.edit', $project)
                ->with('success', 'Project created successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create project: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified project.
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        $project = $this->projectService->getAdminProjects()->find($id);

        if (!$project) {
            abort(404);
        }

        return view('admin.projects.edit', compact('project'));
    }

    /**
     * Update the specified project.
     *
     * @param ProjectRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(ProjectRequest $request, int $id): RedirectResponse
    {
        try {
            $this->projectService->update($id, $request->validated());

            return redirect()
                ->route('admin.projects.edit', $id)
                ->with('success', 'Project updated successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update project: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified project.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $result = $this->projectService->bulkDelete([$id]);

            return redirect()
                ->route('admin.projects.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete project: ' . $e->getMessage());
        }
    }

    /**
     * Toggle project status (AJAX).
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        try {
            $result = $this->projectService->toggleStatus($id);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle featured status (AJAX).
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function toggleFeatured(Request $request, int $id): JsonResponse
    {
        try {
            $result = $this->projectService->toggleFeatured($id);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle featured: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete projects.
     *
     * @param BulkProjectRequest $request
     * @return JsonResponse
     */
    public function bulkDelete(BulkProjectRequest $request): JsonResponse
    {
        try {
            $result = $this->projectService->bulkDelete($request->input('ids', []));
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete projects: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk publish projects.
     *
     * @param BulkProjectRequest $request
     * @return JsonResponse
     */
    public function bulkPublish(BulkProjectRequest $request): JsonResponse
    {
        try {
            $publish = $request->input('publish', true);
            $result = $this->projectService->bulkPublish($request->input('ids', []), $publish);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update projects: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk feature projects.
     *
     * @param BulkProjectRequest $request
     * @return JsonResponse
     */
    public function bulkFeature(BulkProjectRequest $request): JsonResponse
    {
        try {
            $feature = $request->input('feature', true);
            $result = $this->projectService->bulkFeature($request->input('ids', []), $feature);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update projects: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder projects.
     *
     * @param ReorderProjectRequest $request
     * @return JsonResponse
     */
    public function reorder(ReorderProjectRequest $request): JsonResponse
    {
        try {
            $result = $this->projectService->reorder($request->input('ids', []));
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder projects: ' . $e->getMessage()
            ], 500);
        }
    }
}
