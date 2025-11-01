<?php

namespace App\Providers;

use App\Models\ProjectTechnology;
use App\Observers\ProjectTechnologyObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (config('cache.default') === 'file') {
            $this->app->register(\App\Providers\CacheServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Previeni lazy loading in sviluppo
        Model::preventLazyLoading(!$this->app->isProduction());

        // Registra gli Observer
        ProjectTechnology::observe(ProjectTechnologyObserver::class);

        // Verifica supporto cache tags
        $this->checkCacheTagSupport();
    }

    /**
     * Verifica se il driver cache supporta i tag
     */
    private function checkCacheTagSupport(): void
    {
        $driver = config('cache.default');
        $supportsTagging = in_array($driver, ['redis', 'memcached', 'dynamodb', 'array']);

        if (!$supportsTagging && app()->isLocal()) {
            logger()->warning("Il driver cache '{$driver}' non supporta il tagging. Considera l'uso di Redis o Memcached per funzionalità complete.");
        }
    }
}
