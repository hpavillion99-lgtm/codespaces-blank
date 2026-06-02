<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MediaController;
use App\Models\Media;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// 1. Dynamic Homepage Endpoint (Protected with try-catch)
Route::get('/', function () {
    try {
        $file = Media::where('name', 'index.html')->first();
        $liveContent = '';
        
        if ($file && Storage::disk('public')->exists($file->file_path)) {
            $liveContent = Storage::disk('public')->get($file->file_path);
        }
        
        return view('welcome', compact('liveContent'));
    } catch (\Exception $e) {
        // Fallback to normal welcome page if database or storage fails
        return view('welcome', ['liveContent' => '']);
    }
});

// 2. Admin Dashboard View Route
Route::get('/dashboard', [MediaController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// 3. Authenticated Panel Endpoints
Route::middleware('auth')->group(function () {
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Complete File Manager Core Endpoints (Keeps dashboard active)
    Route::post('/media', [MediaController::class, 'store'])->name('media.store');
    Route::post('/media/create-file', [MediaController::class, 'createFile'])->name('media.createFile');
    Route::put('/media/{medium}', [MediaController::class, 'update'])->name('media.update');
    Route::delete('/media/{medium}', [MediaController::class, 'destroy'])->name('media.destroy');
});

require __DIR__.'/auth.php';