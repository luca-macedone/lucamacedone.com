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
        return Project::forCard()
            ->published()
            ->featured()
            ->ordered()
            ->limit(4)
            ->get();
    }

    public function getProjectsForHomepage()
    {
        $featured = $this->getFeaturedProjects();

        if ($featured->count() < 4) {
            // Completa con progetti recenti
            $latest = Project::forCard()
                ->published()
                ->whereNotIn('id', $featured->pluck('id'))
                ->latest()
                ->limit(4 - $featured->count())
                ->get();

            return $featured->merge($latest);
        }

        return $featured;
    }

    public function getStats()
    {
        return [
            'total_projects' => Project::forStats()->published()->count(),
            'years_experience' => now()->year - 2018,
            'technologies_used' => \App\Models\ProjectTechnology::count(),
            'clients_served' => Project::forStats()
                ->whereNotNull('client')
                ->published()
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
