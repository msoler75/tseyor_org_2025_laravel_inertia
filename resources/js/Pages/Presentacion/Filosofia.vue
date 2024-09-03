<template>
    <Sections height="500">

        <Head title="Filosofía Tseyor" />

        <Back href="/quienes-somos" floatAtY="-100">Quiénes Somos</Back>


        <Hero title="Tiempos de transformación" srcImage="/almacen/medios/paginas/hombre-universo.jpg">
            <p>Nos encontramos en un momento de transformación sin precedentes a nivel cósmico, que nos va a conducir a
                un nuevo nivel vibratorio, lo que significa mayor comprensión.</p>
            <p>La Confederación de Mundos Habitados de la Galaxia está tutelando al grupo Tseyor en un proceso de
                autodescubrimiento.</p>
            <p>Un proceso que llevará a la humanidad a conectarse con sus otras realidades interdimensionales.</p>
        </Hero>


        <Section>
            <Hero title="Una puerta abierta al infinito" srcImage="/almacen/medios/paginas/cristo-cosmico-replica.jpg"
                image-right src-width="600" src-height="400">
                <p>Aprendamos a conectar con nuestra chispa divina, con la inestimable ayuda del Cristo-Cósmico, a
                    través de nuestro amigo el pequeño Christian. </p>
                <p>Y descubriremos en nosotros nuestro universo interior, hallando la verdadera felicidad y la gran
                    Verdad que siempre estuvo ahí.</p>
            </Hero>
        </Section>

        <!--
            <Section>
        <TextImage title="Interesa la comprobación" srcImage="/almacen/medios/paginas/hombre-con-lupa.jpg"
                 href="/libros/filosofia" >
                <p>No habremos de creer nada, en lugar de eso comprobaremos mediante la experimentación.</p>
                <p>Solo la propia comprobación nos dará la convicción y la certeza que estamos en una transformación de
                    nuestras impresiones, contemplando las realidades que están aquí y ahora.</p>
                </TextImage>
            </Section>
            -->


        <Section>
            <TextImage title="Filosofía cuántica" srcImage="/almacen/medios/paginas/hombre-comprension.jpg"
                href="/libros/filosofia">
                <p>La realidad cuántica significa creación a través del pensamiento.</p>
                <p>Necesitamos claves, referencias, dispositivos, que nos permitan ese lanzamiento hacia las estrellas,
                    que en el fondo no es más que el reencuentro con uno mismo, a través de uno mismo en su universo
                    interior.</p>
            </TextImage>
        </Section>



        <Section>
            <TextImage title="Las sociedades armónicas" srcImage="/almacen/medios/paginas/SociedadesArmonicas.jpg"
                buttonLabel="Descargar Libro" image-right href="/libros/las-sociedades-armonicas" src-width="1920" src-height="1282">
                <p>Son sociedades en las que predomina la hermandad, no hay líderes, no hay enfermedad, y sí la
                    auténtica libertad.</p>
                <p>A nuestro planeta le ha llegado el momento de transformarse de forma pacífica y con mucho amor y
                    paciencia materializar las Sociedades Armónicas.</p>
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
                srcWidth="2432" srcHeight="1664" srcImage="/almacen/medios/paginas/inscribirse.jpg">
                <p>Te invitamos a recorrer con nosotros el camino del autodescubrimiento con plena libertad.</p>
                <p>Juntos, exploraremos nuestra esencia y buscaremos trascender las limitaciones de esta realidad
                    ilusoria. </p>
            </TextImage>
        </Section>

    </Sections>
</template>

<script setup>

import { usePage } from "@inertiajs/vue3";
const page = usePage()

</script>
