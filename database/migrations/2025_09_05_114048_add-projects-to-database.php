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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->longText('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable(); // Array di immagini
            $table->json('technologies')->nullable(); // Array di tecnologie usate
            $table->string('client')->nullable();
            $table->string('project_url')->nullable();
            $table->string('github_url')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['draft', 'published', 'featured'])->default('draft');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('project_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->default('#3B82F6'); // Colore hex per categoria
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('project_category_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->string('original_name');
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->enum('type', ['gallery', 'featured', 'thumbnail'])->default('gallery');
            $table->timestamps();
        });

        Schema::create('project_technologies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable(); // Path icona o classe CSS
            $table->string('color')->default('#6B7280'); // Colore hex
            $table->string('category')->nullable(); // frontend, backend, database, etc.
            $table->timestamps();
        });

        Schema::create('project_technology_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_technology_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('project_seo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_seo');
        Schema::dropIfExists('project_technology_pivot');
        Schema::dropIfExists('project_technologies');
        Schema::dropIfExists('project_images');
        Schema::dropIfExists('project_category_pivot');
        Schema::dropIfExists('project_categories');
        Schema::dropIfExists('projects');
    }
};
