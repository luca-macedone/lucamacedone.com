<?php
// app/Livewire/Frontend/Welcome.php

namespace App\Livewire\Frontend;

use App\Models\Project;
use App\Models\ProjectTechnology;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Welcome extends Component
{
    // Cache configuration
    private const CACHE_TTL = 3600; // 1 ora
    private const CACHE_PREFIX = 'luca_macedone_cache_';

    /**
     * Ottieni progetti in evidenza per la homepage (con cache)
     */
    public function getFeaturedProjects()
    {
        $cacheKey = self::CACHE_PREFIX . 'featured_projects';

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
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
                \Log::error('Error fetching featured projects: ' . $e->getMessage());
                return collect([]);
            }
        });
    }

    /**
     * Ottieni progetti per la homepage (featured + recenti se necessario) (con cache)
     */
    public function getProjectsForHomepage()
    {
        $cacheKey = self::CACHE_PREFIX . 'homepage_projects';

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
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
        });
    }

    /**
     * Ottieni statistiche per la homepage (con cache)
     */
    public function getStats()
    {
        $cacheKey = self::CACHE_PREFIX . 'homepage_stats';

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
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
        });
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
