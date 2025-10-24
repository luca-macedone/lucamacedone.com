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
        Schema::create('work_experiences', function (Blueprint $table) {
            $table->id();
            $table->string('job_title');
            $table->string('company');
            $table->string('location')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable(); // null se lavoro attuale
            $table->boolean('is_current')->default(false);
            $table->text('description')->nullable();
            $table->json('key_achievements')->nullable(); // array di achievements
            $table->json('technologies')->nullable(); // array di tecnologie
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('company_logo')->nullable(); // percorso logo aziendale
            $table->string('company_url')->nullable();
            $table->enum('employment_type', ['full-time', 'part-time', 'contract', 'freelance', 'internship'])->default('full-time');
            $table->timestamps();

            // Indici per ottimizzazione
            $table->index(['is_active', 'sort_order']);
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_experiences');
    }
};
