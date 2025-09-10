<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TechnologyController;
use App\Livewire\Frontend\Welcome;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\UserManager;
use Illuminate\Support\Facades\Route;

Route::get('/', Welcome::class)->name('home');

Route::get('/dashboard', AdminDashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::prefix('progetti')->name('projects.')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('index');
    Route::get('/crea', [ProjectController::class, 'create'])->name('create');
    Route::get('/{project}/modifica', [ProjectController::class, 'edit'])->name('edit');
    Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');

    // Bulk actions
    Route::post('/bulk-delete', [ProjectController::class, 'bulkDelete'])->name('bulk-delete');
    Route::post('/bulk-publish', [ProjectController::class, 'bulkPublish'])->name('bulk-publish');
    Route::post('/bulk-feature', [ProjectController::class, 'bulkFeature'])->name('bulk-feature');

    // Ajax routes for quick actions
    Route::post('/{project}/toggle-status', [ProjectController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/{project}/toggle-featured', [ProjectController::class, 'toggleFeatured'])->name('toggle-featured');
    Route::post('/reorder', [ProjectController::class, 'reorder'])->name('reorder');
});

// Gestione Categorie
Route::prefix('categorie')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/crea', [CategoryController::class, 'create'])->name('create');
    Route::get('/{category}/modifica', [CategoryController::class, 'edit'])->name('edit');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    Route::post('/reorder', [CategoryController::class, 'reorder'])->name('reorder');
});

// Gestione Tecnologie
Route::prefix('tecnologie')->name('technologies.')->group(function () {
    Route::get('/', [TechnologyController::class, 'index'])->name('index');
    Route::get('/crea', [TechnologyController::class, 'create'])->name('create');
    Route::get('/{technology}/modifica', [TechnologyController::class, 'edit'])->name('edit');
    Route::delete('/{technology}', [TechnologyController::class, 'destroy'])->name('destroy');

    // Import/Export
    Route::get('/export', [TechnologyController::class, 'export'])->name('export');
    Route::post('/import', [TechnologyController::class, 'import'])->name('import');
});

// Media Library
Route::prefix('media')->name('media.')->group(function () {
    Route::get('/', [MediaController::class, 'index'])->name('index');
    Route::post('/upload', [MediaController::class, 'upload'])->name('upload');
    Route::delete('/{media}', [MediaController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-delete', [MediaController::class, 'bulkDelete'])->name('bulk-delete');
});

// Impostazioni
Route::prefix('impostazioni')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::post('/', [SettingsController::class, 'update'])->name('update');
    Route::get('/seo', [SettingsController::class, 'seo'])->name('seo');
    Route::post('/seo', [SettingsController::class, 'updateSeo'])->name('seo.update');
});

// Analytics e Statistiche
Route::prefix('analytics')->name('analytics.')->group(function () {
    Route::get('/', [AnalyticsController::class, 'index'])->name('index');
    Route::get('/progetti', [AnalyticsController::class, 'projects'])->name('projects');
    Route::get('/export', [AnalyticsController::class, 'export'])->name('export');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/users', UserManager::class)->name('users');
});

require __DIR__ . '/auth.php';
