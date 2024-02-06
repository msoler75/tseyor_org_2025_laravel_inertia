<?php

namespace App\Pigmalion;

use App\Models\Contenido;
use Illuminate\Support\Facades\Cache;


class NovedadesHelper
{

    // Establece la duración de la caché
    public static $cacheDuration = 60 * 60 * 24;

    // Establece la clave de la caché
    public static $cacheKey = 'novedades';

    public static $colecciones_excluidas = ['paginas', 'informes', 'normativas', 'audios', 'meditaciones', 'terminos', 'lugares', 'guias'];

    /**
     * Borra la cache
     */
    public static function clearCache()
    {
        Cache::forget(self::$cacheKey);
    }

    /**
     * Carga la primera página de novedades en contenidos
     */
    public static function getNovedades($page)
    {
        $user = auth()->user();

        $esAdministrador = $user && $user->hasPermissionTo('administrar contenidos');

        // solo se cachea la primera página
        // vista para usuarios normales:
        if ($page == 1 && !$esAdministrador)
            return Cache::remember(self::$cacheKey, self::$cacheDuration, function () {
                return Contenido::select(['slug_ref', 'titulo', 'imagen', 'descripcion', 'fecha', 'coleccion', 'visibilidad'])
                    ->where('visibilidad', 'P')
                    ->whereNotIn('coleccion', self::$colecciones_excluidas)
                    ->latest('updated_at') // Ordenar por updated_at
                    ->paginate(10);
            });


        // los administradores ven todos los contenidos, y si están en modo publicado o borrador
        return Contenido::select(['slug_ref', 'titulo', 'imagen', 'descripcion', 'fecha', 'coleccion', 'visibilidad'])
            ->when(!$esAdministrador, function ($query) {
                $query->where('visibilidad', 'P');
            })
            ->whereNotIn('coleccion', self::$colecciones_excluidas)
            ->latest('updated_at') // Ordenar por updated_at
            ->paginate(10);
    }



}




