<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectService
{
    /**
     * Toggle project status (published/draft)
     *
     * @param int $projectId
     * @return array
     * @throws ModelNotFoundException
     */
    public function toggleStatus(int $projectId): array
    {
        $project = Project::findOrFail($projectId);

        DB::beginTransaction();
        try {
            $project->is_published = !$project->is_published;
            $project->save();

            DB::commit();

            return [
                'success' => true,
                'is_published' => $project->is_published,
                'message' => $project->is_published
                    ? 'Project published successfully'
                    : 'Project set to draft successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle featured status
     *
     * @param int $projectId
     * @return array
     * @throws ModelNotFoundException
     */
    public function toggleFeatured(int $projectId): array
    {
        $project = Project::findOrFail($projectId);

        DB::beginTransaction();
        try {
            // If setting as featured, unfeatured all others first
            if (!$project->is_featured) {
                Project::where('is_featured', true)->update(['is_featured' => false]);
            }

            $project->is_featured = !$project->is_featured;
            $project->save();

            DB::commit();

            return [
                'success' => true,
                'is_featured' => $project->is_featured,
                'message' => $project->is_featured
                    ? 'Project featured successfully'
                    : 'Project unfeatured successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Bulk delete projects
     *
     * @param array $projectIds
     * @return array
     */
    public function bulkDelete(array $projectIds): array
    {
        $projectIds = array_filter($projectIds, 'is_numeric');

        if (empty($projectIds)) {
            return [
                'success' => false,
                'deleted_count' => 0,
                'message' => 'No valid project IDs provided'
            ];
        }

        DB::beginTransaction();
        try {
            // Soft delete if model uses SoftDeletes trait
            $deletedCount = Project::whereIn('id', $projectIds)->delete();

            DB::commit();

            return [
                'success' => true,
                'deleted_count' => $deletedCount,
                'message' => "$deletedCount project(s) deleted successfully"
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Bulk publish/unpublish projects
     *
     * @param array $projectIds
     * @param bool $publish
     * @return array
     */
    public function bulkPublish(array $projectIds, bool $publish = true): array
    {
        $projectIds = array_filter($projectIds, 'is_numeric');

        if (empty($projectIds)) {
            return [
                'success' => false,
                'updated_count' => 0,
                'message' => 'No valid project IDs provided'
            ];
        }

        DB::beginTransaction();
        try {
            $updatedCount = Project::whereIn('id', $projectIds)
                ->update(['is_published' => $publish]);

            DB::commit();

            $action = $publish ? 'published' : 'unpublished';
            return [
                'success' => true,
                'updated_count' => $updatedCount,
                'message' => "$updatedCount project(s) $action successfully"
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Bulk feature/unfeature projects
     *
     * @param array $projectIds
     * @param bool $feature
     * @return array
     */
    public function bulkFeature(array $projectIds, bool $feature = true): array
    {
        $projectIds = array_filter($projectIds, 'is_numeric');

        if (empty($projectIds)) {
            return [
                'success' => false,
                'updated_count' => 0,
                'message' => 'No valid project IDs provided'
            ];
        }

        DB::beginTransaction();
        try {
            // If featuring, unfeatured all others first
            if ($feature) {
                Project::where('is_featured', true)->update(['is_featured' => false]);
            }

            $updatedCount = Project::whereIn('id', $projectIds)
                ->update(['is_featured' => $feature]);

            DB::commit();

            $action = $feature ? 'featured' : 'unfeatured';
            return [
                'success' => true,
                'updated_count' => $updatedCount,
                'message' => "$updatedCount project(s) $action successfully"
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reorder projects
     *
     * @param array $orderedIds Array of project IDs in desired order
     * @return array
     */
    public function reorder(array $orderedIds): array
    {
        $orderedIds = array_filter($orderedIds, 'is_numeric');

        if (empty($orderedIds)) {
            return [
                'success' => false,
                'reordered_count' => 0,
                'message' => 'No valid project IDs provided'
            ];
        }

        DB::beginTransaction();
        try {
            $reorderedCount = 0;

            foreach ($orderedIds as $position => $projectId) {
                $updated = Project::where('id', $projectId)
                    ->update(['sort_order' => $position + 1]);

                if ($updated) {
                    $reorderedCount++;
                }
            }

            DB::commit();

            return [
                'success' => true,
                'reordered_count' => $reorderedCount,
                'message' => "$reorderedCount project(s) reordered successfully"
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get projects for admin listing
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAdminProjects(array $filters = [], int $perPage = 15)
    {
        $query = Project::query();

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['is_published'])) {
            $query->where('is_published', $filters['is_published']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        // Default ordering
        $query->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Create a new project
     *
     * @param array $data
     * @return Project
     */
    public function create(array $data): Project
    {
        DB::beginTransaction();
        try {
            // Get max sort_order for new project
            $maxSortOrder = Project::max('sort_order') ?? 0;
            $data['sort_order'] = $maxSortOrder + 1;

            $project = Project::create($data);

            DB::commit();

            return $project;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update a project
     *
     * @param int $projectId
     * @param array $data
     * @return Project
     * @throws ModelNotFoundException
     */
    public function update(int $projectId, array $data): Project
    {
        $project = Project::findOrFail($projectId);

        DB::beginTransaction();
        try {
            $project->update($data);

            DB::commit();

            return $project->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
