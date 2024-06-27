<template>
    <Sections height="500">

        <Hero title="Tiempos de transformación" srcImage="/almacen/medios/paginas/pareja-espacio.jpg" src-width="600"
            src-height="400" >
            <p>Nos encontramos en un momento de transformación cósmica que requiere de nuestro desarrollo personal, equilibrio y conexión con la realidad objetiva. </p>
            <p>Al elevar nuestra vibración acogeremos la energía del cosmos y facilitaremos la transmutación de la humanidad y el planeta, pues somos Uno.</p>
        </Hero>


        <Section>
            <Hero title="Una puerta abierta al infinito" srcImage="/almacen/medios/paginas/cristo-cosmico.jpg"
                image-right src-width="600" src-height="400" >
                <p>El grupo Tseyor participa en una aventura para acceder a nuestra realidad interdimensional a través de nuestro pensamiento más profundo.</p>
                <p>Contamos con la inestimable ayuda del Cristo-Cósmico, a través de nuestro amigo el pequeño Christian. </p>
                <p>Estamos siendo tutelados por todo el amor de la Confederación de Mundos Habitados de la Galaxia.</p>
            </Hero>
        </Section>

        <Section>
            <TextImage title="Interesa la comprobación" srcImage="/almacen/medios/paginas/creer-planta.jpg"
                src-width="600" src-height="400" href="/libros/filosofia" >
                <p>No habremos de creer nada, en lugar de eso comprobaremos mediante la experimentación.</p>
                <p>Solo la propia comprobación nos dará la convicción y la certeza que estamos en una transformación de
                    nuestras impresiones, contemplando todas las nuevas realidades que están aquí y ahora.</p>
            </TextImage>
        </Section>


        <Section>
            <TextImage title="Las sociedades armónicas" srcImage="/almacen/medios/paginas/sociedades-armonicas.jpg"
                buttonLabel="Descargar Libro" image-right href="/sociedades" src-width="500" src-height="700">
                <p>
                    Son sociedades que han superado todas nuestras lacras, en
                    las que predomina la hermandad, la entrega a los demás y la libertad sin más restricción que la
                    responsabilidad de cada cual.
                </p>
                <p>
                    A nuestro planeta le ha llegado el momento de transformarse de forma pacífica y sin interferencias y materializar las Sociedades Armónicas.
                </p>
            </TextImage>
        </Section>




        <Section>
            <TextImage title="Glosario" buttonLabel="Ir al Glosario" src-width="600" src-height="400" href="/glosario"
                srcImage="/almacen/medios/paginas/glosario.jpg">
                <p>
                    Consulta todos los términos de la filosofía Tseyor en nuestro glosario de términos. También podrás
                    ver los Guías Estelares y los lugares interesantes de la Galaxia.
                </p>
            </TextImage>
        </Section>


        <!-- Sección: Quiero ser de Tseyor -->
        <Section>
        <TextImage title="Únete a Tseyor" image-right buttonLabel="Formulario de inscripción" href="/inscripcion"
            srcImage="/almacen/medios/paginas/inscribirse.jpg" >
            <p>Te invitamos a recorrer con nosotros el camino del autodescubrimiento con plena libertad.</p>
            <p>Juntos, exploraremos nuestra esencia y buscaremos trascender las limitaciones de esta realidad ilusoria. </p>
        </TextImage>
    </Section>

    </Sections>
</template>

<script setup>

import { usePage } from "@inertiajs/vue3";
const page = usePage()
import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

</script>
