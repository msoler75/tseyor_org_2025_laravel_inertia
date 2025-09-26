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
        $service = new EnlaceCortoService();
        $urls = [
            'https://tseyor.org/eventos/convivencias-de-otono-la-libelula',
            'https://tseyor.org/eventos/convivencias-de-primavera-el-colibri',
            'https://tseyor.org/blog/2025/09/26/entrada-especial-con-slug-muy-largo-y-detalles',
            'https://tseyor.org/recursos/documentos/guia-avanzada-2025.pdf?version=2.1',
            'https://tseyor.org/audios/charla/2025/09/26/audio-charla-final.mp3?user=juan',
            'https://tseyor.org/eventos/encuentro-internacional-2025',
            'https://tseyor.org/presentaciones/ponencia-innovacion.pptx?ref=abc123',
            'https://tseyor.org/musica/ambiental/relax.wav',
            'https://tseyor.org/blog/2025/09/26/entrada-especial?utm_source=twitter',
            'https://tseyor.org/inscripciones/curso-avanzado?step=2',
            'https://tseyor.org/archivo.docx',
            'https://tseyor.org/descargas/manual.xlsx?download=true',
            'https://tseyor.org/galeria/imagen.jpg',
            'https://tseyor.org/streaming/video.m4a',
            'https://tseyor.org/otros/archivo-aleatorio.ogg',
            'https://tseyor.org/recursos/guia-basica.pdf',
            'https://tseyor.org/sonidos/efecto.flac',
            'https://tseyor.org/landing?promo=2025',
            'https://tseyor.org/archivos/equipos/divulgacion/archivo.ppt',
            'https://tseyor.org/archivos/equipos/agora-del-junantal/archivo.xls',
            'https://tseyor.org/archivos/equipos/divulgacion/archivo.doc',
            'https://tseyor.org/archivos/equipos/divulgacion/archivo.aac',
            'https://tseyor.org/archivos/equipos/divulgacion/archivo.wma',
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
}
