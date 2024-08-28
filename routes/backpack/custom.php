<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Admin\CommandController;


// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    // Route::get('dashboard', 'Backpack\dashboard')->name('dashboard');
    Route::get('logout', function() {
        auth()->logout();
        return redirect('/');
    });
    Route::post('user/new-password', [AdminController::class, 'newPassword']);
    Route::crud('user', 'UserCrudController');
    Route::crud('comunicado', 'ComunicadoCrudController');
    Route::crud('guia', 'GuiaCrudController');
    Route::crud('nodo', 'NodoCrudController');
    Route::crud('acl', 'AclCrudController');
    Route::crud('grupo', 'GrupoCrudController');
    Route::crud('equipo', 'EquipoCrudController');
    Route::crud('noticia', 'NoticiaCrudController');
    Route::crud('entrada', 'EntradaCrudController');
    Route::crud('audio', 'AudioCrudController');
    Route::crud('contacto', 'ContactoCrudController');
    Route::crud('centro', 'CentroCrudController');
    Route::crud('comentario', 'ComentarioCrudController');
    Route::crud('email', 'EmailCrudController');
    Route::crud('evento', 'EventoCrudController');
    Route::crud('inscripcion', 'InscripcionCrudController');
    Route::crud('meditacion', 'MeditacionCrudController');
    Route::crud('publicacion', 'PublicacionCrudController');
    Route::crud('informe', 'InformeCrudController');
    Route::crud('lugar', 'LugarCrudController');
    Route::crud('normativa', 'NormativaCrudController');
    Route::crud('solicitud', 'SolicitudCrudController');
    Route::crud('libro', 'LibroCrudController');
    Route::crud('sala', 'SalaCrudController');
    Route::crud('setting', 'SettingCrudController');
    Route::crud('revision', 'RevisionCrudController');
    Route::crud('radio-item', 'RadioItemCrudController');
    Route::crud('pagina', 'PaginaCrudController');
    Route::crud('termino', 'TerminoCrudController');
    Route::crud('video', 'VideoCrudController');
    Route::crud('tutorial', 'TutorialCrudController');
    Route::crud('experiencia', 'ExperienciaCrudController');
    Route::crud('job', 'JobCrudController');
    Route::crud('job-failed', 'JobFailedCrudController');

    Route::post('comunicado/importar/crear', 'ComunicadoCrudController@importCreate');
    Route::post('comunicado/importar/actualizar/{id}', 'ComunicadoCrudController@importUpdate');

    Route::post('noticia/importar/crear', 'NoticiaCrudController@importCreate');
    Route::post('noticia/importar/actualizar/{id}', 'NoticiaCrudController@importUpdate');

    Route::post('evento/importar/crear', 'EventoCrudController@importCreate');
    Route::post('evento/importar/actualizar/{id}', 'EventoCrudController@importUpdate');

    Route::post('entrada/importar/crear', 'EntradaCrudController@importCreate');
    Route::post('entrada/importar/actualizar/{id}', 'EntradaCrudController@importUpdate');

    Route::post('normativa/importar/crear', 'NormativaCrudController@importCreate');
    Route::post('normativa/importar/actualizar/{id}', 'NormativaCrudController@importUpdate');

    Route::post('publicacion/importar/crear', 'PublicacionCrudController@importCreate');
    Route::post('publicacion/importar/actualizar/{id}', 'PublicacionCrudController@importUpdate');

    Route::post('informe/importar/crear', 'InformeCrudController@importCreate');
    Route::post('informe/importar/actualizar/{id}', 'InformeCrudController@importUpdate');

    Route::post('tutorial/importar/crear', 'TutorialCrudController@importCreate');
    Route::post('tutorial/importar/actualizar/{id}', 'TutorialCrudController@importUpdate');

    Route::post('meditacion/importar/crear', 'MeditacionCrudController@importCreate');
    Route::post('meditacion/importar/actualizar/{id}', 'MeditacionCrudController@importUpdate');

    Route::post('termino/importar/crear', 'TerminoCrudController@importCreate');
    Route::get('termino/importando/paso1', 'TerminoCrudController@importando1');
    Route::get('termino/importando/paso2', 'TerminoCrudController@importando2');
    Route::get('termino/importando/test', 'TerminoCrudController@test');


    Route::get('search/{model}', 'SearchModelController@index');


    Route::get('worker/check', [WorkerController::class, 'checkWorkerStatus'])->name('worker.check');
    Route::get('worker/start', [WorkerController::class, 'startWorker'])->name('worker.start');
    Route::get('worker/stop', [WorkerController::class, 'stopWorker'])->name('worker.stop');
    Route::get('worker/restart', [WorkerController::class, 'restartWorker'])->name('worker.restart');

    Route::get('command', function () {
        return view('admin.command');
    } );
    Route::get('command/{command}', [CommandController::class, 'runCommand'])->name('command.run');


    Route::get('archivos', function () {
        return view('admin.archivos');
    } );
    Route::get('logs', function () {
        return view('admin.logs');
    } );


    Route::post('loginAs/{idUser}', [AdminController::class, 'loginAs'])->name('admin.loginAs');

    Route::get('getlog/{log}', [AdminController::class, 'getLog'] );
    Route::get('list-images{ruta}', [AdminController::class, 'listImages'] )->where(['ruta' => '(\/.+)?'])->name('admin.list-images');
    Route::get('dashboard', [AdminController::class, 'dashboard'] );
    Route::get('', [AdminController::class, 'dashboard'] );

    // administración de tareas
    Route::get('jobs/retry-failed-jobs', 'JobsController@retryFailedJobs');
    Route::get('jobs/retry-job/{id}', 'JobsController@retryJob');
    Route::get('jobs/flush', 'JobsController@flushJobs');
    Route::get('jobs/detect-audios-to-process', 'JobsController@detectAudiosToProcess');
    // queue  batch

}); // this should be the absolute last line of this file
