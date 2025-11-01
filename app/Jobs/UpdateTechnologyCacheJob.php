<?php

namespace App\Jobs;

use App\Models\ProjectTechnology;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateTechnologyCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Numero di tentativi
     */
    public $tries = 3;

    /**
     * Timeout in secondi
     */
    public $timeout = 30;

    /**
     * ID della tecnologia da aggiornare (null per tutte)
     */
    protected ?int $technologyId;

    /**
     * Create a new job instance.
     */
    public function __construct(?int $technologyId = null)
    {
        $this->technologyId = $technologyId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if ($this->technologyId) {
                $this->updateSingleTechnology();
            } else {
                $this->updateAllTechnologies();
            }

            // Invalida la cache dopo l'aggiornamento
            $this->invalidateCache();

            Log::info('Technology cache updated successfully', [
                'technology_id' => $this->technologyId,
                'timestamp' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update technology cache', [
                'technology_id' => $this->technologyId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e; // Re-throw per retry automatico
        }
    }

    /**
     * Aggiorna cache per singola tecnologia
     */
    private function updateSingleTechnology(): void
    {
        $technology = ProjectTechnology::find($this->technologyId);

        if (!$technology) {
            Log::warning('Technology not found for cache update', [
                'technology_id' => $this->technologyId
            ]);
            return;
        }

        $count = $technology->projects()->count();

        $technology->update([
            'projects_count_cache' => $count,
            'cache_updated_at' => now()
        ]);
    }

    /**
     * Aggiorna cache per tutte le tecnologie
     */
    private function updateAllTechnologies(): void
    {
        // Usa una query ottimizzata per aggiornamento bulk
        DB::statement('
            UPDATE project_technologies pt
            LEFT JOIN (
                SELECT 
                    project_technology_id,
                    COUNT(*) as count
                FROM project_project_technology
                GROUP BY project_technology_id
            ) counts ON counts.project_technology_id = pt.id
            SET 
                pt.projects_count_cache = COALESCE(counts.count, 0),
                pt.cache_updated_at = NOW()
        ');
    }

    /**
     * Invalida la cache correlata
     */
    private function invalidateCache(): void
    {
        $tags = ['project_technologies', 'skills_technologies', 'skills_stats'];

        try {
            Cache::tags($tags)->flush();
        } catch (\Exception $e) {
            // Fallback per driver che non supportano tags
            Cache::forget('skills_technologies');
            Cache::forget('skills_stats');
        }
    }

    /**
     * Gestione dei fallimenti
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('UpdateTechnologyCacheJob permanently failed', [
            'technology_id' => $this->technologyId,
            'error' => $exception->getMessage()
        ]);

        // Notifica amministratori se necessario
        // Mail::to('admin@example.com')->send(new CacheUpdateFailed($exception));
    }
}
