<?php

namespace App\Livewire\Frontend;

use App\Models\Project;
use Livewire\Attributes\On;
use Livewire\Component;

class Welcome extends Component
{
    // Altri metodi del componente...
    public function getFeaturedProjects()
    {
        return Project::with(['categories', 'technologies'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->orderBy('sort_order', 'asc')
            ->limit(4)
            ->get();
    }

    public function getProjectsForHomepage()
    {
        $featured = $this->getFeaturedProjects();

        if ($featured->count() < 4) {
            $latest = Project::with(['categories', 'technologies'])
                ->where('status', 'published')
                ->whereNotIn('id', $featured->pluck('id'))
                ->orderBy('created_at', 'desc')
                ->limit(4 - $featured->count())
                ->get();

            return $featured->merge($latest);
        }

        return $featured;
    }

    public function getStats()
    {
        return [
            'total_projects' => Project::where('status', 'published')->count(),
            'years_experience' => now()->year - 2018,
            'technologies_used' => \App\Models\ProjectTechnology::count(),
            'clients_served' => Project::whereNotNull('client')
                ->where('status', 'published')
                ->distinct('client')
                ->count('client'),
        ];
    }

    public function render()
    {
        return view('livewire.frontend.welcome', [
            'featuredProjects' => $this->getProjectsForHomepage(),
            'stats' => $this->getStats()
        ])->layout('layouts.guest');
    }
}
