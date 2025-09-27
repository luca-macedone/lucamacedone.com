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
        Schema::table('projects', function (Blueprint $table) {
            // Aggiungi colonne SEO
            $table->string('meta_title', 60)->nullable()->after('is_featured');
            $table->string('meta_description', 160)->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');

            // Rinomina gallery in gallery_images per consistenza con il codice
            $table->renameColumn('gallery', 'gallery_images');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'meta_keywords']);
            $table->renameColumn('gallery_images', 'gallery');
        });
    }
};
