<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebController;
use App\Http\Controllers\ModelController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DataSetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard',[WebController::class, 'Dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // group routes for models
    Route::prefix('models')->group(function () {
        Route::get('/', [ModelController::class, 'index'])->name('models.index');
        Route::get('/create', [ModelController::class, 'create'])->name('models.create');
        Route::post('/', [ModelController::class, 'store'])->name('models.store');
        Route::get('/{model}', [ModelController::class, 'show'])->name('models.show');
        Route::get('/{model}/edit', [ModelController::class, 'edit'])->name('models.edit');
        Route::patch('/{model}', [ModelController::class, 'update'])->name('models.update');
        Route::delete('/{model}', [ModelController::class, 'destroy'])->name('models.destroy');
    });

    // group routes for datasets
    Route::prefix('datasets')->group(function () {
        Route::get('/', [DataSetController::class, 'index'])->name('datasets.index');
        Route::get('/create', [DataSetController::class, 'create'])->name('datasets.create');
        Route::post('/', [DataSetController::class, 'store'])->name('datasets.store');
        Route::get('/{dataset}', [DataSetController::class, 'show'])->name('datasets.show');
        Route::get('/{dataset}/edit', [DataSetController::class, 'edit'])->name('datasets.edit');
        Route::patch('/{dataset}', [DataSetController::class, 'update'])->name('datasets.update');
        Route::delete('/{dataset}', [DataSetController::class, 'destroy'])->name('datasets.destroy');
    });

});

require __DIR__.'/auth.php';
