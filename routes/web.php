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
use App\Http\Controllers\TerminosController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\EquiposController;
use App\Http\Controllers\LibrosController;
use App\Http\Controllers\CentrosController;
use App\Http\Controllers\ContactosController;
use App\Http\Controllers\AudiosController;
use App\Http\Controllers\VideosController;
use App\Http\Controllers\ContenidosController;
use App\Http\Controllers\PortadaController;
use App\Http\Controllers\CursosController;
use App\Http\Controllers\SalasController;
use App\Http\Controllers\RadioController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\ExperienciasController;
use App\Http\Controllers\ContactarController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\PublicacionesController;
use App\Http\Controllers\InformesController;
use App\Http\Controllers\MeditacionesController;
use App\Http\Controllers\TutorialesController;
use App\Http\Controllers\NormativasController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\ChatGPTController;
use App\Http\Controllers\ImagenesController;
use App\Http\Controllers\TarjetaVisitaController;
use App\Http\Controllers\EmailsController;
use App\Http\Controllers\DevController;
use App\Http\Controllers\Api\ComentariosController;
use App\Pigmalion\SEO;



// a borrar:

Route::get('glosario/parse', [TerminosController::class, 'parse'])->name('parse');


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







Route::get('settings', [SettingsController::class, 'index'])->name('settings');
Route::get('settings/{id}', [SettingsController::class, 'show'])->name('setting');

Route::get('test', function () {
    return Inertia::render('Test', []);
})->name('test');

Route::get('biblioteca', function () { return Inertia::render('Biblioteca', []) ->withViewData(SEO::get('novedades')); })->name('biblioteca');

Route::get('', [PortadaController::class, 'index'])->name('portada');

Route::get('novedades', [ContenidosController::class, 'index'])->name('novedades');
Route::get('buscar', [ContenidosController::class, 'search'])->name('buscar');
Route::post('buscar', [ContenidosController::class, 'searchStore'])->name('busqueda.guardar');

Route::get('archivos', [ArchivosController::class, 'archivos'])->name('archivos0');
Route::get('archivos_info', [ArchivosController::class, 'info'])->name('archivos.info');
Route::get('archivos_buscar', [ArchivosController::class, 'buscar'])->name('archivos.buscar');
Route::get('archivos{ruta}', [ArchivosController::class, 'archivos'])->where(['ruta' => '(\/.+)?'])->name('archivos');

Route::get('filemanager{ruta}', [ArchivosController::class, 'filemanager'])->where(['ruta' => '(\/.*)?'])->name('filemanager');

Route::get('almacen{ruta}', [ArchivosController::class, 'descargar'])->where(['ruta' => '(\/.+)?'])->name('storage');
Route::get('storage{ruta}', [ArchivosController::class, 'descargar'])->where(['ruta' => '(\/.+)?'])->name('storage.classic');
Route::get('mis_archivos', [ArchivosController::class, 'archivos'])->name('mis_archivos');
Route::get('archivos_raiz', [ArchivosController::class, 'archivos'])->name('archivos_raiz');

 // manejo de archivos
 Route::post('files/upload/file', [ArchivosController::class, 'uploadFile'])->name('files.upload.file');
 Route::post('files/upload/image', [ArchivosController::class, 'uploadImage'])->name('files.upload.image');
 Route::post('files/rename', [ArchivosController::class, 'rename'])->name('files.rename');
 Route::post('files/update', [ArchivosController::class, 'update'])->name('files.update');
 Route::post('files/move', [ArchivosController::class, 'move'])->name('files.move');
 Route::post('files/copy', [ArchivosController::class, 'copy'])->name('files.copy');
 Route::put('files/mkdir', [ArchivosController::class, 'makeDir'])->name('files.mkdir');
 Route::delete('files{ruta}', [ArchivosController::class, 'delete'])->where(['ruta' => '(\/.+)?'])->name('files.delete');



