<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Prima migra i dati esistenti alle tabelle appropriate
        $this->migrateExistingData();

        // Poi rimuovi i campi ridondanti
        Schema::table('projects', function (Blueprint $table) {
            // Rimuovi campi che dovrebbero essere nelle tabelle relazionate
            $table->dropColumn([
                'gallery_images',  // Usa project_images table
                'technologies',    // Usa project_technology_pivot
                'meta_title',      // Usa project_seo table
                'meta_description', // Usa project_seo table
                'meta_keywords'    // Usa project_seo table
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->json('gallery_images')->nullable()->after('featured_image');
            $table->json('technologies')->nullable()->after('gallery_images');
            $table->string('meta_title', 60)->nullable()->after('is_featured');
            $table->string('meta_description', 160)->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
        });
    }

    /**
     * Migra i dati esistenti alle tabelle appropriate
     */
    private function migrateExistingData(): void
    {
        $projects = DB::table('projects')->get();

        foreach ($projects as $project) {
            // Migra gallery images alla tabella project_images
            if ($project->gallery_images) {
                $images = json_decode($project->gallery_images, true);
                if (is_array($images)) {
                    foreach ($images as $index => $imagePath) {
                        DB::table('project_images')->insertOrIgnore([
                            'project_id' => $project->id,
                            'filename' => $imagePath,
                            'original_name' => basename($imagePath),
                            'alt_text' => $project->title . ' - Gallery Image ' . ($index + 1),
                            'caption' => null,
                            'sort_order' => $index,
                            'type' => 'gallery',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Migra SEO data alla tabella project_seo
            if ($project->meta_title || $project->meta_description || $project->meta_keywords) {
                DB::table('project_seo')->insertOrIgnore([
                    'project_id' => $project->id,
                    'meta_title' => $project->meta_title,
                    'meta_description' => $project->meta_description,
                    'meta_keywords' => $project->meta_keywords ? json_encode(explode(',', $project->meta_keywords)) : null,
                    'og_image' => $project->featured_image, // Usa featured image come og:image
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
};
