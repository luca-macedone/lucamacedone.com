<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FlushProjectCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:flush-projects 
                            {--tags= : Flush solo tag specifici (separati da virgola)}
                            {--all : Flush tutta la cache dei progetti}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invalida la cache dei progetti e delle tecnologie';

    /**
     * Array di tutti i tag cache disponibili
     */
    private array $availableTags = [
        'project_technologies' => 'Cache delle tecnologie dei progetti',
        'skills_technologies' => 'Cache delle skills nella home',
        'skills_stats' => 'Cache delle statistiche skills',
        'projects' => 'Cache generale dei progetti',
        'project_categories' => 'Cache delle categorie progetti'
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔄 Iniziando invalidazione cache...');

        try {
            if ($this->option('all')) {
                $this->flushAllProjectCache();
            } elseif ($this->option('tags')) {
                $this->flushSpecificTags();
            } else {
                $this->interactiveFlush();
            }

            $this->newLine();
            $this->info('✅ Cache invalidata con successo!');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Errore durante l\'invalidazione della cache: ' . $e->getMessage());

            if ($this->confirm('Vuoi provare il metodo fallback?')) {
                $this->fallbackFlush();
                return Command::SUCCESS;
            }

            return Command::FAILURE;
        }
    }

    /**
     * Flush di tutta la cache dei progetti
     */
    private function flushAllProjectCache(): void
    {
        $this->line('Invalidando tutta la cache dei progetti...');

        $tags = array_keys($this->availableTags);

        if ($this->supportsTags()) {
            Cache::tags($tags)->flush();
            $this->info('Cache invalidata per i tag: ' . implode(', ', $tags));
        } else {
            $this->fallbackFlush();
        }
    }

    /**
     * Flush di tag specifici
     */
    private function flushSpecificTags(): void
    {
        $requestedTags = explode(',', $this->option('tags'));
        $validTags = [];

        foreach ($requestedTags as $tag) {
            $tag = trim($tag);
            if (isset($this->availableTags[$tag])) {
                $validTags[] = $tag;
            } else {
                $this->warn("Tag non valido ignorato: {$tag}");
            }
        }

        if (empty($validTags)) {
            $this->error('Nessun tag valido specificato.');
            return;
        }

        if ($this->supportsTags()) {
            Cache::tags($validTags)->flush();
            $this->info('Cache invalidata per i tag: ' . implode(', ', $validTags));
        } else {
            $this->fallbackFlushForTags($validTags);
        }
    }

    /**
     * Flush interattivo con selezione dei tag
     */
    private function interactiveFlush(): void
    {
        $this->table(
            ['Tag', 'Descrizione'],
            collect($this->availableTags)->map(function ($description, $tag) {
                return [$tag, $description];
            })
        );

        $selectedTags = $this->choice(
            'Quali tag vuoi invalidare?',
            array_merge(['all' => 'Tutti i tag'], $this->availableTags),
            'all',
            null,
            true
        );

        if (in_array('all', $selectedTags)) {
            $this->flushAllProjectCache();
        } else {
            if ($this->supportsTags()) {
                Cache::tags($selectedTags)->flush();
                $this->info('Cache invalidata per i tag: ' . implode(', ', $selectedTags));
            } else {
                $this->fallbackFlushForTags($selectedTags);
            }
        }
    }

    /**
     * Verifica se il driver cache supporta i tag
     */
    private function supportsTags(): bool
    {
        $driver = config('cache.default');
        return in_array($driver, ['redis', 'memcached', 'dynamodb', 'array']);
    }

    /**
     * Fallback per driver che non supportano i tag
     */
    private function fallbackFlush(): void
    {
        $this->warn('Il driver cache attuale non supporta i tag. Uso metodo fallback...');

        $keys = [
            'skills_technologies',
            'skills_stats',
            'admin_technologies_types',
            'project_list',
            'featured_projects'
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
            $this->line("Cache key rimossa: {$key}");
        }
    }

    /**
     * Fallback per tag specifici
     */
    private function fallbackFlushForTags(array $tags): void
    {
        $keyMap = [
            'skills_technologies' => ['skills_technologies'],
            'skills_stats' => ['skills_stats'],
            'project_technologies' => ['skills_technologies', 'admin_technologies_types'],
            'projects' => ['project_list', 'featured_projects'],
            'project_categories' => ['project_categories']
        ];

        $keysToFlush = [];
        foreach ($tags as $tag) {
            if (isset($keyMap[$tag])) {
                $keysToFlush = array_merge($keysToFlush, $keyMap[$tag]);
            }
        }

        $keysToFlush = array_unique($keysToFlush);

        foreach ($keysToFlush as $key) {
            Cache::forget($key);
            $this->line("Cache key rimossa: {$key}");
        }
    }
}
