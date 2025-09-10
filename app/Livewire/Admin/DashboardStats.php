<?php

namespace App\Livewire\Admin;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectTechnology;
use Carbon\Carbon;
use Livewire\Component;

class DashboardStats extends Component
{
    public $stats = [];
    public $chartData = [];
    public $recentProjects = [];

    public function mount()
    {
        $this->loadStats();
        $this->loadChartData();
        $this->loadRecentProjects();
    }

    public function loadStats()
    {
        $this->stats = [
            'total_projects' => Project::count(),
            'published_projects' => Project::published()->count(),
            'draft_projects' => Project::where('status', 'draft')->count(),
            'featured_projects' => Project::featured()->count(),
            'total_categories' => ProjectCategory::count(),
            'total_technologies' => ProjectTechnology::count(),
        ];

        // Crescita mensile
        $thisMonth = Project::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $lastMonth = Project::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        $this->stats['monthly_growth'] = $lastMonth > 0 ?
            round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1) : 0;
    }

    public function loadChartData()
    {
        $months = [];
        $projectCounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M');

            $count = Project::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $projectCounts[] = $count;
        }

        $this->chartData = [
            'labels' => $months,
            'data' => $projectCounts
        ];
    }

    public function loadRecentProjects()
    {
        $this->recentProjects = Project::with(['categories'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function refreshStats()
    {
        $this->loadStats();
        $this->loadChartData();
        $this->loadRecentProjects();

        $this->dispatch('stats-refreshed');
    }

    public function render()
    {
        return view('livewire.admin.dashboard-stats');
    }
}