Route::get('audios', [AudiosController::class, 'index'])->name('audios');
Route::get('audios/{slug}', [AudiosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('audio');

Route::get('videos', [VideosController::class, 'index'])->name('videos');
Route::get('videos/{slug}', [VideosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('video');


Route::get('noticias', [NoticiasController::class, 'index'])->name('noticias');
Route::get('noticias/{slug}', [NoticiasController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('noticia');

Route::get('comunicados', [ComunicadosController::class, 'index'])->name('comunicados');
Route::get('comunicados/{slug}', [ComunicadosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('comunicado');
// Route::get('archivo/comunicados', [ComunicadosController::class, 'archive'])->name('archivo.comunicados');
Route::get('comunicados/{slug}/pdf', [ComunicadosController::class, 'pdf'])->where('slug', '[a-z0-9\-]+')->name('comunicado.pdf');

Route::get('libros', [LibrosController::class, 'index'])->name('libros');
Route::get('libros/{slug}', [LibrosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('libro');

Route::get('entradas', [EntradasController::class, 'index'])->name('entradas');
Route::get('entradas/{slug}', [EntradasController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('entrada');

Route::get('glosario', [TerminosController::class, 'index'])->name('terminos');
Route::get('glosario/{slug}', [TerminosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('termino');

Route::get('guias', [GuiasController::class, 'index'])->name('guias');
Route::get('guias/{slug}', [GuiasController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('guia');

Route::get('lugares', [LugaresController::class, 'index'])->name('lugares');
Route::get('lugares/{slug}', [LugaresController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('lugar');

Route::get('eventos', [EventosController::class, 'index'])->name('eventos');
Route::get('eventos/{slug}', [EventosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('evento');


Route::get('salas', [SalasController::class, 'index'])->name('salas');
Route::get('salas/{slug}', [SalasController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('sala');


Route::get('donde-estamos', [ContactosController::class, 'index'])->name('contactos');
Route::get('contactos/{slug}', [ContactosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('contacto');

Route::get('centros', [CentrosController::class, 'index'])->name('centros');
Route::get('centros/{slug}', [CentrosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('centro');

Route::get('quienes-somos', function () {
    return Inertia::render('Presentacion/QuienesSomos', [])
        ->withViewData(SEO::get('quienes-somos'));
})->name('quienes-somos');

Route::get('origenes-de-tseyor', function () {
    return Inertia::render('Presentacion/OrigenesTseyor', [])
        ->withViewData(SEO::get('origenes-de-tseyor'));
})->name('origenes-de-tseyor');

Route::get('filosofia', function () {
    return Inertia::render('Presentacion/Filosofia', [])
        ->withViewData(SEO::get('filosofia'));
})->name('filosofia');

Route::get('cursos', [CursosController::class, 'index'])->name('cursos');

Route::get('radio', [RadioController::class, 'index'])->name('radio');

Route::get('inscripcion', function () {
    return Inertia::render('Cursos/NuevaInscripcion', [])
        ->withViewData(SEO::get('inscripcion'));
})->name('cursos.inscripcion.nueva');
Route::post('inscripcion/store', [InscripcionController::class, 'store'])->name('cursos.inscripcion.store');


Route::post('experiencia/store', [ExperienciasController::class, 'store']);
Route::get('experiencias', [ExperienciasController::class, 'index'])->name('experiencias');
Route::get('experiencias/nueva', [ExperienciasController::class, 'nueva'])->name('experiencia.nueva');
Route::get('experiencias/{id}', [ExperienciasController::class, 'show'])->name('experiencia');

Route::get('contactar', function () {
    return Inertia::render('Contactar', [])
        ->withViewData(SEO::get('contactar'));
})->name('contactar');
Route::post('contactar/enviar', [ContactarController::class, 'send'])->name('contactar.send');
Route::get('contactar/test', [ContactarController::class, 'test'])->name('contactar.test');

Route::get('ong', function () {
    return Inertia::render('Ong/Index', [])
        ->withViewData(SEO::get('ong'));
})->name('ong');
Route::get('ong/muular', function () {
    return Inertia::render('Ong/Muular', [])
        ->withViewData(SEO::get('muular'));
})->name('muular');

Route::get('utg', [EquiposController::class, 'index_utg'])->name('utg');
Route::get(
    '/utg/departamentos',
    function () {
        return Redirect::to('equipos?categoria=utg');
    }
)->name('utg.departamentos');
Route::get('utg/departamentos/{slug}', [EquiposController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('utg.departamento');

Route::get('publicaciones', [PublicacionesController::class, 'index'])->name('publicaciones');
Route::get('publicaciones/{slug}', [PublicacionesController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('publicacion');

Route::get('meditaciones', [MeditacionesController::class, 'index'])->name('meditaciones');
Route::get('meditaciones/{slug}', [MeditacionesController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('meditacion');

Route::get('tutoriales', [TutorialesController::class, 'index'])->name('tutoriales');
Route::get('tutoriales/{slug}', [TutorialesController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('tutorial');


Route::get('normativas', [NormativasController::class, 'index'])->name('normativas');
Route::get('normativas/{slug}', [NormativasController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('normativa');

Route::get('usuarios', [UsuariosController::class, 'index'])->name('usuarios');
Route::get('usuarios/_buscar/{buscar}', [UsuariosController::class, 'search'])->name('usuarios.buscar');
Route::get('usuarios/_permisos', [UsuariosController::class, 'permissions'])->name('usuario.permisos');
Route::get('usuarios/_grupos', [UsuariosController::class, 'grupos'])->name('grupos');
Route::put('usuarios/{slug}', [UsuariosController::class, 'store'])->where('slug', '[a-z0-9\-]+')->name('usuario.guardar');
Route::get('usuarios/{slug}', [UsuariosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('usuario');

Route::get('login/1', [DevController::class, 'loginUser1'])->name('login1');
Route::get('login/2', [DevController::class, 'loginUser2'])->name('login2');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

// queue  batch
Route::get('__process_jobs', [WorkerController::class, 'process'])->name('process.jobs');



// EQUIPOS

Route::get('equipos', [EquiposController::class, 'index'])->name('equipos');
Route::get('equipos/nuevo', function () {
    return Inertia::render('Equipos/Nuevo', []);
})->name('equipo.nuevo');
Route::post('equipo/store', [EquiposController::class, 'store']);
Route::get('equipos/{slug}', [EquiposController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('equipo');
Route::post('equipos/{id}', [EquiposController::class, 'update'])->name('equipo.modificar');

// informes de equipo

Route::get('informes', [InformesController::class, 'index'])->name('informes');
Route::get('informes/{id}', [InformesController::class, 'show'])->name('informe');
Route::get('equipos/{slug}/informes', [InformesController::class, 'equipo'])->where('slug', '[a-z0-9\-]+')->name('equipo.informes');

// invitaciones y respuesta
Route::get('equipos/{id}/invitaciones', [EquiposController::class, 'invitations'])->name('equipo.invitaciones');
Route::post('invitar/{idEquipo}', [EquiposController::class, 'invite'])->name('invitar');
Route::get('invitacion/{token}/aceptar', [EquiposController::class, 'acceptInvitation'])->name('invitacion.aceptar');
Route::get('invitacion/{token}/declinar', [EquiposController::class, 'declineInvitation'])->name('invitacion.declinar');

// solicitudes y respuesta
Route::get('equipos/{id}/solicitudes', [EquiposController::class, 'solicitudes'])->name('equipo.solicitudes');
Route::get('equipos/{id}/solicitar', [EquiposController::class, 'solicitar'])->name('equipo.solicitar');
Route::get('solicitud/{id}/aceptar', [EquiposController::class, 'aceptarSolicitud'])->name('solicitud.aceptar');
Route::get('solicitud/{id}/denegar', [EquiposController::class, 'denegarSolicitud'])->name('solicitud.denegar');
Route::post('equipos/{id}/abandonar', [EquiposController::class, 'abandonar'])->name('equipo.abandonar');

// administración de miembros
Route::put('equipos/{idEquipo}/{idUsuario}/agregar', [EquiposController::class, 'addMember'])->name('equipo.agregar');
Route::put('equipos/{idEquipo}/{idUsuario}/remover', [EquiposController::class, 'removeMember'])->name('equipo.remover');
Route::put('equipos/{idEquipo}/update/{idUsuario}/{rol}', [EquiposController::class, 'updateMember'])->name('equipo.modificarRol');


// chat gpt

Route::get('/chatgpt', [ChatGPTController::class, 'chat'])->name('chatgpt');

// comentarios


Route::get('/comentarios', [ComentariosController::class, 'index'])->name('comentarios');
Route::post('/comentarios', [ComentariosController::class, 'create'])->name('comentario.nuevo');

Route::get('phpinfo', function () {
    return phpinfo();
});

// imagenes
Route::get('imagen{ruta}', [ImagenesController::class, 'descargar'])->where(['ruta' => '(\/.+)?'])->name('imagen');
Route::get('image_size', [ImagenesController::class, 'size'])->name('imagen.tamaño');

// herramientas muul

Route::get('muul/tarjeta.visita', [TarjetaVisitaController::class, 'index'])->name('tarjeta.visita');
Route::post('muul/tarjeta.visita', [TarjetaVisitaController::class, 'send'])->name('tarjeta.visita.enviar');


// administración
Route::get('emails', [EmailsController::class, 'index'])->name('emails');
Route::get('emails/{id}', [EmailsController::class, 'index'])->name('email');


Route::get('asociacion', 'App\Http\Controllers\PaginasController@show')->name('asociacion');




Route::get('test/tiptap', function () {
    return Inertia::render('test/TipTapTest');
});

Route::get('test/editor', function () {
    return Inertia::render('test/EditorJsTest');
});

Route::get('test/image', function () {
    return Inertia::render('test/imageTest');
});

// test para convertir archivos .docx a markdown
Route::get('test/docx',  'App\Http\Controllers\TestController@docx');
Route::get('test/docx/{num}',  'App\Http\Controllers\TestController@docxShow');

// test para ver la conversión de archivos .docx a pdf
Route::get('test/word2pdf',  'App\Http\Controllers\TestController@word2pdf');


Route::get('{ruta}', 'App\Http\Controllers\PaginasController@show')->where('ruta', '[a-z0-9\-\/\.]+')->name('pagina');


/* Route::fallback(function () {
    return app()->call('App\Http\Controllers\PaginasController@index');
}); */
