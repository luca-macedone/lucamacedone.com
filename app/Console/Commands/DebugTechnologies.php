<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProjectTechnology;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DebugTechnologies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:technologies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug technologies and their relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== DEBUG TECHNOLOGIES ===');
        $this->newLine();

        // 1. Controlla le tabelle
        $this->checkTables();

        // 2. Controlla i dati
        $this->checkData();

        // 3. Controlla le relazioni
        $this->checkRelationships();

        // 4. Controlla la cache
        $this->checkCache();

        // 5. Test delle query
        $this->testQueries();

        return Command::SUCCESS;
    }

    /**
     * Controlla l'esistenza delle tabelle
     */
    protected function checkTables()
    {
        $this->info('1. Controllo Tabelle:');

        $tables = [
            'project_technologies' => DB::table('information_schema.tables')
                ->where('table_schema', DB::getDatabaseName())
                ->where('table_name', 'project_technologies')
                ->exists(),
            'projects' => DB::table('information_schema.tables')
                ->where('table_schema', DB::getDatabaseName())
                ->where('table_name', 'projects')
                ->exists(),
            'project_technology_pivot' => DB::table('information_schema.tables')
                ->where('table_schema', DB::getDatabaseName())
                ->where('table_name', 'project_technology_pivot')
                ->exists(),
        ];

        foreach ($tables as $table => $exists) {
            $this->line("  - {$table}: " . ($exists ? '✅ Esiste' : '❌ NON ESISTE'));
        }

        $this->newLine();
    }

    /**
     * Controlla i dati nelle tabelle
     */
    protected function checkData()
    {
        $this->info('2. Controllo Dati:');

        // Conta tecnologie
        $techCount = ProjectTechnology::count();
        $this->line("  - Tecnologie totali: {$techCount}");

        if ($techCount > 0) {
            $this->line("  - Prime 3 tecnologie:");
            ProjectTechnology::take(3)->get()->each(function ($tech) {
                $this->line("    • {$tech->name} (ID: {$tech->id}, Category: {$tech->category})");
            });
        }

        // Conta progetti
        $projectCount = Project::count();
        $publishedCount = Project::where('is_published', true)->count();
        $featuredCount = Project::where('is_featured', true)->count();

        $this->line("  - Progetti totali: {$projectCount}");
        $this->line("    • Pubblicati: {$publishedCount}");
        $this->line("    • In evidenza: {$featuredCount}");
        $this->line(
            "    • Non in evidenza e pubblicati: " .
                Project::where('is_published', true)->where('is_featured', false)->count()
        );

        $this->newLine();
    }

    /**
     * Controlla le relazioni nella tabella pivot
     */
    protected function checkRelationships()
    {
        $this->info('3. Controllo Relazioni (tabella pivot):');

        // Conta relazioni nella tabella pivot
        $pivotCount = DB::table('project_technology_pivot')->count();
        $this->line("  - Relazioni totali nella pivot: {$pivotCount}");

        if ($pivotCount > 0) {
            // Mostra alcune relazioni di esempio
            $this->line("  - Prime 5 relazioni:");
            $relations = DB::table('project_technology_pivot')
                ->join('projects', 'projects.id', '=', 'project_technology_pivot.project_id')
                ->join('project_technologies', 'project_technologies.id', '=', 'project_technology_pivot.project_technology_id')
                ->select(
                    'projects.title as project_title',
                    'project_technologies.name as tech_name',
                    'projects.is_published',
                    'projects.is_featured'
                )
                ->limit(5)
                ->get();

            foreach ($relations as $rel) {
                $status = $rel->is_published ? '✅ Pubblicato' : '❌ Non pubblicato';
                $featured = $rel->is_featured ? '⭐ Featured' : '';
                $this->line("    • {$rel->project_title} → {$rel->tech_name} {$status} {$featured}");
            }
        }

        // Conta tecnologie con progetti
        $techsWithProjects = ProjectTechnology::has('projects')->count();
        $this->line("  - Tecnologie con almeno un progetto: {$techsWithProjects}");

        $this->newLine();
    }

    /**
     * Controlla lo stato della cache
     */
    protected function checkCache()
    {
        $this->info('4. Controllo Cache:');

        $this->line("  - Driver cache attuale: " . config('cache.default'));

        $cacheKeys = [
            'luca_macedone_cache_skills_technologies',
            'luca_macedone_cache_skills_stats'
        ];

        foreach ($cacheKeys as $key) {
            $hasCache = Cache::has($key);
            $this->line("  - {$key}: " . ($hasCache ? '✅ Presente' : '❌ Assente'));

            if ($hasCache) {
                $data = Cache::get($key);
                if (is_countable($data)) {
                    $count = count($data);
                    $this->line("    Elementi: {$count}");
                }
            }
        }

        $this->newLine();
    }

    /**
     * Test delle query utilizzate
     */
    protected function testQueries()
    {
        $this->info('5. Test Query:');

        // Test query base
        $this->line("  - Query base tecnologie:");
        $basicTechs = ProjectTechnology::take(3)->get();
        $this->line("    Risultati: " . $basicTechs->count());

        // Test query con withCount
        $this->line("  - Query con conteggio progetti:");
        $techsWithCount = ProjectTechnology::withCount(['projects' => function ($query) {
            $query->where('is_published', true)
                ->where('is_featured', false);
        }])->take(3)->get();

        foreach ($techsWithCount as $tech) {
            $this->line("    • {$tech->name}: {$tech->projects_count} progetti");
        }

        // Test query completa come nel componente
        $this->line("  - Query completa del componente:");
        $fullQuery = ProjectTechnology::withCount(['projects' => function ($query) {
            $query->where('is_published', true)
                ->where('is_featured', false);
        }])
            ->orderBy('name')
            ->get();

        $this->line("    Tecnologie trovate: " . $fullQuery->count());
        $this->line("    Progetti totali (non featured): " . $fullQuery->sum('projects_count'));

        $this->newLine();
    }
}
