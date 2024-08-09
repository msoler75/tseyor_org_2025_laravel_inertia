<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Contenido;
use App\Models\Pagina;


class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sitemap.xml';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contenidosMap = Sitemap::create();

        Contenido::all()->each(function (Contenido $contenido) use ($contenidosMap) {

            $noindexar = ['informes', 'paginas', 'experiencias'];

            $traducir = ['terminos' => 'glosario'];

            if (!in_array($contenido->coleccion, $noindexar)) {

                $palabra = $traducir[$contenido->coleccion] ?? $contenido->coleccion; 
                $contenidosMap->add(
                    Url::create("/{$palabra}/" . ($contenido->slug_ref ? $contenido->slug_ref : $contenido->id_ref))
                        ->setPriority(0.8)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                );
            
            }
        });

        // agregamos todas las páginas, con algo más de prioridad
        Pagina::all()->each(function (Pagina $pagina) use ($contenidosMap) {
            $contenidosMap->add(
                Url::create($pagina->ruta)
                    ->setPriority(0.9)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            );
        });

        $contenidosMap->writeToFile(public_path('sitemap.xml'));
    }
}
