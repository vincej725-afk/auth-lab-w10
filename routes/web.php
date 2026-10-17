<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('courses.index'));

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Everything in this group requires a logged-in user
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // create, store, edit, update, destroy
    Route::resource('courses', CourseController::class)->except(['index', 'show']);
});

// Public: index and show. Keep this AFTER the group above, otherwise
// GET /courses/create would be matched by GET /courses/{course}.
Route::resource('courses', CourseController::class)->only(['index', 'show']);

// Admin area: must be logged in AND have the admin role
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', function () {
            $users = User::withCount('courses')->orderBy('role')->orderBy('name')->get();
            return view('admin.index', compact('users'));
        })->name('index');
    });

require __DIR__ . '/auth.php';
