<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectTechnology;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Csv\Writer;

class AnalyticsController extends Controller
{
    /**
     * Dashboard analytics
     */
    public function index()
    {
        $stats = $this->getOverviewStats();
        $chartData = $this->getProjectsChartData();
        $recentProjects = Project::with(['categories'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.analytics.index', compact('stats', 'chartData', 'recentProjects'));
    }

    /**
     * Analytics progetti dettagliati
     */
    public function projects()
    {
        $projectsByCategory = ProjectCategory::withCount(['projects' => function ($query) {
            $query->published();
        }])->having('projects_count', '>', 0)->get();

        $projectsByTechnology = ProjectTechnology::withCount(['projects' => function ($query) {
            $query->published();
        }])->having('projects_count', '>', 0)
            ->orderBy('projects_count', 'desc')
            ->limit(10)
            ->get();

        $projectsByStatus = [
            'draft' => Project::where('status', 'draft')->count(),
            'published' => Project::where('status', 'published')->count(),
            'featured' => Project::where('status', 'featured')->count(),
        ];

        $monthlyProjects = Project::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        return view('admin.analytics.projects', compact(
            'projectsByCategory',
            'projectsByTechnology',
            'projectsByStatus',
            'monthlyProjects'
        ));
    }

    /**
     * Export dati analytics
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        try {
            $data = [
                'projects' => Project::with(['categories', 'technologies'])->get(),
                'categories' => ProjectCategory::withCount('projects')->get(),
                'technologies' => ProjectTechnology::withCount('projects')->get(),
            ];

            if ($format === 'json') {
                return response()->json($data);
            }

            // Export CSV
            $filename = 'analytics_export_' . date('Y-m-d_H-i-s') . '.csv';

            $csv = Writer::createFromString('');
            $csv->insertOne(['Report Analytics - ' . date('Y-m-d H:i:s')]);
            $csv->insertOne([]);

            // Progetti
            $csv->insertOne(['PROGETTI']);
            $csv->insertOne(['ID', 'Titolo', 'Status', 'In Evidenza', 'Cliente', 'Categorie', 'Tecnologie', 'Creato']);

            foreach ($data['projects'] as $project) {
                $csv->insertOne([
                    $project->id,
                    $project->title,
                    $project->status,
                    $project->is_featured ? 'Sì' : 'No',
                    $project->client ?? '',
                    $project->categories->pluck('name')->implode(', '),
                    $project->technologies->pluck('name')->implode(', '),
                    $project->created_at->format('Y-m-d H:i:s')
                ]);
            }

            $csv->insertOne([]);
            $csv->insertOne(['CATEGORIE']);
            $csv->insertOne(['ID', 'Nome', 'Progetti', 'Ordine']);

            foreach ($data['categories'] as $category) {
                $csv->insertOne([
                    $category->id,
                    $category->name,
                    $category->projects_count,
                    $category->sort_order
                ]);
            }

            return response($csv->toString(), 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.analytics.index')
                ->with('error', 'Errore nell\'export: ' . $e->getMessage());
        }
    }

    /**
     * Statistiche overview per API
     */
    public function overview()
    {
        return response()->json($this->getOverviewStats());
    }

    /**
     * Calcola statistiche generali
     */
    private function getOverviewStats()
    {
        $totalProjects = Project::count();
        $publishedProjects = Project::published()->count();
        $featuredProjects = Project::featured()->count();
        $draftProjects = Project::where('status', 'draft')->count();

        // Progetti questo mese
        $thisMonth = Project::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Progetti mese scorso per calcolare crescita
        $lastMonth = Project::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        $growth = $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;

        // Categoria più usata
        $topCategory = ProjectCategory::withCount('projects')
            ->orderBy('projects_count', 'desc')
            ->first();

        // Tecnologia più usata  
        $topTechnology = ProjectTechnology::withCount('projects')
            ->orderBy('projects_count', 'desc')
            ->first();

        return [
            'total_projects' => $totalProjects,
            'published_projects' => $publishedProjects,
            'featured_projects' => $featuredProjects,
            'draft_projects' => $draftProjects,
            'projects_this_month' => $thisMonth,
            'projects_last_month' => $lastMonth,
            'growth_percentage' => round($growth, 1),
            'top_category' => $topCategory?->name,
            'top_technology' => $topTechnology?->name,
            'total_categories' => ProjectCategory::count(),
            'total_technologies' => ProjectTechnology::count(),
        ];
    }

    /**
     * Dati per grafici progetti
     */
    private function getProjectsChartData()
    {
        // Ultimi 12 mesi
        $months = [];
        $projectCounts = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');

            $count = Project::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $projectCounts[] = $count;
        }

        return [
            'months' => $months,
            'project_counts' => $projectCounts
        ];
    }
}
