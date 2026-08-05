<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Libro;
use App\Pigmalion\SEO;

class CursosController extends Controller
{
    /**
     * Testimonios de la página de cursos.
     * Por ahora son ficticios; se reemplazarán por testimonios reales.
     */
    private function testimonios(): array
    {
        return [
            [
                'frase' => 'Nunca en todas las filosofías espirituales que he conocido me han llevado a la comprensión del por qué y para qué estoy aquí como en Tseyor.',
                'nombre' => 'Presta Atención La Pm',
                'pais' => 'España',
                'imagen' => '/almacen/medios/testimonios/presta-atencion.jpg',
            ],
            [
                'frase' => 'Para mí Tseyor supuso el comienzo de un camino que no tiene fin, un camino eterno.',
                'nombre' => 'Punto Sur La Pm',
                'pais' => 'España',
                'imagen' => '/almacen/medios/testimonios/punto-sur.jpg',
            ],
            [
                'frase' => 'Tseyor ha sido mi llamada espiritual.',
                'nombre' => 'Electrón Pm',
                'pais' => 'España',
                'imagen' => '/almacen/medios/testimonios/electron.jpg',
            ],
            [
                'frase' => 'En Tseyor he aprendido a vivir el presente y disfrutar de cada momento con gratitud y alegría. La sabiduría de Tseyor ha calado en lo más profundo de mi ser, transformandome en un hombre nuevo y consciente de mi realidad existencial.',
                'nombre' => 'Romano Primo Pm',
                'pais' => 'Venezuela',
                'imagen' => '/almacen/medios/testimonios/romano-primo.jpg',
            ],
            [
                'frase' => 'Tseyor para mí es amor, reconocimiento de uno mismo, reencuentros.',
                'nombre' => 'Referencia Tseyor La Pm',
                'pais' => 'Chile',
                'imagen' => '/almacen/medios/testimonios/referencia-tseyor.jpg',
            ],
            [
                'frase' => 'Estar en tseyor me está dando la oportunidad de ir tomando conciencia poco a poco sobre quien soy, hacía donde voy; también sobre la importancia del AMOR y la ayuda humanitaria sin esperar nada a cambio.',
                'nombre' => 'Benéfica Amor Pm',
                'pais' => 'Panamá',
                'imagen' => '/almacen/medios/testimonios/benefica-amor.jpg',
            ],
            [
                'frase' => 'Para mí tseyor ha sido alejarme de la política y encontrar la espiritualidad.',
                'nombre' => 'Punto Este La Pm',
                'pais' => 'España',
                'imagen' => '/almacen/medios/testimonios/punto-este_cr.jpg',
            ],
            [
                'frase' => 'Maravilloso ha sido disfrutar de este gran aprendizaje que me ha enseñado a conocerme y amar.',
                'nombre' => 'Colorea Copiosamente La Pm',
                'pais' => 'Chile',
                'imagen' => '/almacen/medios/testimonios/colorea-copiosamente_cr.jpg',
            ],
            [
                'frase' => 'Tseyor representa en mi vida un antes y un después. Reconociéndome como parte de un todo, me cuestiono quién soy, para qué estoy aquí y hacia dónde voy. Experimento ese Despertar de Consciencia a través de la autoobservación, en camino hacia la realidad de los mundos.',
                'nombre' => 'Nepal',
                'pais' => 'Colombia',
                'imagen' => '/almacen/medios/testimonios/nepal.jpg',
            ],
            [
                'frase' => 'Para mí uno de los motivos por los que estoy en Tseyor es la oportunidad de tener una vida consciente, en equilibrio, alegre, con creatividad y solidaridad, experimentando que mi crecimiento espiritual es el mismo que puede darse en toda la humanidad.',
                'nombre' => 'Patrón Marino La Pm',
                'pais' => 'España',
                'imagen' => '/almacen/medios/testimonios/patron-marino.jpg',
            ],
            [
                'frase' => 'Tseyor se ha convertido en mi filosofía de vida. Hermandad, comprensión, bondad, trabajo en equipo, dar sin esperar nada a cambio. Cambios de pensamiento,  mejora de salud en todo sentido y despertar de conciencia.',
                'nombre' => 'Arán Valles Pm',
                'pais' => 'México',
                'imagen' => '/almacen/medios/testimonios/aran-valles.jpg',
            ],
            [
                'frase' => 'Tseyor: mi camino hacia la libertad, hacia mi auténtica realidad, a mi Génesis.',
                'nombre' => 'Asombroso La Pm',
                'pais' => 'México',
                'imagen' => '/almacen/medios/testimonios/asombroso.jpg',
            ],
            [
                'frase' => 'En Tseyor he encontrado las respuestas a muchas inquietudes, sueños y visiones que tenia desde niña. Al conocer sus proyectos siento esperanza y mucho agradecimiento por todos los miembros del grupo. ¡Gracias!',
                'nombre' => 'Un Gran Suspiro La Pm',
                'pais' => 'República Dominicana La Pm',
                'imagen' => '/almacen/medios/testimonios/un-gran-suspiro.jpg',
            ],
            [
                'frase' => 'Tseyor ha Supuesto un cambio de mentalidad, me permite el autorreconocimiento, una manera de enfocar mi vida hacia un  objetivo de ayuda humanitaria.',
                'nombre' => 'Capitel Pi Pm',
                'pais' => 'Chile',
                'imagen' => '/almacen/medios/testimonios/capitel-pi_cr.jpg',
            ],
            [
                'frase' => 'Tseyor me ha hecho recordar y confirmar quién soy y de dónde vengo. Sentir que las experiencias vividas cobraban sentido cual piezas de puzzle que encajaban. Que la Alegría, el Entusiasmo y el Optimismo han de estar presentes en nuestro caminar.',
                'nombre' => 'No Te Olvides La Pm',
                'pais' => 'España',
                'imagen' => '/almacen/medios/testimonios/no-te-olvides.jpg',
            ],
        ];
    }

    public function index()
    {
        $libro = Libro::where('slug', 'curso-holistico-tseyor')->first();
        $libroGuias = Libro::where('slug', 'los-guias-estelares')->first();

        return Inertia::render('Cursos/Index', [
            'libro' => $libro,
            'libroGuias' => $libroGuias,
            'testimonios' => $this->testimonios(),
        ])
        ->withViewData(SEO::get('cursos'));
    }
}
