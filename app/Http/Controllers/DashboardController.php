<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectTechnology;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard principale admin
     */
    public function index()
    {
        $stats = [
            'total_projects' => Project::count(),
            'published_projects' => Project::published()->count(),
            'draft_projects' => Project::where('status', 'draft')->count(),
            'featured_projects' => Project::featured()->count(),
            'total_categories' => ProjectCategory::count(),
            'total_technologies' => ProjectTechnology::count(),
            'total_users' => User::count(),
        ];

        // Progetti recenti
        $recentProjects = Project::with(['categories', 'technologies'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Progetti più visualizzati (se hai implementato tracking)
        $popularProjects = Project::published()
            ->with(['categories'])
            ->orderBy('created_at', 'desc') // Sostituisci con 'views_count' se implementato
            ->limit(5)
            ->get();

        // Categorie più usate
        $topCategories = ProjectCategory::withCount('projects')
            ->orderBy('projects_count', 'desc')
            ->limit(5)
            ->get();

        // Tecnologie più usate
        $topTechnologies = ProjectTechnology::withCount('projects')
            ->orderBy('projects_count', 'desc')
            ->limit(8)
            ->get();

        // Attività recente (ultimi progetti modificati)
        $recentActivity = Project::with(['categories'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentProjects',
            'popularProjects',
            'topCategories',
            'topTechnologies',
            'recentActivity'
        ));
    }

    /**
     * Widget statistiche per AJAX
     */
    public function statsWidget()
    {
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $thisWeek = now()->startOfWeek();
        $lastWeek = now()->subWeek()->startOfWeek();

        return response()->json([
            'projects' => [
                'total' => Project::count(),
                'published' => Project::published()->count(),
                'today' => Project::whereDate('created_at', $today)->count(),
                'this_week' => Project::where('created_at', '>=', $thisWeek)->count(),
            ],
            'growth' => [
                'daily' => [
                    'today' => Project::whereDate('created_at', $today)->count(),
                    'yesterday' => Project::whereDate('created_at', $yesterday)->count(),
                ],
                'weekly' => [
                    'this_week' => Project::where('created_at', '>=', $thisWeek)->count(),
                    'last_week' => Project::whereBetween('created_at', [$lastWeek, $thisWeek])->count(),
                ]
            ]
        ]);
    }

    /**
     * Notifiche per dashboard
     */
    public function notifications()
    {
        $notifications = [];

        // Progetti in bozza da molto tempo
        $oldDrafts = Project::where('status', 'draft')
            ->where('created_at', '<', now()->subDays(7))
            ->count();

        if ($oldDrafts > 0) {
            $notifications[] = [
                'type' => 'warning',
                'message' => "Hai {$oldDrafts} progetti in bozza da più di 7 giorni",
                'action' => route('admin.projects.index', ['status' => 'draft'])
            ];
        }

        // Categorie senza progetti
        $emptyCategories = ProjectCategory::doesntHave('projects')->count();
        if ($emptyCategories > 0) {
            $notifications[] = [
                'type' => 'info',
                'message' => "Ci sono {$emptyCategories} categorie senza progetti",
                'action' => route('admin.categories.index')
            ];
        }

        // Progetti senza immagine featured
        $noImageProjects = Project::published()
            ->whereNull('featured_image')
            ->count();

        if ($noImageProjects > 0) {
            $notifications[] = [
                'type' => 'warning',
                'message' => "{$noImageProjects} progetti pubblicati senza immagine principale",
                'action' => route('admin.projects.index', ['status' => 'published'])
            ];
        }

        return response()->json($notifications);
    }
}
