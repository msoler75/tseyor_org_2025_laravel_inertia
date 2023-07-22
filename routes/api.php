<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArchivosController;
use App\Http\Controllers\EquiposController;
use App\Http\Controllers\Api\ComentariosController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // ni idea
});



Route::get('/comentarios', [ComentariosController::class, 'index'])->name('comentarios');

Route::middleware(['web'])->group(function () {
    Route::post('/comentarios', [ComentariosController::class, 'create'])->name('comentario.nuevo');

    // archivos
    Route::post('/files/upload/file', [ArchivosController::class, 'uploadFile'])->name('files.upload.file');
    Route::post('/files/upload/image', [ArchivosController::class, 'uploadImage'])->name('files.upload.image');
    Route::post('/files/rename', [ArchivosController::class, 'rename'])->name('files.rename');
    Route::post('/files/move', [ArchivosController::class, 'move'])->name('files.move');
    Route::post('/files/copy', [ArchivosController::class, 'copy'])->name('files.copy');
    Route::put('/files/mkdir', [ArchivosController::class, 'makeDir'])->name('files.mkdir');
    Route::delete('/files{ruta}', [ArchivosController::class, 'delete'])->where(['ruta' => '(\/.+)?'])->name('files.delete');
})
    ->middleware(['auth']);



/*
Route::middleware(['web'])->group(function () {
Route::put('/equipos/{idEquipo}/agregar/{idUsuario}', [EquiposController::class, 'addMember'])->name('equipo.agregar');
Route::put('/equipos/{idEquipo}/remover/{idUsuario}', [EquiposController::class, 'removeMember'])->name('equipo.remover');
Route::post('/equipos/{id}', [EquiposController::class, 'update'])->name('equipo.modificar');
Route::put('/equipos/{idEquipo}/update/{idUsuario}/{rol}', [EquiposController::class, 'updateMember'])->name('equipo.modificarRol');
});
*/
