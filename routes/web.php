<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SeoAnalysisController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
use App\Services\WordPressApiService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// WordPress sites management
Route::middleware(['auth'])->group(function () {
    // Sites routes
    Route::resource('sites', SiteController::class);
    Route::post('/sites/{site}/test-connection', [SiteController::class, 'testConnection'])->name('sites.test-connection');
    Route::get('/sites/{site}/app-password/instructions', [SiteController::class, 'showAppPasswordInstructions'])->name('sites.app-password.instructions');

    // JWT Debug Routes - temporarily removing the role:admin middleware
    // Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::middleware(['auth'])->group(function () { // Temporarily using only auth middleware
        Route::get('/jwt/debug/{site}', [App\Http\Controllers\JwtDebugController::class, 'debug'])->name('jwt.debug');
        Route::get('/jwt/verify/{site}', [App\Http\Controllers\JwtDebugController::class, 'verify'])->name('jwt.verify');
        Route::get('/jwt/compare/{site}', [App\Http\Controllers\JwtDebugController::class, 'compare'])->name('jwt.compare');
        Route::get('/jwt/get-token/{site}/{format?}', [App\Http\Controllers\JwtDebugController::class, 'getToken'])->name('jwt.get-token');
    });

    Route::post('/sites/{site}/sync', [SiteController::class, 'syncSite'])->name('sites.sync');
    Route::post('/sites/{site}/jwt/setup', [SiteController::class, 'setupJwt'])->name('sites.jwt.setup');
    Route::post('/sites/{site}/generate-jwt', [SiteController::class, 'generateJwtSecret'])->name('sites.generate-jwt');
    Route::post('/sites/{site}/revoke-jwt', [SiteController::class, 'revokeJwtSecret'])->name('sites.revoke-jwt');

    // Posts routes
    Route::resource('posts', PostController::class);
    // Add publish to WordPress route
    Route::post('/posts/{post}/publish-to-wordpress', [PostController::class, 'publishToWordPress'])->name('posts.publish-to-wordpress');
    // Add fallback route for old URL format
    Route::post('/posts/{post}/publish', [PostController::class, 'publishToWordPress'])->name('posts.publish');

    // Push post with exact headers (fixing authentication issues)
    Route::post('/posts/{post}/push-exact', [App\Http\Controllers\PostController::class, 'pushWithExactHeaders'])->name('posts.push-exact');
    // Products routes
    Route::resource('products', ProductController::class);
    Route::get('/products/import', [ProductController::class, 'importForm'])->name('products.import');
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.process-import');

    // SEO analysis routes
    Route::resource('seo', SeoAnalysisController::class);

    // User management routes
    Route::resource('users', UserController::class);

    // User management - additional functionality
    Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete')->middleware('permission:delete users');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status')->middleware('permission:edit users');

    // Role management
    Route::group(['middleware' => 'permission:manage roles'], function() {
        Route::get('/roles', [UserController::class, 'roles'])->name('users.roles');
        Route::get('/roles/create', [UserController::class, 'createRole'])->name('users.create_role');
        Route::post('/roles', [UserController::class, 'storeRole'])->name('users.store_role');
        Route::get('/roles/{role}/edit', [UserController::class, 'editRole'])->name('users.edit_role');
        Route::put('/roles/{role}', [UserController::class, 'updateRole'])->name('users.update_role');
        Route::delete('/roles/{role}', [UserController::class, 'destroyRole'])->name('users.destroy_role');
    });
});

require __DIR__.'/auth.php';
