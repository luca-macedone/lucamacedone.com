<?php
// app/Livewire/Frontend/Welcome.php

namespace App\Livewire\Frontend;

use App\Models\Project;
use App\Models\ProjectTechnology;
use Livewire\Component;

class Welcome extends Component
{
    /**
     * Ottieni progetti in evidenza per la homepage
     */
    public function getFeaturedProjects()
    {
        // Verifica prima se i metodi esistono e sono configurati correttamente
        try {
            return Project::query()
                ->with(['categories:id,name,color', 'technologies:id,name,icon'])
                ->select([
                    'id',
                    'title',
                    'slug',
                    'description',
                    'featured_image',
                    'client',
                    'status',
                    'is_featured',
                    'sort_order',
                    'created_at'
                ])
                ->where('status', 'published')
                ->where('is_featured', true)
                ->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get();
        } catch (\Exception $e) {
            // Se c'è un errore, ritorna collezione vuota
            \Log::error('Error fetching featured projects: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Ottieni progetti per la homepage (featured + recenti se necessario)
     */
    public function getProjectsForHomepage()
    {
        $featured = $this->getFeaturedProjects();

        // Se non ci sono abbastanza progetti featured, completa con i più recenti
        if ($featured->count() < 4) {
            try {
                $latest = Project::query()
                    ->with(['categories:id,name,color', 'technologies:id,name,icon'])
                    ->select([
                        'id',
                        'title',
                        'slug',
                        'description',
                        'featured_image',
                        'client',
                        'status',
                        'is_featured',
                        'sort_order',
                        'created_at'
                    ])
                    ->where('status', 'published')
                    ->whereNotIn('id', $featured->pluck('id')->toArray())
                    ->orderBy('created_at', 'desc')
                    ->limit(4 - $featured->count())
                    ->get();

                return $featured->merge($latest);
            } catch (\Exception $e) {
                \Log::error('Error fetching latest projects: ' . $e->getMessage());
                return $featured;
            }
        }

        return $featured;
    }

    /**
     * Ottieni statistiche per la homepage
     */
    public function getStats()
    {
        try {
            // Calcola gli anni di esperienza
            $startYear = config('app.experience_start_year', 2018);
            $yearsExperience = now()->year - $startYear;

            // Conta progetti pubblicati
            $totalProjects = Project::where('status', 'published')->count();

            // Conta tecnologie utilizzate
            $technologiesUsed = ProjectTechnology::count();

            // Conta clienti serviti (distinct)
            $clientsServed = Project::query()
                ->whereNotNull('client')
                ->where('client', '!=', '')
                ->where('status', 'published')
                ->distinct()
                ->count('client');

            return [
                'total_projects' => $totalProjects,
                'years_experience' => $yearsExperience,
                'technologies_used' => $technologiesUsed,
                'clients_served' => $clientsServed,
            ];
        } catch (\Exception $e) {
            \Log::error('Error fetching stats: ' . $e->getMessage());

            // Ritorna valori default in caso di errore
            return [
                'total_projects' => 0,
                'years_experience' => now()->year - 2018,
                'technologies_used' => 0,
                'clients_served' => 0,
            ];
        }
    }

    /**
     * Render del componente
     */
    public function render()
    {
        return view('livewire.frontend.welcome', [
            'featuredProjects' => $this->getProjectsForHomepage(),
            'stats' => $this->getStats()
        ])->layout('layouts.guest');
    }
}
