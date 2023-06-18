<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Centro;
use App\Models\Contacto;
use App\Models\Comunicado;
use App\Models\Entrada;
use App\Models\Noticia;
use App\Models\Libro;
use App\Models\Publicacion;
use App\Models\Evento;
use App\Models\Normativa;
// use TCG\Voyager\Facades\Voyager;
// use App\FormFields\MarkdownImagesField;
use App\Pigmalion\Contenidos;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Voyager::addFormField(MarkdownImagesField::class);
    }



    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // BEFORE SAVE
        Comunicado::saving(function ($comunicado) {
            Contenidos::rellenarSlugImagenYDescripcion($comunicado);
        });

        Centro::saving(function ($centro) {
            Contenidos::rellenarSlugImagenYDescripcion($centro);
        });

        Entrada::saving(function ($entrada) {
            Contenidos::rellenarSlugImagenYDescripcion($entrada);
        });

        Noticia::saving(function ($noticia) {
            Contenidos::rellenarSlugImagenYDescripcion($noticia);
        });

        Libro::saving(function ($libro) {
            Contenidos::rellenarSlugImagenYDescripcion($libro);
        });

        Contacto::saving(function ($contacto) {
            Contenidos::rellenarSlugImagenYDescripcion($contacto);
        });

        Evento::saving(function ($evento) {
            Contenidos::rellenarSlugImagenYDescripcion($evento);
        });

        Normativa::saving(function ($normativa) {
            Contenidos::rellenarSlugImagenYDescripcion($normativa);
        });

        Publicacion::saving(function ($publicacion) {
            Contenidos::rellenarSlugImagenYDescripcion($publicacion);
        });


        // SAVED
        Noticia::saved(function ($noticia) {
            Contenidos::guardarContenido("noticias", $noticia);
        });

        Comunicado::saved(function ($comunicado) {
            Contenidos::guardarContenido("comunicados", $comunicado);
        });

        Libro::saved(function ($libro) {
            Contenidos::guardarContenido("libros", $libro);
        });

        Entrada::saved(function ($entrada) {
            Contenidos::guardarContenido("entradas", $entrada);
        });

        Centro::saved(function ($centro) {
            Contenidos::guardarContenido("centros", $centro);
        });

        Contacto::saved(function ($contacto) {
            Contenidos::guardarContenido("contactos", $contacto);
        });

        Evento::saved(function ($evento) {
            Contenidos::guardarContenido("eventos", $evento);
        });

        Normativa::saved(function ($normativa) {
            Contenidos::guardarContenido("normativas", $normativa);
        });

        Publicacion::saved(function ($publicacion) {
            Contenidos::guardarContenido("publicaciones", $publicacion);
        });


    }



}
