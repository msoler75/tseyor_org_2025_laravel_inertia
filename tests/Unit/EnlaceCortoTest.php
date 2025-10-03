<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Services\EnlaceCortoService;
use App\Models\EnlaceCorto;

class EnlaceCortoTest extends TestCase
{


     public function testUrlCortaGeneradaCorrectamente()
    {
        $enlace = new EnlaceCorto([
            'codigo' => 'ABC123',
            'prefijo' => 'e',
            'url_original' => 'https://tseyor.org/eventos/convivencias-de-otono-la-libelula',
        ]);
        $urlEsperada = rtrim(config('enlaces_cortos.dominios.principal'), '/') . '/e/ABC123';
        $this->assertEquals($urlEsperada, $enlace->url_corta);
    }


    public function testCrearEnlaceCortoParaVariasUrls()
    {
         $baseUrl = rtrim(config('app.url'), '/');
        $service = new EnlaceCortoService();
        $urls = [
             $baseUrl . '/eventos/convivencias-de-otono-la-libelula',
             $baseUrl . '/eventos/convivencias-de-primavera-el-colibri',
             $baseUrl . '/blog/2025/09/26/entrada-especial-con-slug-muy-largo-y-detalles',
             $baseUrl . '/recursos/documentos/guia-avanzada-2025.pdf?version=2.1',
             $baseUrl . '/audios/charla/2025/09/26/audio-charla-final.mp3?user=juan',
             $baseUrl . '/eventos/encuentro-internacional-2025',
             $baseUrl . '/presentaciones/ponencia-innovacion.pptx?ref=abc123',
             $baseUrl . '/musica/ambiental/relax.wav',
             $baseUrl . '/blog/2025/09/26/entrada-especial?utm_source=twitter',
             $baseUrl . '/inscripciones/curso-avanzado?step=2',
             $baseUrl . '/archivo_con_nombre_muy_largo_y_detalles.docx',
             $baseUrl . '/descargas/manual.xlsx?download=true',
             $baseUrl . '/galeria/imagen_con_nombre_largo.jpg',
             $baseUrl . '/streaming/video_con_nombre_largo.m4a',
             $baseUrl . '/otros/archivo-aleatorio.ogg',
             $baseUrl . '/doble/carp/1.pdf',
             $baseUrl . '/archivos/equipos/divulgacion/archivo.ppt',
             $baseUrl . '/archivos/equipos/agora-del-junantal/archivo.xls',
             $baseUrl . '/archivos/equipos/divulgacion/archivo.doc',
             $baseUrl . '/archivos/equipos/divulgacion/archivo.aac',
             $baseUrl . '/archivos/equipos/divulgacion/archivo.wma',
        ];
        $resultados = [];
        foreach ($urls as $url) {
            $enlace = $service->crear($url);
            if(!$enlace) {
                $resultados[] = [
                    'original' => $url,
                    'url_corta' => $url,
                ];
            } else {
                $resultados[] = [
                    'original' => $url,
                    'url_corta' => $enlace->url_corta,
                ];
            };
        }
        // Imprimir resultados para inspección manual
        // fwrite(STDERR, print_r($resultados, true));
        $this->assertCount(count($urls), $resultados);
        foreach ($resultados as $res) {
            if (empty($res['url_corta'])) {
                fwrite(STDERR, "FALLA: " . $res['original'] . " => url_corta vacío\n");
            }
            $this->assertNotEmpty($res['url_corta']);
        }
    }

