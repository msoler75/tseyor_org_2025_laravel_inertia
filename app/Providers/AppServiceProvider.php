<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
/*use App\Models\Centro;
use App\Models\Contacto;
use App\Models\Comunicado;
use App\Models\Entrada;
use App\Models\Noticia;
use App\Models\Libro;
use App\Models\Publicacion;
use App\Models\Pagina;
use App\Models\Evento;
use App\Models\Normativa;
use App\Models\Lugar;
use App\Models\Guia;
use App\Pigmalion\Contenidos;
use App\Http\Controllers\ContactosController;
*/
use App\Models\Equipo;
use App\Models\Membresia;
use App\Observers\MembresiaObserver;
use App\Observers\EquipoObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \Backpack\PermissionManager\app\Http\Controllers\UserCrudController::class, //this is package controller
            \App\Http\Controllers\Admin\UserCrudController::class //this should be your own controller
        );

    }



    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Equipos y grupos

        // Lógica que se ejecutará cuando se cree o cambie algún equipo
        Equipo::observe(EquipoObserver::class);

        // observamos los cambios en membresías de equipos
        Membresia::observe(MembresiaObserver::class);
    }

}
