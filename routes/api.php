<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FilesController;

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


Route::post('/files/upload/file', [FilesController::class, 'uploadFile'])->name('files.upload.file');
Route::post('/files/upload/image', [FilesController::class, 'uploadImage'])->name('files.upload.image');
Route::post('/files/rename', [FilesController::class, 'rename'])->name('files.rename');
Route::post('/files/move', [FilesController::class, 'move'])->name('files.move');
Route::post('/files/copy', [FilesController::class, 'copy'])->name('files.copy');
Route::put('/files/mkdir', [FilesController::class, 'createFolder'])->name('files.mkdir');
Route::delete('/files{ruta}', [FilesController::class, 'delete'])->where(['ruta' => '(\/.+)?'])->name('files.delete');
