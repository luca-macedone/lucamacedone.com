<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to add performance indexes
     */
    public function up(): void
    {
        // Indici per la tabella projects
        Schema::table('projects', function (Blueprint $table) {
            // Indice per ricerche slug (URL SEO-friendly)
            $table->index('slug', 'idx_projects_slug');

            // Indice per filtro status
            $table->index('status', 'idx_projects_status');

            // Indice per progetti in evidenza
            $table->index('is_featured', 'idx_projects_featured');

            // Indice composito per query comuni (progetti pubblicati e in evidenza)
            $table->index(['status', 'is_featured'], 'idx_projects_status_featured');

            // Indice per ordinamento
            $table->index('sort_order', 'idx_projects_sort_order');

            // Indice composito per ordinamento con data
            $table->index(['sort_order', 'created_at'], 'idx_projects_sort_created');

            // Indice per date (utile per timeline e filtri)
            $table->index('start_date', 'idx_projects_start_date');
            $table->index('end_date', 'idx_projects_end_date');

            // Indice full-text per ricerche (MySQL/MariaDB)
            if (DB::connection()->getDriverName() === 'mysql') {
                $table->fullText(['title', 'description'], 'idx_projects_fulltext');
            }
        });

        // Indici per tabelle pivot many-to-many
        Schema::table('project_category_pivot', function (Blueprint $table) {
            // Indice composito per relazioni efficienti
            $table->index(['project_id', 'project_category_id'], 'idx_project_category');

            // Indice inverso per query dalla categoria
            $table->index(['project_category_id', 'project_id'], 'idx_category_project');
        });

        Schema::table('project_technology_pivot', function (Blueprint $table) {
            // Indice composito per relazioni efficienti
            $table->index(['project_id', 'project_technology_id'], 'idx_project_technology');

            // Indice inverso per query dalla tecnologia
            $table->index(['project_technology_id', 'project_id'], 'idx_technology_project');
        });

        // Indici per categorie
        Schema::table('project_categories', function (Blueprint $table) {
            $table->index('slug', 'idx_categories_slug');
            $table->index('sort_order', 'idx_categories_sort_order');
        });

        // Indici per tecnologie
        Schema::table('project_technologies', function (Blueprint $table) {
            $table->index('slug', 'idx_technologies_slug');
            $table->index('category', 'idx_technologies_category');

            // Indice composito per query raggruppate per categoria
            $table->index(['category', 'name'], 'idx_technologies_category_name');
        });

        // Indici per immagini
        Schema::table('project_images', function (Blueprint $table) {
            $table->index('project_id', 'idx_images_project_id');
            $table->index('type', 'idx_images_type');
            $table->index('sort_order', 'idx_images_sort_order');

            // Indice composito per query di galleria ordinate
            $table->index(['project_id', 'type', 'sort_order'], 'idx_images_gallery');
        });

        // Indici per SEO
        Schema::table('project_seo', function (Blueprint $table) {
            // Indice univoco per evitare duplicati
            $table->unique('project_id', 'idx_seo_project_unique');

            // Indice full-text per meta description (se supportato)
            if (DB::connection()->getDriverName() === 'mysql') {
                $table->fullText('meta_description', 'idx_seo_meta_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rimuovi indici dalla tabella projects
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('idx_projects_slug');
            $table->dropIndex('idx_projects_status');
            $table->dropIndex('idx_projects_featured');
            $table->dropIndex('idx_projects_status_featured');
            $table->dropIndex('idx_projects_sort_order');
            $table->dropIndex('idx_projects_sort_created');
            $table->dropIndex('idx_projects_start_date');
            $table->dropIndex('idx_projects_end_date');

            if (DB::connection()->getDriverName() === 'mysql') {
                $table->dropFullText('idx_projects_fulltext');
            }
        });

        // Rimuovi indici dalle tabelle pivot
        Schema::table('project_category_pivot', function (Blueprint $table) {
            $table->dropIndex('idx_project_category');
            $table->dropIndex('idx_category_project');
        });

        Schema::table('project_technology_pivot', function (Blueprint $table) {
            $table->dropIndex('idx_project_technology');
            $table->dropIndex('idx_technology_project');
        });

        // Rimuovi indici dalle categorie
        Schema::table('project_categories', function (Blueprint $table) {
            $table->dropIndex('idx_categories_slug');
            $table->dropIndex('idx_categories_sort_order');
        });

        // Rimuovi indici dalle tecnologie
        Schema::table('project_technologies', function (Blueprint $table) {
            $table->dropIndex('idx_technologies_slug');
            $table->dropIndex('idx_technologies_category');
            $table->dropIndex('idx_technologies_category_name');
        });

        // Rimuovi indici dalle immagini
        Schema::table('project_images', function (Blueprint $table) {
            $table->dropIndex('idx_images_project_id');
            $table->dropIndex('idx_images_type');
            $table->dropIndex('idx_images_sort_order');
            $table->dropIndex('idx_images_gallery');
        });

        // Rimuovi indici SEO
        Schema::table('project_seo', function (Blueprint $table) {
            $table->dropUnique('idx_seo_project_unique');

            if (DB::connection()->getDriverName() === 'mysql') {
                $table->dropFullText('idx_seo_meta_description');
            }
        });
    }
};
