<template>
    <Sections>
        <!--
            <Seo
        title="Filosofía de Tseyor"
        description="Conoce la filosofía que proviene de las estrellas"
        />
    -->

        <Hero title="La Filosofía Tseyor" subtitle="No habremos de creer nada, en lugar de eso comprobaremos mediante la experimentación."
            srcImage="https://via.placeholder.com/1600x1000.png/00088f?text=no-creer-nada" image-right
            labelButton="Leer libro"
            href="/libros/filosofia"
            ></Hero>


        <Section>
            <TextImage title="Los tiempos que corren"
                srcImage="https://via.placeholder.com/563x269.png/5000dd?text=tiempos-que-corren">
                <p>Estamos en unos momentos de transformación y transmutación en los que debemos trabajar en
                    nuestro desarrollo personal, equilibrio, armonía y conexión con nuestra realidad objetiva.</p>
                <p> Nuestra vibración energética se elevará para acoger la energía proveniente del cosmos y
                    facilitar la transformación de la humanidad y el planeta mismo.</p>
            </TextImage>
        </Section>


        <Section>
            <TextImage title="Una puerta abierta al infinito"
                srcImage="https://via.placeholder.com/963x569.png/f00fdd?text=groupMeeting" image-right>
                <p>El grupo Tseyor participa en una aventura que consiste en acceder a nuestra realidad objetiva,
                    espiritual e interdimensional a través de la puerta abierta al infinito desde nuestro corazón y
                    nuestro pensamiento inspirado en los valores y las energías cósmicas.</p>
            </TextImage>
        </Section>

        <Section>
            <TextImage title="Las sociedades armónicas"
                srcImage="https://via.placeholder.com/700x600.png/0000ff?text=ONG.jpg" buttonLabel="Descargar Libro"
                href="/sociedades">
                <p>
                    A este tipo de sociedades pertenecen las civilizaciones que forman parte de la citada
                    Confederación de Mundos Habitados. Son sociedades que han superado todas nuestras lacras, en
                    las
                    que predomina la hermandad, la entrega a los demás y la libertad sin más restricción que la
                    responsabilidad de cada cual.
                </p>
                <p>
                    A nuestro planeta le ha llegado el momento de transformarse en el soporte de una sociedad
                    armónica en contacto con todas las que nos rodean, y que están colaborando en nuestra
                    transformación de forma pacífica y sin interferencias.
                </p>
            </TextImage>
        </Section>


                    <!-- Sección: Qué significa TSEYOR -->
                    <Section>
            <Hero title="¿Qué significa TSEYOR?" buttonLabel="Más información" href="/tseyor-significado">
                <p class="text-center">
                    Tseyor es un acrónimo que corresponde a:
                </p>
                <p class="text-center text-xl">
                    <b>T</b>iempo <b>S</b>imbólico <b>E</b>stelar del <b>YO</b> en <b>R</b>etroalimentación
                </p>
                <p class="text-center">
                    Y en él están incluidas miles de personas que anhelan el perfeccionamiento de su pensamiento.
                </p>
            </Hero>
        </Section>

         <Section>
            <TextImage title="Glosario"
                srcImage="https://via.placeholder.com/700x400.png/00ffff?text=Glosario" buttonLabel="Ir al Glosario"
                image-right
                href="/glosario">
                <p>
                   Consulta todos los términos de la filosofía Tseyor en nuestro glosario de términos. También podrás ver los Guías Estelares y los Lugares de la Galaxia.
                </p>
            </TextImage>
        </Section>


        <!-- Sección: Quiero ser de Tseyor -->
        <Hero title="Quiero ser de Tseyor" subtitle="Entra a forma parte del grupo" buttonLabel="Formulario de inscripción"
            href="/inscripcion" textClass="space-y-8" />

    </Sections>
</template>

<script setup>

import { usePage } from "@inertiajs/vue3";
const page = usePage()
import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

</script>
