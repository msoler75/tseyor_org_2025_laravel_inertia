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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contenidosMap = Sitemap::create();

        Contenido::get()->each(function(Contenido $contenido) use($contenidosMap) {
            $noindexar = ['informes', 'paginas', 'experiencias'];
            if(!in_array ($contenido->coleccion,  $noindexar))
            $contenidosMap->add(
                Url::create("/{$contenido->coleccion}/".($contenido->slug_ref?$contenido->slug_ref:$contenido->id_ref))
                ->setPriority(0.8)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            );
        });

        Pagina::get()->each(function(Pagina $pagina) use($contenidosMap) {
            $contenidosMap->add(
                Url::create($pagina->ruta)
                ->setPriority(0.9)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            );
        });

        $contenidosMap->writeToFile(public_path('sitemap.xml'));
    }
}
