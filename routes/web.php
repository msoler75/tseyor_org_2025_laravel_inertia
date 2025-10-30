<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PaginasController;
use App\Http\Controllers\ArchivosController;
use App\Http\Controllers\NodosController;
use App\Http\Controllers\NoticiasController;
use App\Http\Controllers\ComunicadosController;
use App\Http\Controllers\EntradasController;
use App\Http\Controllers\PreguntasController;
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
use App\Http\Controllers\CursosController;
use App\Http\Controllers\SalasController;
use App\Http\Controllers\RadioController;
use App\Http\Controllers\InscripcionesController;
use App\Http\Controllers\ExperienciasController;
use App\Http\Controllers\ContactarController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\PublicacionesController;
use App\Http\Controllers\InformesController;
use App\Http\Controllers\MeditacionesController;
use App\Http\Controllers\PsicografiasController;
use App\Http\Controllers\TutorialesController;
use App\Http\Controllers\NormativasController;
use App\Http\Controllers\ChatGPTController;
use App\Http\Controllers\ImagenesController;
use App\Http\Controllers\TarjetaVisitaController;
use App\Http\Controllers\EmailsController;
use App\Http\Controllers\Api\ComentariosController;
use App\Http\Controllers\Admin\JobsController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\BoletinesController;
use App\Http\Controllers\SuscriptorController;
use App\Http\Controllers\McpTokenController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\EnlaceCortoController;
use App\Http\Controllers\PWALogController;
use App\Pigmalion\SEO;
use Illuminate\Support\Facades\Cookie;
use App\Services\MuularElectronico;

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


Route::middleware(['auth', 'verified'])->group(function () {
    // ...otras rutas de perfil...
    Route::get('/user/mcp-token', [McpTokenController::class, 'show'])->name('profile.mcp-token');
    Route::post('/user/mcp-token', [McpTokenController::class, 'generate'])->name('profile.mcp-token.generate');

    // Rutas de gestión de inscripciones
    Route::prefix('inscripciones')->group(function () {
        Route::get('mis-asignaciones', [InscripcionesController::class, 'misAsignaciones'])->name('inscripciones.mis-asignaciones');
        Route::post('{inscripcion}/actualizar-estado', [InscripcionesController::class, 'actualizarEstado'])->name('inscripciones.actualizar-estado');
        Route::post('{inscripcion}/rebotar', [InscripcionesController::class, 'rebotar'])->name('inscripciones.rebotar');
        Route::post('{inscripcion}/agregar-comentario', [InscripcionesController::class, 'agregarComentario'])->name('inscripciones.agregar-comentario');
        Route::put('{inscripcion}/actualizar-notas', [InscripcionesController::class, 'actualizarNotas'])->name('inscripciones.actualizar-notas');

        // Rutas para administradores
        Route::middleware('can:admin')->group(function () {
            Route::post('asignar', [InscripcionesController::class, 'asignar'])->name('inscripciones.asignar');
            Route::post('asignar-masiva', [InscripcionesController::class, 'asignarMasiva'])->name('inscripciones.asignar-masiva');
            // Route::get('exportar', [InscripcionesController::class, 'exportar'])->name('inscripciones.exportar');
            Route::get('buscar-usuarios', [InscripcionesController::class, 'buscarUsuarios'])->name('inscripciones.buscar-usuarios');
        });
    });
});






// switch theme dark/light
Route::post('update-theme', function (Request $request) {
    $theme = $request->input('theme');
    // Cookie accesible por JS (httpOnly=false), secure solo si HTTPS, SameSite=Lax, path global
    Cookie::queue(
        Cookie::make('theme', $theme, 60 * 24 * 30, '/', null, request()->isSecure(), false, false, 'lax')
    );
    return response()->json(['success' => true]);
});

// update font size
Route::post('update-font-size', function (Request $request) {
    $fontSize = $request->input('fontSize');
    Cookie::queue(
        Cookie::make('fontSize', $fontSize, 60 * 24 * 30, '/', null, request()->isSecure(), false, false, 'lax')
    );
    return response()->json(['success' => true]);
});

Route::get('settings', [SettingsController::class, 'index'])->name('settings');
Route::get('settings/{id}', [SettingsController::class, 'show'])->name('setting');