    /**
     * Test del método necesitaAcortarse con diferentes tipos de URLs
     */
    public function testNecesitaAcortarse()
    {
        $service = new EnlaceCortoService();
        $baseUrl = rtrim(config('app.url'), '/');

        // URLs que NO deben acortarse (son cortas o coinciden con patrones excluidos)
        $urlsCortas = [
            $baseUrl . '/informes/552',           // Patrón excluido: números
            $baseUrl . '/informes/1234',          // Patrón excluido: números
            $baseUrl . '/experiencias/42',        // Patrón excluido: números
            $baseUrl . '/nodos/15',               // Patrón excluido: números
            $baseUrl . '/settings/3',             // Patrón excluido: números
            $baseUrl . '/',                       // Página principal
            $baseUrl . '/inicio',                 // Página principal
            $baseUrl . '/radio',                  // Página principal
            $baseUrl . '/mapa',                   // Página principal
            $baseUrl . '/contacto',               // Página principal
            $baseUrl . '/login',                  // Página principal
            $baseUrl . '/libros/abc',             // Slug muy corto
            $baseUrl . '/eventos/test',           // Slug muy corto
        ];        // URLs que SÍ deben acortarse (largas o complejas)
        $urlsLargas = [
            $baseUrl . '/eventos/convivencias-de-otono-en-la-libelula-con-muchos-detalles-y-actividades',
            $baseUrl . '/comunicados/comunicado-especial-sobre-la-situacion-actual-del-mundo-y-nuestras-propuestas',
            $baseUrl . '/libros/el-libro-de-las-ensenanzas-fundamentales-para-el-desarrollo-espiritual',
            $baseUrl . '/blog/2025/09/26/entrada-especial-con-slug-muy-largo-y-muchos-detalles-importantes',
            $baseUrl . '/noticias/noticia-importante-sobre-los-nuevos-desarrollos-tecnologicos-en-tseyor',
            $baseUrl . '/recursos/documentos/guia-completa-para-principiantes-en-el-camino-espiritual.pdf',
            $baseUrl . '/eventos/encuentro-internacional-de-hermanos-mayores-2025-en-madrid?registro=abierto&descuento=10',
            $baseUrl . '/cursos/curso-avanzado-de-meditacion-y-desarrollo-personal-nivel-3?inscripcion=activa',
            $baseUrl . '/audios/charlas/2025/charla-magistral-sobre-la-evolucion-de-la-conciencia-humana.mp3',
            $baseUrl . '/guias/guia-practica-para-la-aplicacion-de-los-principios-universales-en-la-vida-diaria',
        ];

        // Verificar URLs que NO deben acortarse
        foreach ($urlsCortas as $url) {
            $necesitaAcortar = $service->necesitaAcortarse($url);
            $this->assertFalse(
                $necesitaAcortar,
                "La URL '$url' no debería necesitar acortarse, pero el método devolvió true"
            );
        }

        // Verificar URLs que SÍ deben acortarse
        foreach ($urlsLargas as $url) {
            $necesitaAcortar = $service->necesitaAcortarse($url);
            $this->assertTrue(
                $necesitaAcortar,
                "La URL '$url' debería necesitar acortarse, pero el método devolvió false"
            );
        }
    }

    /**
     * Test específico para URLs con slugs largos que sí deben acortarse
     */
    public function testGeneracionEnlacesSlugLargos()
    {
        $service = new EnlaceCortoService();
        $baseUrl = rtrim(config('app.url'), '/');

        $urlsConSlugLargo = [
            $baseUrl . '/eventos/convivencias-de-otono-en-la-hermosa-libelula-con-actividades-especiales',
            $baseUrl . '/comunicados/comunicado-extraordinario-sobre-la-nueva-era-de-luz-y-amor-universal',
            $baseUrl . '/libros/manual-completo-de-ensenanzas-para-el-despertar-de-la-conciencia-cristica',
        ];        foreach ($urlsConSlugLargo as $url) {
            // Verificar que necesita acortarse
            $necesitaAcortar = $service->necesitaAcortarse($url);
            $this->assertTrue($necesitaAcortar, "URL con slug largo debería necesitar acortarse: $url");

            // Verificar que obtenerEnlaceParaUrl devuelve un enlace (no null)
            $fueAcortada = false;
            $enlace = $service->obtenerEnlaceParaUrl($url, $fueAcortada);

            $this->assertTrue($fueAcortada, "URL con slug largo debería haberse acortado: $url");
            $this->assertNotNull($enlace, "debería haberse creado un enlace para: $url");
            $this->assertNotEmpty($enlace->url_corta, "El enlace corto no debería estar vacío para: $url");
            $this->assertStringContainsString('/e/', $enlace->url_corta, "URL corta debe contener prefijo '/e/': " . $enlace->url_corta);
        }
    }
}
