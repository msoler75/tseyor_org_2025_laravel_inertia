<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ArchivosController;
use App\Http\Controllers\NoticiasController;
use App\Http\Controllers\ComunicadosController;

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

Route::get('/archivos{ruta}', [ArchivosController::class, 'index'])->where(['ruta' => '(\/.+)?'])->name('archivos');


Route::get('/noticias', [NoticiasController::class, 'index'])->name('noticias');
Route::get('/noticias/{id}', [NoticiasController::class, 'show'])->name('noticia');

Route::get('/comunicados', [ComunicadosController::class, 'index'])->name('comunicados');
Route::get('/comunicados/{id}', [ComunicadosController::class, 'show'])->name('comunicado');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});


