<?php

namespace App\Observers;

use App\Models\ProjectTechnology;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProjectTechnologyObserver
{
    /**
     * Array di tag cache da invalidare
     */
    private array $cacheTags = [
        'project_technologies',
        'skills_technologies',
        'skills_stats'
    ];

    /**
     * Handle the ProjectTechnology "created" event.
     */
    public function created(ProjectTechnology $technology): void
    {
        $this->invalidateCache('created', $technology);
    }

    /**
     * Handle the ProjectTechnology "updated" event.
     */
    public function updated(ProjectTechnology $technology): void
    {
        $this->invalidateCache('updated', $technology);
    }

    /**
     * Handle the ProjectTechnology "deleted" event.
     */
    public function deleted(ProjectTechnology $technology): void
    {
        $this->invalidateCache('deleted', $technology);
    }

    /**
     * Handle the ProjectTechnology "restored" event.
     */
    public function restored(ProjectTechnology $technology): void
    {
        $this->invalidateCache('restored', $technology);
    }

    /**
     * Handle the ProjectTechnology "force deleted" event.
     */
    public function forceDeleted(ProjectTechnology $technology): void
    {
        $this->invalidateCache('forceDeleted', $technology);
    }

    /**
     * Invalida la cache per i tag correlati
     */
    private function invalidateCache(string $event, ProjectTechnology $technology): void
    {
        try {
            // Invalida tutti i tag correlati
            Cache::tags($this->cacheTags)->flush();

            // Log dell'operazione per debugging
            Log::info('Cache invalidata per ProjectTechnology', [
                'event' => $event,
                'technology_id' => $technology->id,
                'technology_name' => $technology->name,
                'tags' => $this->cacheTags
            ]);
        } catch (\Exception $e) {
            // Se il driver cache non supporta i tag, usa fallback
            $this->fallbackCacheInvalidation();

            Log::warning('Fallback cache invalidation utilizzato', [
                'reason' => $e->getMessage()
            ]);
        }
    }

    /**
     * Fallback per driver cache che non supportano i tag
     */
    private function fallbackCacheInvalidation(): void
    {
        $cacheKeys = [
            'skills_technologies',
            'skills_stats',
            'admin_technologies_types'
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }
}
