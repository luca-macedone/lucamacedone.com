<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TechnologyController;

// Livewire Components
use App\Livewire\Frontend\Welcome;
use App\Livewire\Frontend\Portfolio;
use App\Livewire\Frontend\ProjectShow;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\UserManager;
use App\Livewire\Admin\Projects\ProjectsList;
use App\Livewire\Admin\Projects\ProjectCreate;
use App\Livewire\Admin\Projects\ProjectEdit;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| FRONTEND ROUTES (GUEST/PUBLIC)
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', Welcome::class)->name('home');

// Portfolio pubblico
Route::prefix('portfolio')->name('portfolio.')->group(function () {
    Route::get('/', Portfolio::class)->name('index'); // Lista tutti i progetti
    Route::get('/{slug}', ProjectShow::class)->name('show'); // Singolo progetto
});

// Routes di contatto/informazioni
Route::get('/about', function () {
    return redirect('/');
})->name('about');

Route::get('/contact', function () {
    return redirect('/#contacts');
})->name('contact');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Dashboard utente (può essere diversa da admin)
    Route::get('/dashboard', AdminDashboard::class)
        ->middleware('verified')
        ->name('dashboard');

    // Profilo utente
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (BACKOFFICE)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard Admin
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');

    // Gestione Utenti
    Route::get('/users', UserManager::class)->name('users');

    // ===== GESTIONE PROGETTI =====
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', ProjectsList::class)->name('index');
        Route::get('/create', ProjectCreate::class)->name('create');
        Route::get('/{project}/edit', ProjectEdit::class)->name('edit');

        // API Routes per azioni AJAX
        Route::post('/{project}/toggle-status', [ProjectController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{project}/toggle-featured', [ProjectController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');

        // Bulk Actions
        Route::post('/bulk-delete', [ProjectController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-publish', [ProjectController::class, 'bulkPublish'])->name('bulk-publish');
        Route::post('/bulk-feature', [ProjectController::class, 'bulkFeature'])->name('bulk-feature');
        Route::post('/reorder', [ProjectController::class, 'reorder'])->name('reorder');
    });

    // ===== GESTIONE CATEGORIE =====
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::patch('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [CategoryController::class, 'reorder'])->name('reorder');
    });

    // ===== GESTIONE TECNOLOGIE =====
    Route::prefix('technologies')->name('technologies.')->group(function () {
        Route::get('/', [TechnologyController::class, 'index'])->name('index');
        Route::get('/create', [TechnologyController::class, 'create'])->name('create');
        Route::get('/{technology}/edit', [TechnologyController::class, 'edit'])->name('edit');
        Route::post('/', [TechnologyController::class, 'store'])->name('store');
        Route::patch('/{technology}', [TechnologyController::class, 'update'])->name('update');
        Route::delete('/{technology}', [TechnologyController::class, 'destroy'])->name('destroy');

        // Import/Export
        Route::get('/export', [TechnologyController::class, 'export'])->name('export');
        Route::post('/import', [TechnologyController::class, 'import'])->name('import');
    });

    // ===== MEDIA LIBRARY =====
    Route::prefix('media')->name('media.')->group(function () {
        Route::get('/', [MediaController::class, 'index'])->name('index');
        Route::post('/upload', [MediaController::class, 'upload'])->name('upload');
        Route::delete('/{media}', [MediaController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [MediaController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // ===== IMPOSTAZIONI =====
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/', [SettingsController::class, 'update'])->name('update');
        Route::get('/seo', [SettingsController::class, 'seo'])->name('seo');
        Route::post('/seo', [SettingsController::class, 'updateSeo'])->name('seo.update');
    });

    // ===== ANALYTICS =====
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        Route::get('/projects', [AnalyticsController::class, 'projects'])->name('projects');
        Route::get('/export', [AnalyticsController::class, 'export'])->name('export');
    });
});

/*
|--------------------------------------------------------------------------
| API ROUTES (se necessarie)
|--------------------------------------------------------------------------
*/

Route::prefix('api')->middleware('auth:sanctum')->group(function () {
    // Route per API se necessarie (es. mobile app)
    Route::get('/projects', [ProjectController::class, 'apiIndex']);
    Route::get('/projects/{project}', [ProjectController::class, 'apiShow']);
});

require __DIR__ . '/auth.php';
