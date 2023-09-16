<?php

use Illuminate\Support\Facades\Route;

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
    Route::crud('evento', 'EventoCrudController');
    Route::crud('inscripcion', 'InscripcionCrudController');
    Route::crud('lugar', 'LugarCrudController');
    Route::crud('normativa', 'NormativaCrudController');
    Route::crud('publicacion', 'PublicacionCrudController');
    Route::crud('solicitud', 'SolicitudCrudController');
    Route::crud('libro', 'LibroCrudController');
    Route::crud('sala', 'SalaCrudController');

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

    Route::get('search/{model}', 'SearchModelController@index');

    Route::crud('setting', 'SettingCrudController');
    Route::crud('radio-item', 'RadioItemCrudController');
    Route::crud('pagina', 'PaginaCrudController');
    Route::crud('termino', 'TerminoCrudController');

    Route::post('termino/importar/crear', 'TerminoCrudController@importCreate');

    Route::crud('video', 'VideoCrudController');
}); // this should be the absolute last line of this file
