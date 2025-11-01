<?php
// database/migrations/2024_XX_XX_optimize_project_technologies_for_caching.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Aggiungi indici per migliorare le performance delle query cached
        Schema::table('project_technologies', function (Blueprint $table) {
            // Indice per categoria (usato nel raggruppamento)
            if (!$this->indexExists('project_technologies', 'idx_category')) {
                $table->index('category', 'idx_category');
            }

            // Indice composito per ordinamento
            if (!$this->indexExists('project_technologies', 'idx_category_name')) {
                $table->index(['category', 'name'], 'idx_category_name');
            }

            // Indice per soft deletes se utilizzato
            if (Schema::hasColumn('project_technologies', 'deleted_at')) {
                if (!$this->indexExists('project_technologies', 'idx_deleted_at')) {
                    $table->index('deleted_at', 'idx_deleted_at');
                }
            }
        });

        // Ottimizza la tabella pivot per conteggi più veloci
        Schema::table('project_technology_pivot', function (Blueprint $table) {
            // Indice per technology_id per COUNT queries
            if (!$this->indexExists('project_technology_pivot', 'idx_technology_count')) {
                $table->index('project_technology_id', 'idx_technology_count');
            }

            // Indice per project_id
            if (!$this->indexExists('project_technology_pivot', 'idx_project_id')) {
                $table->index('project_id', 'idx_project_id');
            }
        });

        // Aggiungi colonna cached per progetti se non esiste
        if (!Schema::hasColumn('project_technologies', 'projects_count_cache')) {
            Schema::table('project_technologies', function (Blueprint $table) {
                $table->unsignedInteger('projects_count_cache')->default(0)->after('color');
                $table->timestamp('cache_updated_at')->nullable()->after('projects_count_cache');
            });

            // Popola il conteggio iniziale
            $this->updateProjectsCacheCount();
        }

        // Crea tabella per tracking invalidazioni cache (opzionale, per monitoring)
        if (!Schema::hasTable('cache_invalidations')) {
            Schema::create('cache_invalidations', function (Blueprint $table) {
                $table->id();
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->string('event'); // created, updated, deleted, etc
                $table->json('tags_invalidated')->nullable();
                $table->string('triggered_by')->nullable(); // user, system, command
                $table->timestamps();

                $table->index(['model_type', 'model_id']);
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rimuovi indici
        Schema::table('project_technologies', function (Blueprint $table) {
            $table->dropIndex('idx_category');
            $table->dropIndex('idx_category_name');

            if (Schema::hasColumn('project_technologies', 'deleted_at')) {
                $table->dropIndex('idx_deleted_at');
            }

            if (Schema::hasColumn('project_technologies', 'projects_count_cache')) {
                $table->dropColumn(['projects_count_cache', 'cache_updated_at']);
            }
        });

        Schema::table('project_technology_pivot', function (Blueprint $table) {
            $table->dropIndex('idx_technology_count');
            $table->dropIndex('idx_project_id');
        });

        // Rimuovi tabella tracking
        Schema::dropIfExists('cache_invalidations');
    }

    /**
     * Verifica se un indice esiste
     */
    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table}");

        foreach ($indexes as $idx) {
            if ($idx->Key_name === $index) {
                return true;
            }
        }

        return false;
    }

    /**
     * Aggiorna il conteggio cache dei progetti
     */
    private function updateProjectsCacheCount(): void
    {
        DB::statement('
            UPDATE project_technologies pt
            SET projects_count_cache = (
                SELECT COUNT(*)
                FROM project_technology_pivot ptp
                WHERE ptp.project_technology_id = pt.id
            ),
            cache_updated_at = NOW()
        ');
    }
};
