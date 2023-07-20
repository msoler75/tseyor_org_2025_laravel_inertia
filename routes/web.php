<?php

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ArchivosController;
use App\Http\Controllers\NoticiasController;
use App\Http\Controllers\ComunicadosController;
use App\Http\Controllers\EntradasController;
use App\Http\Controllers\GuiasController;
use App\Http\Controllers\LugaresController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\EquiposController;
use App\Http\Controllers\LibrosController;
use App\Http\Controllers\CentrosController;
use App\Http\Controllers\ContactosController;
use App\Http\Controllers\AudiosController;
use App\Http\Controllers\NovedadesController;
use App\Http\Controllers\PortadaController;
use App\Http\Controllers\CursosController;
use App\Http\Controllers\RadioController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\UsuariosController;
use App\Pigmalion\SEO;


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


Route::get('/', [PortadaController::class, 'index'])->name('portada');

Route::get('/novedades', [NovedadesController::class, 'index'])->name('novedades');

Route::get('/archivos', [ArchivosController::class, 'index'])->name('archivos0');
Route::get('/archivos{ruta}', [ArchivosController::class, 'index'])->where(['ruta' => '(\/.+)?'])->name('archivos');

Route::get('/storage/{ruta}', [ArchivosController::class, 'download'])->where(['ruta' => '(\/.+)?'])->name('storage');

Route::get('/audios', [AudiosController::class, 'index'])->name('audios');
Route::get('/audios/{id}', [AudiosController::class, 'show'])->name('audio');

Route::get('/videos', function () {
    return Inertia::render('Videos', [])
        ->withViewData(SEO::get('videos'));
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

Route::get('/enciclopedia/guias', [GuiasController::class, 'index'])->name('enciclopedia.guias');
Route::get('/enciclopedia/guias/{id}', [GuiasController::class, 'show'])->name('enciclopedia.guia');

Route::get('/enciclopedia/lugares', [LugaresController::class, 'index'])->name('enciclopedia.lugares');
Route::get('/enciclopedia/lugares/{id}', [LugaresController::class, 'show'])->name('enciclopedia.lugar');

Route::get('/eventos', [EventosController::class, 'index'])->name('eventos');
Route::get('/eventos/{id}', [EventosController::class, 'show'])->name('evento');

Route::get('/equipos', [EquiposController::class, 'index'])->name('equipos');
Route::get('/equipos/nuevo', function() {
    return Inertia::render('Equipos/Nuevo', []);
})->name('equipo.crear');
Route::post('/equipos', [EquiposController::class, 'store'])->name('equipo.nuevo');
Route::get('/equipos/{id}', [EquiposController::class, 'show'])->name('equipo');


Route::post('/invitar/{idEquipo}', [InvitacionesController::class, 'invite'])->name('invitacion.aceptar');
Route::get('/invitacion/{token}/aceptar', [InvitacionesController::class, 'acceptInvitation'])->name('invitacion.aceptar');
Route::get('/invitacion/{token}/declinar', [InvitacionesController::class, 'declineInvitation'])->name('invitacion.declinar');


Route::get('/donde-estamos', [ContactosController::class, 'index'])->name('contactos');
Route::get('/contactos/{id}', [ContactosController::class, 'show'])->name('contacto');

Route::get('/centros', [CentrosController::class, 'index'])->name('centros');
Route::get('/centros/{id}', [CentrosController::class, 'show'])->name('centro');

Route::get('/quienes-somos', function () {
    return Inertia::render('Presentacion/QuienesSomos', [])
        ->withViewData(SEO::get('quienes-somos'));
})->name('quienes-somos');

Route::get('/origenes-de-tseyor', function () {
    return Inertia::render('Presentacion/OrigenesTseyor', [])
        ->withViewData(SEO::get('origenes-de-tseyor'));
})->name('origenes-de-tseyor');

Route::get('/filosofia', function () {
    return Inertia::render('Presentacion/Filosofia', [])
        ->withViewData(SEO::get('filosofia'));
})->name('filosofia');

Route::get('/cursos', [CursosController::class, 'index'])->name('cursos');

Route::get('/radio', [RadioController::class, 'index'])->name('radio');

Route::get('/cursos/inscripcion', function () {
    return Inertia::render('Cursos/Inscripcion', [])
        ->withViewData(SEO::get('cursos.inscripcion'));
})->name('cursos.inscripcion');
Route::post('/inscripcion', [InscripcionController::class, 'store'])->name('inscripcion.store');


Route::get('/ong', function () {
    return Inertia::render('Ong/Index', [])
        ->withViewData(SEO::get('ong'));
})->name('ong');
Route::get('/ong/muular', function () {
    return Inertia::render('Ong/Muular', [])
        ->withViewData(SEO::get('muular'));
})->name('muular');

Route::get('/utg', [EquiposController::class, 'index_utg'])->name('utg');
Route::get(
    '/utg/departamentos',
    function () {
        return Redirect::to('/equipos?categoria=utg');
    }
)->name('utg.departamentos');
Route::get('/utg/departamentos/{id}', [EquiposController::class, 'show'])->name('utg.departamento');

Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios');
Route::get('/usuarios/_buscar/{buscar}', [UsuariosController::class, 'search'])->name('usuarios.buscar');
Route::get('/usuarios/{id}', [UsuariosController::class, 'show'])->name('usuario');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
