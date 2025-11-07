<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\ProjectTechnology;
use App\Models\ProjectCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearProjectCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-project 
                            {--all : Pulisce tutta la cache del progetto}
                            {--technologies : Pulisce solo la cache delle tecnologie}
                            {--projects : Pulisce solo la cache dei progetti}
                            {--stats : Pulisce solo la cache delle statistiche}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulisce la cache specifica del progetto portfolio';

    /**
     * Cache prefix
     */
    private const CACHE_PREFIX = 'luca_macedone_cache_';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Pulizia cache in corso...');

        $cleared = 0;

        // Pulisce tutto
        if ($this->option('all')) {
            $cleared += $this->clearAllCache();
            $this->info("Cache completamente pulita. {$cleared} chiavi rimosse.");
            return Command::SUCCESS;
        }

        // Pulisce cache specifiche
        if ($this->option('technologies')) {
            $cleared += $this->clearTechnologiesCache();
            $this->info("Cache tecnologie pulita.");
        }

        if ($this->option('projects')) {
            $cleared += $this->clearProjectsCache();
            $this->info("Cache progetti pulita.");
        }

        if ($this->option('stats')) {
            $cleared += $this->clearStatsCache();
            $this->info("Cache statistiche pulita.");
        }

        // Se nessuna opzione, pulisce tutto
        if (!$this->option('technologies') && !$this->option('projects') && !$this->option('stats')) {
            $cleared += $this->clearAllCache();
            $this->info("Cache completamente pulita.");
        }

        $this->info("Totale chiavi rimosse: {$cleared}");

        // Rigenera cache importante
        if ($this->confirm('Vuoi rigenerare la cache principale?', true)) {
            $this->regenerateCache();
        }

        return Command::SUCCESS;
    }

    /**
     * Pulisce tutta la cache del progetto
     */
    private function clearAllCache(): int
    {
        $count = 0;
        $count += $this->clearTechnologiesCache();
        $count += $this->clearProjectsCache();
        $count += $this->clearStatsCache();
        $count += $this->clearCategoriesCache();

        return $count;
    }

    /**
     * Pulisce cache delle tecnologie
     */
    private function clearTechnologiesCache(): int
    {
        $keys = [
            'skills_technologies',
            'admin_technologies_types',
            'technologies_categories_list',
        ];

        $count = $this->clearKeys($keys);

        // Pulisce cache per categoria
        if (class_exists(ProjectTechnology::class)) {
            $categories = ProjectTechnology::distinct('category')->pluck('category');
            foreach ($categories as $category) {
                if (Cache::forget(self::CACHE_PREFIX . 'technologies_category_' . \Str::slug($category))) {
                    $count++;
                }
            }

            // Pulisce cache singole tecnologie
            $ids = ProjectTechnology::pluck('id');
            foreach ($ids as $id) {
                if (Cache::forget(self::CACHE_PREFIX . 'technology_' . $id)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Pulisce cache dei progetti
     */
    private function clearProjectsCache(): int
    {
        $keys = [
            'featured_projects',
            'all_projects',
            'published_projects',
            'projects_count',
        ];

        $count = $this->clearKeys($keys);

        // Pulisce cache singoli progetti
        if (class_exists(Project::class)) {
            $ids = Project::pluck('id');
            foreach ($ids as $id) {
                if (Cache::forget(self::CACHE_PREFIX . 'project_' . $id)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Pulisce cache delle statistiche
     */
    private function clearStatsCache(): int
    {
        $keys = [
            'skills_stats',
            'project_stats',
            'home_stats',
            'dashboard_stats',
        ];

        return $this->clearKeys($keys);
    }

    /**
     * Pulisce cache delle categorie
     */
    private function clearCategoriesCache(): int
    {
        $keys = [
            'project_categories',
            'technology_categories',
        ];

        return $this->clearKeys($keys);
    }

    /**
     * Helper per pulire array di chiavi
     */
    private function clearKeys(array $keys): int
    {
        $count = 0;

        foreach ($keys as $key) {
            if (Cache::forget(self::CACHE_PREFIX . $key)) {
                $count++;
                $this->line("  - Rimossa: {$key}");
            }
        }

        return $count;
    }

    /**
     * Rigenera la cache principale
     */
    private function regenerateCache(): void
    {
        $this->info('Rigenerazione cache in corso...');

        $bar = $this->output->createProgressBar(4);
        $bar->start();

        // Rigenera cache tecnologie
        if (class_exists(ProjectTechnology::class)) {
            ProjectTechnology::getWithProjectsCount();
            $bar->advance();
            $this->line(' Tecnologie');
        }

        // Rigenera cache statistiche
        if (class_exists(ProjectTechnology::class)) {
            ProjectTechnology::getSkillsStats();
            $bar->advance();
            $this->line(' Statistiche');
        }

        // Rigenera cache progetti featured
        if (class_exists(Project::class) && method_exists(Project::class, 'getFeatured')) {
            Project::getFeatured();
            $bar->advance();
            $this->line(' Progetti in evidenza');
        }

        // Rigenera cache categorie
        if (class_exists(ProjectCategory::class) && method_exists(ProjectCategory::class, 'getAllCached')) {
            ProjectCategory::getAllCached();
            $bar->advance();
            $this->line(' Categorie');
        }

        $bar->finish();
        $this->newLine();
        $this->info('Cache rigenerata con successo!');
    }
}
