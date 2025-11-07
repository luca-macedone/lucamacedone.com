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
        // Registra macro per cache senza tags
        Cache::macro('rememberWithoutTags', function ($key, $ttl, $callback) {
            // Usa direttamente il driver senza tags
            return Cache::driver(config('cache.default'))
                ->remember($key, $ttl, $callback);
        });

        // Registra macro per forget multiplo
        Cache::macro('forgetMultiple', function (array $keys) {
            $count = 0;
            foreach ($keys as $key) {
                if (Cache::forget($key)) {
                    $count++;
                }
            }
            return $count;
        });

        // Registra macro per verificare il supporto dei tags
        Cache::macro('supportsTags', function () {
            $driver = config('cache.default');
            return in_array($driver, ['redis', 'memcached', 'dynamodb']);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureCacheWarming();
        $this->configureRateLimiting();
        $this->registerCacheCommands();
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
