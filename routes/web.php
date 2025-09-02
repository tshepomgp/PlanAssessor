<?php

// Add these routes to your web.php file

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\PlanVerificationController;
use App\Http\Controllers\Admin\TokenPackageController;
use Illuminate\Support\Facades\Route;

// Existing routes...
Route::get('/dashboard', function () {
    return redirect('/LandingPage');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/LandingPage', [PlanController::class, 'listClientFolders'])->name('plans.index');
    Route::get('/plans/folder/{client}', [PlanController::class, 'viewClientFolder'])->name('plans.client.folder');
    Route::post('/plans/upload', [PlanController::class, 'upload'])->name('plans.upload');
    Route::get('/plans/download/{plan}', [PlanController::class, 'download'])->name('plans.download');
    
    // Token Management Routes
    Route::prefix('tokens')->name('tokens.')->group(function () {
        Route::get('/', [TokenController::class, 'index'])->name('index');
        Route::post('/purchase', [TokenController::class, 'purchase'])->name('purchase');
        Route::get('/statement', [TokenController::class, 'statement'])->name('statement');
    });
});

// Admin Routes (protected by admin middleware)
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });
        
        // User Management
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
        Route::patch('/users/{user}/status', [AdminController::class, 'updateUserStatus'])->name('users.update-status');
        Route::post('/users/{user}/tokens', [AdminController::class, 'adjustUserTokens'])->name('users.adjust-tokens');
        
        // Plan Management & Verification
        Route::get('/plans', [AdminController::class, 'plans'])->name('plans');
        Route::get('/plans/{plan}', [AdminController::class, 'showPlan'])->name('plans.show');
        Route::post('/plans/{plan}/verify', [AdminController::class, 'verifyPlan'])->name('plans.verify');
        
        // Token Package Management
        Route::get('/token-packages', [AdminController::class, 'tokenPackages'])->name('token-packages');
        Route::post('/token-packages', [AdminController::class, 'storeTokenPackage'])->name('token-packages.store');
        Route::patch('/token-packages/{package}', [AdminController::class, 'updateTokenPackage'])->name('token-packages.update');
        
        // Analytics & Reports
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
        
        // System Settings
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    });

// Profile routes
Route::get('/profile', function () {
    return view('profile');
})->middleware(['auth'])->name('profile.edit');

Route::patch('/admin/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.update-role');


require __DIR__.'/auth.php';