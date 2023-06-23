<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ArchivosController;
use App\Http\Controllers\NoticiasController;
use App\Http\Controllers\ComunicadosController;
use App\Http\Controllers\EntradasController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\LibrosController;
use App\Http\Controllers\CentrosController;
use App\Http\Controllers\ContactosController;
use App\Http\Controllers\AudiosController;
use App\Http\Controllers\NovedadesController;
use App\Http\Controllers\CursosController;

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

Route::get('/novedades', [NovedadesController::class, 'index'])->name('novedades');

Route::get('/archivos{ruta}', [ArchivosController::class, 'index'])->where(['ruta' => '(\/.+)?'])->name('archivos');

Route::get('/audios', [AudiosController::class, 'index'])->name('audios');
Route::get('/audios/{id}', [AudiosController::class, 'show'])->name('audio');

Route::get('/videos', function () {
    return Inertia::render('Videos', []);
})->name('videos');


Route::get('/noticias', [NoticiasController::class, 'index'])->name('noticias');
Route::get('/noticias/{id}', [NoticiasController::class, 'show'])->name('noticia');

Route::get('/comunicados', [ComunicadosController::class, 'index'])->name('comunicados');
Route::get('/comunicados/{id}', [ComunicadosController::class, 'show'])->name('comunicado');
Route::get('/archivo/comunicados', [ComunicadosController::class, 'archive'])->name('archivo.comunicados');

Route::get('/libros', [LibrosController::class, 'index'])->name('libros');
Route::get('/libros/{id}', [LibrosController::class, 'show'])->name('libro');


Route::get('/entradas', [EntradasController::class, 'index'])->name('entradas');
Route::get('/entradas/{id}', [EntradasController::class, 'show'])->name('entrada');

Route::get('/eventos', [EventosController::class, 'index'])->name('eventos');
Route::get('/eventos/{id}', [EventosController::class, 'show'])->name('evento');


Route::get('/donde-estamos', [ContactosController::class, 'index'])->name('contactos');
Route::get('/contactos/{id}', [ContactosController::class, 'show'])->name('contacto');

Route::get('/centros', [CentrosController::class, 'index'])->name('centros');
Route::get('/centros/{id}', [CentrosController::class, 'show'])->name('centro');

Route::get('/quienes-somos', function () {
    return Inertia::render('QuienesSomos', []);
})->name('quienes-somos');

Route::get('/cursos', [CursosController::class, 'index'])->name('cursos');

Route::get('/cursos/inscripcion', function () {
    return Inertia::render('Cursos/Inscripcion', []);
})->name('cursos.inscripcion');
Route::post('/inscripcion', 'InscripcionController@store')->name('inscripcion.store');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
