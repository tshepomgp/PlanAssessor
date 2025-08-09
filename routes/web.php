<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlanController;

Route::get('/dashboard', function () {
    return redirect('/LandingPage');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/LandingPage', [PlanController::class, 'listClientFolders'])->name('plans.index');
 //   Route::post('/plans/upload', [PlanController::class, 'upload'])->name('plans.upload');
});

Route::get('/profile', function () {
    return view('profile');
})->middleware(['auth'])->name('profile.edit');


Route::get('/plans/folders', [PlanController::class, 'listClientFolders'])->name('plans.folders');
Route::get('/plans/folder/{client}', [PlanController::class, 'viewClientFolder'])->name('plans.client.folder');

// routes/web.php
Route::get('/LandingPage', [PlanController::class, 'listClientFolders'])->name('plans.index');
Route::get('/plans/client/{client}', [PlanController::class, 'viewClientFolder'])->name('plans.client.folder');
Route::post('/plans/upload', [PlanController::class, 'upload'])->name('plans.upload');
Route::get('/plans/download/{plan}', [PlanController::class, 'download'])->name('plans.download');




require __DIR__.'/auth.php';
