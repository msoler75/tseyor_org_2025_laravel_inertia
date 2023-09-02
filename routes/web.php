<?php

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SettingsController;
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
use App\Http\Controllers\ContenidosController;
use App\Http\Controllers\PortadaController;
use App\Http\Controllers\CursosController;
use App\Http\Controllers\RadioController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\DevController;
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

Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
Route::get('/settings/{id}', [SettingsController::class, 'show'])->name('setting');

Route::get('/test', function () {
    return Inertia::render('Test', []);
})->name('test');

Route::get('/', [PortadaController::class, 'index'])->name('portada');

Route::get('/novedades', [ContenidosController::class, 'index'])->name('novedades');
Route::get('/buscar', [ContenidosController::class, 'search'])->name('buscar');

Route::get('/archivos', [ArchivosController::class, 'archivos'])->name('archivos0');
Route::get('/archivos{ruta}', [ArchivosController::class, 'archivos'])->where(['ruta' => '(\/.+)?'])->name('archivos');

Route::get('/filemanager{ruta}', [ArchivosController::class, 'filemanager'])->where(['ruta' => '(\/.*)?'])->name('filemanager');

Route::get('/storage/{ruta}', [ArchivosController::class, 'storage'])->where(['ruta' => '(\/.+)?'])->name('storage');

Route::get('/audios', [AudiosController::class, 'index'])->name('audios');
Route::get('/audios/{slug}', [AudiosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('audio');

Route::get('/videos', function () {
    return Inertia::render('Videos', [])
        ->withViewData(SEO::get('videos'));
})->name('videos');


Route::get('/noticias', [NoticiasController::class, 'index'])->name('noticias');
Route::get('/noticias/{slug}', [NoticiasController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('noticia');

Route::get('/comunicados', [ComunicadosController::class, 'index'])->name('comunicados');
Route::get('/comunicados/{slug}', [ComunicadosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('comunicado');
Route::get('/archivo/comunicados', [ComunicadosController::class, 'archive'])->name('archivo.comunicados');

Route::get('/libros', [LibrosController::class, 'index'])->name('libros');
Route::get('/libros/{slug}', [LibrosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('libro');


Route::get('/entradas', [EntradasController::class, 'index'])->name('entradas');
Route::get('/entradas/{slug}', [EntradasController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('entrada');

Route::get('/enciclopedia/guias', [GuiasController::class, 'index'])->name('enciclopedia.guias');
Route::get('/enciclopedia/guias/{slug}', [GuiasController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('enciclopedia.guia');

Route::get('/enciclopedia/lugares', [LugaresController::class, 'index'])->name('enciclopedia.lugares');
Route::get('/enciclopedia/lugares/{slug}', [LugaresController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('enciclopedia.lugar');

Route::get('/eventos', [EventosController::class, 'index'])->name('eventos');
Route::get('/eventos/{slug}', [EventosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('evento');


Route::get('/donde-estamos', [ContactosController::class, 'index'])->name('contactos');
Route::get('/contactos/{slug}', [ContactosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('contacto');

Route::get('/centros', [CentrosController::class, 'index'])->name('centros');
Route::get('/centros/{slug}', [CentrosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('centro');

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
Route::post('/cursos/inscripcion', [InscripcionController::class, 'store'])->name('cursos.inscripcion.store');


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
Route::get('/utg/departamentos/{slug}', [EquiposController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('utg.departamento');

Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios');
Route::get('/usuarios/_buscar/{buscar}', [UsuariosController::class, 'search'])->name('usuarios.buscar');
Route::get('/usuarios/_permisos', [UsuariosController::class, 'permissions'])->name('usuario.permisos');
Route::get('/usuarios/{slug}', [UsuariosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('usuario');

Route::get('/login/1', [DevController::class, 'loginUser1'])->name('login1');
Route::get('/login/2', [DevController::class, 'loginUser2'])->name('login2');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});




// EQUIPOS

Route::get('/equipos', [EquiposController::class, 'index'])->name('equipos');
Route::get('/equipos/nuevo', function() {
    return Inertia::render('Equipos/Nuevo', []);
})->name('equipo.crear');
Route::post('/equipos', [EquiposController::class, 'store'])->name('equipo.nuevo');
Route::get('/equipos/{slug}', [EquiposController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('equipo');

// invitaciones y respuesta
Route::post('/invitar/{idEquipo}', [EquiposController::class, 'invite'])->name('invitar');
Route::get('/invitacion/{token}/aceptar', [EquiposController::class, 'acceptInvitation'])->name('invitacion.aceptar');
Route::get('/invitacion/{token}/declinar', [EquiposController::class, 'declineInvitation'])->name('invitacion.declinar');

// solicitudes y respuesta
Route::get('/equipos/{id}/solicitudes', [EquiposController::class, 'solicitudes'])->name('equipo.solicitudes');
Route::get('/equipos/{id}/solicitar', [EquiposController::class, 'solicitar'])->name('equipo.solicitar');
Route::get('/solicitud/{id}/aceptar', [EquiposController::class, 'aceptarSolicitud'])->name('solicitud.aceptar');
Route::get('/solicitud/{id}/denegar', [EquiposController::class, 'denegarSolicitud'])->name('solicitud.denegar');

// administración de miembros
Route::put('/equipos/{idEquipo}/{idUsuario}/agregar', [EquiposController::class, 'addMember'])->name('equipo.agregar');
Route::put('/equipos/{idEquipo}/{idUsuario}/remover', [EquiposController::class, 'removeMember'])->name('equipo.remover');
Route::post('/equipos/{id}', [EquiposController::class, 'update'])->name('equipo.modificar');
Route::put('/equipos/{idEquipo}/update/{idUsuario}/{rol}', [EquiposController::class, 'updateMember'])->name('equipo.modificarRol');




Route::get('/phpinfo', function () {
    return phpinfo();
});