// Rutas especiales de login para admin
Route::get('admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AuthController::class, 'login'])->name('admin.login.post');

Route::get('test', function () {
    return Inertia::render('Test', []);
})->name('test');

Route::get('biblioteca', [PaginasController::class, 'biblioteca'])->name('biblioteca');

Route::/*middleware('page-cache')->*/get('', [PaginasController::class, 'portada'])->name('portada');
Route::/*middleware('page-cache')->*/get('propuesta',
function () {
    return Inertia::render('PortadaNueva', []);
})->name('portada.propuesta');

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

Route::get('nodos/{id}', [NodosController::class, 'show'])->name('nodo');

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
Route::get('audios/{slug}', [AudiosController::class, 'show'])->where('slug', '[a-z_0-9\-\s]+')->name('audio');

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

// Rutas principales para el blog
Route::get('blog', [EntradasController::class, 'index'])->name('blog');
Route::get('blog/{slug}', [EntradasController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('blog.entrada');
Route::get('blog/{slug}/pdf', [EntradasController::class, 'pdf'])->where('slug', '[a-z0-9\-]+')->name('blog.entrada.pdf');

// Redirigimos la ruta vieja de entradas de blog a blog con redirección permanente
Route::get('entradas', function () {
    return redirect()->route('blog', [], 301);
})->name('entradas');

Route::get('entradas/{slug}', function ($slug) {
    return redirect()->route('blog.entrada', ['slug' => $slug], 301);
})->where('slug', '[a-z0-9\-]+')->name('entrada');

Route::get('entradas/{slug}/pdf', function ($slug) {
    return redirect()->route('blog.entrada.pdf', ['slug' => $slug], 301);
})->where('slug', '[a-z0-9\-]+')->name('entrada.pdf');
//

Route::get('glosario', [TerminosController::class, 'index'])->name('terminos');
Route::get('glosario/{slug}', [TerminosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('termino');
Route::get('buscarTermino', [TerminosController::class, 'search'])->name('buscar.termino');

Route::get('guias', [GuiasController::class, 'index'])->name('guias');
Route::get('guias/{slug}', [GuiasController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('guia');

Route::get('descubre', [PaginasController::class, 'descubre'])->name('descubre');

Route::get('lugares', [LugaresController::class, 'index'])->name('lugares');
Route::get('lugares/{slug}', [LugaresController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('lugar');

Route::get('eventos', [EventosController::class, 'index'])->name('eventos');
Route::get('eventos/{slug}', [EventosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('evento');


Route::get('preguntas-frecuentes', [PreguntasController::class, 'index'])->name('preguntas');
Route::get('preguntas-frecuentes/{slug}', [PreguntasController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('preguntas.seccion');


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
Route::get('radio/{emisora}', [RadioController::class, 'emisora'])->name('radio.emisora');

Route::get('inscripcion', function () {
    return Inertia::render('Cursos/NuevaInscripcion', [])
        ->withViewData(SEO::get('inscripcion'));
})->name('cursos.inscripcion.nueva');
Route::post('inscripcion/store', [InscripcionesController::class, 'store'])->name('cursos.inscripcion.store');


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
    return Inertia::render('Ong/Index', ['estatutosUrl'=>'/normativas/estatutos-ong-mundo-armonico-tseyor'])
        ->withViewData(SEO::get('ong'));
})->name('ong');
Route::get('ong/muular', function () {
    return Inertia::render('Ong/Muular', [])
        ->withViewData(SEO::get('muular'));
})->name('muular');
Route::get('muular-electronico', [MuularElectronico::class, 'redirigir']);

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

Route::get('psicografias', [PsicografiasController::class, 'index'])->name('psicografias');
Route::get('psicografias/{slug}', [PsicografiasController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('psicografia');

Route::get('tutoriales', [TutorialesController::class, 'index'])->name('tutoriales');
Route::get('tutoriales/{slug}', [TutorialesController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('tutorial');


Route::get('normativas', [NormativasController::class, 'index'])->name('normativas');
Route::get('normativas/{slug}', [NormativasController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('normativa');

Route::get('usuarios', [UsuariosController::class, 'index'])->name('usuarios');
Route::get('usuarios/_buscar/{buscar}', [UsuariosController::class, 'search'])->name('usuarios.buscar');
Route::get('usuarios/_grupos', [UsuariosController::class, 'grupos'])->name('grupos');
Route::get('usuario/_permisos', [UsuariosController::class, 'permissions'])->name('usuario.permisos');
Route::get('usuario/_saldo_muulares', [MuularElectronico::class, 'saldo'])->name('usuario.saldo');
Route::post('usuarios/_comprobar_clave', [MuularElectronico::class, 'check_password']);
Route::put('usuarios/{slug}', [UsuariosController::class, 'store'])->where('slug', '[a-z0-9\-]+')->name('usuario.guardar');
Route::get('usuarios/{slug}', [UsuariosController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('usuario');

Route::get('/sello', function () {
    return Inertia::render('Sello');
})->name('sello');


// Route::get('login/1', [DevController::class, 'loginUser1'])->name('login1');
// Route::get('login/2', [DevController::class, 'loginUser2'])->name('login2');
// Route::get('/_email', [DevController::class, 'testMail']);
Route::get('/dev/test', function () {
    return Inertia::render('Test');
});


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
Route::get('__process_audios', [JobsController::class, 'processAudios'])->name('process.audios');



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
Route::get('invitacion/reenviar/{id}', [EquiposController::class, 'resendInvitation'])->name('invitacion.reenviar');
Route::get('invitacion/cancelar/{id}', [EquiposController::class, 'cancelInvitation'])->name('invitacion.cancelar');
Route::delete('invitacion/eliminar/{id}', [EquiposController::class, 'deleteInvitation'])->name('invitacion.eliminar');
Route::get('invitacion/{token}/aceptar', [EquiposController::class, 'acceptInvitation'])->name('invitacion.aceptar');
Route::get('invitacion/{token}/declinar', [EquiposController::class, 'declineInvitation'])->name('invitacion.declinar');

// solicitudes y respuesta
Route::get('equipos/{id}/solicitudes', [EquiposController::class, 'solicitudes'])->name('equipo.solicitudes');
Route::get('equipos/{id}/solicitar', [EquiposController::class, 'solicitar'])->name('equipo.solicitar');
Route::get('solicitud/{id}/aceptar', [EquiposController::class, 'aceptarSolicitud'])->name('solicitud.aceptar');
Route::get('solicitud/{id}/denegar', [EquiposController::class, 'denegarSolicitud'])->name('solicitud.denegar');
Route::post('equipos/{id}/abandonar', [EquiposController::class, 'abandonar'])->name('equipo.abandonar');

// administración de miembros
Route::put('equipos/{idEquipo}/rol/{idUsuario}/{rol}', [EquiposController::class, 'updateMember'])->name('equipo.modificarRol');
Route::put('equipos/{idEquipo}/agregar/{idUsuario}', [EquiposController::class, 'addMember'])->name('equipo.agregar');
Route::put('equipos/{idEquipo}/remover/{idUsuario}', [EquiposController::class, 'removeMember'])->name('equipo.remover');


// chat gpt

Route::get('/chatgpt', [ChatGPTController::class, 'chat'])->name('chatgpt');

// comentarios


Route::delete('/comentarios/{id}', [ComentariosController::class, 'unpublish'])->name('comentario.despublicar');
Route::put('/comentarios/{id}', [ComentariosController::class, 'publish'])->name('comentario.publicar');
Route::get('/comentarios', [ComentariosController::class, 'index'])->name('comentarios');
Route::post('/comentarios', [ComentariosController::class, 'create'])->name('comentario.nuevo');

Route::get('phpinfo', function () {
    return phpinfo();
});

// imagenes
Route::get('imagen{ruta}', [ImagenesController::class, 'descargar'])->where(['ruta' => '(\/.+)?'])->name('imagen');
Route::get('image_size', [ImagenesController::class, 'size'])->name('imagen.tamaño');
Route::get('mockup/libro{ruta}', [ImagenesController::class, 'mockupLibro'])->where(['ruta' => '(\/.+)?'])->name('mockup.libro');

// herramientas muul

Route::get('muul/tarjeta.visita', [TarjetaVisitaController::class, 'index'])->name('tarjeta.visita');
Route::post('muul/tarjeta.visita', [TarjetaVisitaController::class, 'send'])->name('tarjeta.visita.enviar');


// administración
Route::get('emails', [EmailsController::class, 'index'])->name('emails');

// Favoritos: añadir o remover un contenido de favoritos (protegido por auth)
Route::middleware(['auth'])->group(function () {
    // Añadir a favoritos: POST /favoritos/{coleccion}/{id_ref}
    Route::post('favoritos/{coleccion}/{id_ref}', [App\Http\Controllers\FavoritosController::class, 'store'])->name('favoritos.store');

    // Eliminar favorito: DELETE /favoritos/{coleccion}/{id_ref}
    Route::delete('favoritos/{coleccion}/{id_ref}', [App\Http\Controllers\FavoritosController::class, 'destroy'])->name('favoritos.destroy');
});
Route::get('emails/{id}', [EmailsController::class, 'index'])->name('email');


Route::get('asociacion', [PaginasController::class, 'show'])->name('asociacion');


// BOLETINES ///////////////////////////////////////

// generar boletín y enviar
Route::middleware('boletin.token')->group(function () {
    Route::get('boletines/generar-contenido', [BoletinesController::class, 'generarBoletin'])->name('boletin.generar.contenido');
    Route::post('boletines/preparar', [BoletinesController::class, 'prepararBoletin'])->name('boletin.preparar');
    Route::post('boletines/enviar-pendientes', [BoletinesController::class, 'enviarBoletinesPendientes'])->name('boletin.enviar.pendientes');
});


// Ruta para el índice de boletines
Route::get('boletines', [BoletinesController::class, 'index'])->name('boletines');

// Rutas para suscripción y desuscripción
Route::post('boletines/suscribir', [SuscriptorController::class, 'suscribir'])->name('boletin.suscribir');
Route::get('boletines/desuscribir/{token}', [SuscriptorController::class, 'desuscribir'])->name('boletin.desuscribir');
Route::get('boletines/desuscripcion-confirmada', [SuscriptorController::class, 'desuscripcionConfirmada'])->name('boletin.desuscripcion.confirmada');

// Ruta para mostrar la configuración de la suscripción al boletín with token
Route::get('boletines/configurar/{token}', [SuscriptorController::class, 'mostrarConfiguracion'])->name('boletin.configurar.mostrar');

// Ruta para guardar la configuración de la suscripción al boletín with token
Route::post('boletines/configurar/{token}', [SuscriptorController::class, 'configurar'])->name('boletin.configurar');
// Ruta para obtener la suscripción al boletín
Route::get('boletines/suscripcion', [SuscriptorController::class, 'getSuscripcion'])->name('boletin.suscripcion');

// Ruta para visualizar boletines
Route::get('boletines/{id}', [BoletinesController::class, 'ver'])->name('boletin');


////////////////////////////////////////////////////////////////////////////////////////////////
// DEV/DEPLOY: Agrupadas bajo middleware de seguridad
Route::middleware(['deploy.token', 'allowed.ip'])->group(function () {
    Route::post('_sendbuild', 'App\\Http\\Controllers\\DeployController@handlePublicBuildUpload');
    Route::post('_rollback', 'App\\Http\\Controllers\\DeployController@rollbackPublicBuild');
    Route::post('_sendssr', 'App\\Http\\Controllers\\DeployController@handleSSRUpload');
    Route::post('_sendnodemodules', 'App\\Http\\Controllers\\DeployController@handleNodeModulesUpload');
});


Route::get('test/image', function () {
    return Inertia::render('test/ImageTest');
});

Route::get('test/tarjetas', function () {
    return Inertia::render('test/TarjetasTest');
});

Route::get('test/time', function () {
    return Inertia::render('test/TimeAgoTest');
});

// test para convertir archivos .docx a markdown
Route::get('test/docx',  'App\Http\Controllers\TestController@docx');
Route::get('test/docx/{num}',  'App\Http\Controllers\TestController@docxShow');

// test para ver la conversión de archivos .docx a pdf
Route::get('test/word2pdf',  'App\Http\Controllers\TestController@word2pdf');
Route::get('test/word2md',  'App\Http\Controllers\TestController@word2md');

// developing
Route::get('dev/1',  'App\Http\Controllers\DevController@dev1');
Route::get('dev/2',  'App\Http\Controllers\DevController@dev2');

////////////////////////////////////////////////////////////////////////////////////////////////


// Endpoint para MCP
// Route::post('mcp', [\App\Http\Controllers\MCPController::class, 'handle'])->name('mcp.handle');
// Route::get('mcp', [\App\Http\Controllers\MCPController::class, 'handle']);

// Endpoint para MCP (ahora gestionado por opgginc/laravel-mcp-server)
// Route::match(['get', 'post'], 'mcp', '\Opgginc\LaravelMcpServer\Http\Controllers\McpController');

// Analytics - sendBeacon para tracking al cerrar navegador
Route::post('analytics/beacon', [AnalyticsController::class, 'beacon'])->name('analytics.beacon');


// API para enlaces cortos públicos (sin auth para URLs del propio dominio)
Route::post('obtener-enlace', [EnlaceCortoController::class, 'obtener'])->name('obtener.enlace.corto');


//////////////////////////
// PWA DEBUGGING Y LOGGING
// Página de logs PWA para debugging
if(config('app.env') === 'local' || config('app.debug') ) {
    Route::get('pwa-debug', function () {
        return Inertia::render('PWALogs', []);
    })->name('pwa.debug');

    // Rutas de logging PWA (siempre activas para debugging)
    Route::post('pwa-log', [PWALogController::class, 'store']);
    Route::get('pwa-logs', [PWALogController::class, 'show']);
    Route::delete('pwa-logs', [PWALogController::class, 'clear']);
}
//////////////////////////


// Redirección de enlaces cortos (debe ir antes del fallback)
Route::get('{prefix}/{code}', [EnlaceCortoController::class, 'redirigir'])
    ->where('prefix', '^(e|d|a)$')
    ->where('code', '^[a-zA-Z0-9]+$');


// Bloquear rutas sospechosas con patrones generales (cualquier ruta que empiece con wp, templates, o sea un archivo .php sospechoso)
Route::any('{suspicious}', function () {
    abort(404);
})->where('suspicious', '^(wp.*|templates.*|.*\.php)$');

///// FINAL FALLBACK PAGE
Route::get('{ruta}', [PaginasController::class, 'show'])->where('ruta', '^(?!admin/).*')->name('pagina');


