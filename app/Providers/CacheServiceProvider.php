<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Non usiamo Cache facade qui perché non è ancora disponibile
        // Le macro verranno registrate nel boot()
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registra le macro DOPO che il servizio cache è disponibile
        $this->registerCacheMacros();
        $this->configureCacheWarming();
        $this->configureRateLimiting();
        $this->registerCacheCommands();
    }

    /**
     * Registra le macro per la cache
     */
    private function registerCacheMacros(): void
    {
        // Ora possiamo usare Cache facade perché siamo nel boot()
        Cache::macro('rememberWithoutTags', function ($key, $ttl, $callback) {
            return Cache::driver(config('cache.default'))
                ->remember($key, $ttl, $callback);
        });

        Cache::macro('forgetMultiple', function (array $keys) {
            $count = 0;
            foreach ($keys as $key) {
                if (Cache::forget($key)) {
                    $count++;
                }
            }
            return $count;
        });

        Cache::macro('supportsTags', function () {
            $driver = config('cache.default');
            return in_array($driver, ['redis', 'memcached', 'dynamodb']);
        });
    }

    /**
     * Configura il cache warming
     */
    private function configureCacheWarming(): void
    {
        // Se siamo in production, pre-carica alcune cache critiche
        if (app()->environment('production')) {
            // Usa un job in background per non rallentare il boot
            dispatch(function () {
                $this->warmCache();
            })->afterResponse();
        }
    }

    /**
     * Pre-carica cache critiche
     */
    private function warmCache(): void
    {
        try {
            // Pre-carica solo se la cache è vuota
            $cachePrefix = 'luca_macedone_cache_';

            if (!Cache::has($cachePrefix . 'skills_technologies')) {
                \App\Models\ProjectTechnology::getWithProjectsCount();
            }

            if (!Cache::has($cachePrefix . 'skills_stats')) {
                \App\Models\ProjectTechnology::getSkillsStats();
            }
        } catch (\Exception $e) {
            // Log dell'errore ma non bloccare l'applicazione
            \Log::warning('Cache warming failed: ' . $e->getMessage());
        }
    }

    /**
     * Configura rate limiting per la cache
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('cache-refresh', function ($request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }

    /**
     * Registra comandi personalizzati
     */
    private function registerCacheCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\ClearProjectCache::class,
            ]);
        }
    }
}
