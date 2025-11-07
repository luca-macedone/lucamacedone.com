<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait Cacheable
{
    /**
     * TTL di default per la cache (1 ora)
     */
    protected static $defaultCacheTTL = 3600;

    /**
     * Prefisso per le chiavi di cache
     */
    protected static $cachePrefix = 'luca_macedone_cache_';

    /**
     * Lista delle chiavi di cache associate al modello
     */
    protected $cacheKeys = [];

    /**
     * Boot del trait
     */
    public static function bootCacheable()
    {
        // Invalida cache su eventi del modello
        static::saved(function ($model) {
            $model->invalidateModelCache();
        });

        static::deleted(function ($model) {
            $model->invalidateModelCache();
        });

        static::created(function ($model) {
            $model->invalidateModelCache();
        });
    }

    /**
     * Remember con gestione centralizzata delle chiavi
     */
    protected static function cacheRemember(string $key, $ttl = null, \Closure $callback)
    {
        $fullKey = static::getCacheKey($key);
        $ttl = $ttl ?? static::$defaultCacheTTL;

        return Cache::remember($fullKey, $ttl, $callback);
    }

    /**
     * Salva in cache
     */
    protected static function cachePut(string $key, $value, $ttl = null): bool
    {
        $fullKey = static::getCacheKey($key);
        $ttl = $ttl ?? static::$defaultCacheTTL;

        return Cache::put($fullKey, $value, $ttl);
    }

    /**
     * Recupera dalla cache
     */
    protected static function cacheGet(string $key, $default = null)
    {
        $fullKey = static::getCacheKey($key);
        return Cache::get($fullKey, $default);
    }

    /**
     * Verifica se esiste in cache
     */
    protected static function cacheHas(string $key): bool
    {
        $fullKey = static::getCacheKey($key);
        return Cache::has($fullKey);
    }

    /**
     * Rimuove dalla cache
     */
    protected static function cacheForget(string $key): bool
    {
        $fullKey = static::getCacheKey($key);
        return Cache::forget($fullKey);
    }

    /**
     * Genera la chiave di cache completa
     */
    protected static function getCacheKey(string $key): string
    {
        return static::$cachePrefix . $key;
    }

    /**
     * Invalida tutte le cache del modello
     */
    public function invalidateModelCache(): void
    {
        // Invalida cache generiche del modello
        $modelName = Str::snake(class_basename($this));

        $genericKeys = [
            $modelName . '_all',
            $modelName . '_count',
            $modelName . '_' . $this->getKey(),
            $modelName . '_list',
            $modelName . '_stats',
        ];

        foreach ($genericKeys as $key) {
            static::cacheForget($key);
        }

        // Invalida chiavi specifiche del modello
        $this->invalidateSpecificCache();

        // Invalida cache custom definite nel modello
        foreach ($this->getCacheKeys() as $key) {
            static::cacheForget($key);
        }
    }

    /**
     * Da sovrascrivere nei modelli per invalidazione specifica
     */
    protected function invalidateSpecificCache(): void
    {
        // Override in model if needed
    }

    /**
     * Recupera le chiavi di cache del modello
     */
    protected function getCacheKeys(): array
    {
        return $this->cacheKeys;
    }

    /**
     * Aggiunge una chiave di cache
     */
    protected function addCacheKey(string $key): void
    {
        if (!in_array($key, $this->cacheKeys)) {
            $this->cacheKeys[] = $key;
        }
    }

    /**
     * Pulisce cache con pattern (simulazione per driver non-Redis)
     */
    protected static function clearCachePattern(string $pattern): void
    {
        // Per database/file driver non possiamo usare wildcards
        // Dobbiamo gestire manualmente i pattern conosciuti

        $prefix = static::$cachePrefix;

        // Se il driver supporta i pattern (Redis/Memcached)
        if (static::cacheDriverSupportsPatterns()) {
            Cache::deletePattern($prefix . $pattern);
            return;
        }

        // Altrimenti gestiamo manualmente i pattern comuni
        static::clearKnownPatterns($pattern);
    }

    /**
     * Verifica se il driver supporta i pattern
     */
    protected static function cacheDriverSupportsPatterns(): bool
    {
        $driver = config('cache.default');
        return in_array($driver, ['redis', 'memcached', 'dynamodb']);
    }

    /**
     * Pulisce pattern conosciuti per driver che non supportano wildcards
     */
    protected static function clearKnownPatterns(string $pattern): void
    {
        // Da implementare nel modello specifico con i pattern conosciuti
        // Esempio: se pattern è "user_*", itera su tutti gli user ID conosciuti
    }

    /**
     * Cache con lock per prevenire cache stampede
     */
    protected static function cacheRememberWithLock(string $key, $ttl = null, \Closure $callback)
    {
        $fullKey = static::getCacheKey($key);
        $lockKey = $fullKey . '_lock';
        $ttl = $ttl ?? static::$defaultCacheTTL;

        // Se il valore è in cache, restituiscilo
        if (Cache::has($fullKey)) {
            return Cache::get($fullKey);
        }

        // Prova ad acquisire il lock
        $lock = Cache::lock($lockKey, 10);

        if ($lock->get()) {
            try {
                // Ricontrolla la cache (potrebbe essere stata popolata nel frattempo)
                if (Cache::has($fullKey)) {
                    return Cache::get($fullKey);
                }

                // Calcola e salva il valore
                $value = $callback();
                Cache::put($fullKey, $value, $ttl);

                return $value;
            } finally {
                $lock->release();
            }
        }

        // Se non riusciamo ad acquisire il lock, aspetta e poi leggi dalla cache
        sleep(1);
        return Cache::get($fullKey, $callback());
    }

    /**
     * Invalida cache correlate tra modelli
     */
    protected function invalidateRelatedModelsCache(array $models): void
    {
        foreach ($models as $modelClass) {
            if (method_exists($modelClass, 'clearAllCache')) {
                $modelClass::clearAllCache();
            }
        }
    }
}
