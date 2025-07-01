<template>
    <Back href="/quienes-somos" floatAtY="-100">Quiénes Somos</Back>
    <Sections height="550" white-first>

        <div class="container mx-auto text-center my-10">
            <h1>Filosofía de Tseyor</h1>
        </div>

        <Hero title="Tiempos de transformación" srcImage="/almacen/medios/paginas/hombre-universo.jpg" full cover>
            <p>Nos encontramos en un momento de transformación sin precedentes a nivel cósmico, que nos va a conducir a
                un nuevo nivel vibratorio, lo que significa mayor comprensión.</p>
            <p>La Confederación de Mundos Habitados de la Galaxia está tutelando al grupo Tseyor en un proceso de
                autodescubrimiento.</p>
            <p>Un proceso que llevará a la humanidad a conectarse con sus otras realidades interdimensionales.</p>
        </Hero>


        <Section>
            <Hero title="Una puerta abierta al infinito" srcImage="/almacen/medios/paginas/cristo-cosmico-replica.jpg"
                image-right src-width="600" src-height="400" full cover>
                <p>Aprendamos a conectar con nuestra chispa divina, con la inestimable ayuda del Cristo-Cósmico, a
                    través de nuestro amigo el pequeño Christian. </p>
                <p>Y descubriremos en nosotros nuestro universo interior, hallando la verdadera felicidad y la gran
                    Verdad que siempre estuvo ahí.</p>
            </Hero>
        </Section>

        <Section>
            <TextImage
                title="El proceso de autodescubrimiento"
                :href="route('cursos')"
                buttonLabel="Curso gratuito"
                srcImage="/almacen/medios/portada/meditando.jpg"
                src-width="1920" src-height="1080"
                full cover
                image-side-class="bg-bottom-left!"
            >
                <p>
                    A través de meditaciones, talleres y un gran sentimiento de hermanamiento conseguimos transmutar nuestra personalidad hasta lograr la Unidad.
                </p>
            </TextImage>
        </Section>

        <!--
            <Section>
        <TextImage title="Interesa la comprobación" srcImage="/almacen/medios/paginas/hombre-con-lupa.jpg"
                 href="/libros/filosofia" image-right>
                <p>No habremos de creer nada, en lugar de eso comprobaremos mediante la experimentación.</p>
                <p>Solo la propia comprobación nos dará la convicción y la certeza que estamos en una transformación de
                    nuestras impresiones, contemplando las realidades que están aquí y ahora.</p>
                </TextImage>
            </Section>
            -->


        <Section>
            <TextImage title="Filosofía cuántica" srcImage="/almacen/medios/paginas/hombre-comprension.jpg"
                href="/libros/filosofia" image-right full cover                >
                <p>La realidad cuántica significa creación a través del pensamiento.</p>
                <p>Necesitamos claves, referencias, dispositivos, que nos permitan ese lanzamiento hacia las estrellas,
                    que en el fondo no es más que el reencuentro con uno mismo, a través de uno mismo en su universo
                    interior.</p>
            </TextImage>
        </Section>



        <Section>
            <TextImage title="Las sociedades armónicas" srcImage="/almacen/medios/paginas/SociedadesArmonicas.jpg"
                buttonLabel="Descargar Libro" href="/libros/las-sociedades-armonicas" src-width="1920" src-height="1282"
                full cover>
                <p>Son sociedades en las que predomina la hermandad, no hay líderes, no hay enfermedad, y sí la
                    auténtica libertad.</p>
                <p>A nuestro planeta le ha llegado el momento de transformarse de forma pacífica y con mucho amor y
                    paciencia materializar las Sociedades Armónicas.</p>
            </TextImage>
        </Section>


        <Section>
            <TextImage title="Glosario" buttonLabel="Ir al Glosario" src-width="600" src-height="400" href="/glosario"
                srcImage="/almacen/medios/paginas/glosario.jpg" image-right
                full cover>
                <p>
                    Consulta todos los términos de la filosofía Tseyor en nuestro glosario de términos. También podrás
                    ver los Guías Estelares y los lugares interesantes de la Galaxia.
                </p>
            </TextImage>
        </Section>



        <Section>
            <TextImage title="El Rayo Sincronizador">
                <p>
                    Una nueva era se abre con el rayo sincronizador: prepárate
                    para vibrar más alto.
                </p>
                <p>
                    Este rayo elevará la vibración de la Tierra y de todos sus
                    seres, permitiendo la integración de nuevas energías y la
                    apertura a dimensiones superiores.
                </p>
                <template #image>
                    <!-- Imagen para El Rayo Sincronizador -->
                </template>
            </TextImage>
        </Section>
        <Section>
            <TextImage title="Pueblo Tseyor">
                <p>
                    Construimos juntos comunidades armónicas, portales de
                    hermandad y trascendencia.
                </p>
                <p>
                    Los Pueblos Tseyor son espacios de autosostenibilidad,
                    hermandad y comunicación interdimensional, donde florece la
                    espiritualidad y la cooperación.
                </p>
                <template #image>
                    <!-- Imagen para Pueblo Tseyor -->
                </template>
            </TextImage>
        </Section>
        <Section>
            <TextImage title="Recordar nuestros orígenes">
                <p>
                    Somos semillas de las estrellas, aquí para mantener viva la
                    llama espiritual.
                </p>
                <p>
                    Aunque olvidemos nuestro origen cósmico, permanece en
                    nuestro ADN. Nuestra misión es ayudar y acompañar el
                    despertar de la humanidad.
                </p>
                <template #image>
                    <!-- Imagen para Recordar nuestros orígenes -->
                </template>
            </TextImage>
        </Section>
        <Section>
            <TextImage title="Sacrificio por la humanidad">
                <p>
                    El mayor servicio es darlo todo sin esperar nada a cambio.
                </p>
                <p>
                    El sacrificio consciente, fruto de la autoobservación y el
                    desapego, propaga la transformación a través de los campos
                    morfogenéticos de la humanidad.
                </p>
                <template #image>
                    <!-- Imagen para Sacrificio por la humanidad -->
                </template>
            </TextImage>
        </Section>
        <Section>
            <TextImage title="Desapego">
                <p>La libertad nace cuando soltamos lo que creemos poseer.</p>
                <p>
                    El desapego es una tendencia espiritual que nos libera del
                    miedo y la dependencia, permitiendo servir con entrega
                    total.
                </p>
                <template #image>
                    <!-- Imagen para Desapego -->
                </template>
            </TextImage>
        </Section>
        <Section>
            <TextImage title="Autoobservación">
                <p>Obsérvate desde la consciencia, no desde la mente.</p>
                <p>
                    La autoobservación nos reconecta con nuestro ser universal y
                    permite la transmutación de las dualidades ilusorias.
                </p>
                <template #image>
                    <!-- Imagen para Autoobservación -->
                </template>
            </TextImage>
        </Section>
        <Section>
            <TextImage title="El Yo Absoluto">
                <p>
                    Todos somos partículas del Absoluto, unidos en la
                    retroalimentación.
                </p>
                <p>
                    Tseyor significa Tiempo Simbólico Estelar del YO en
                    Retroalimentación: cada uno es parte y totalidad del Yo
                    Absoluto.
                </p>
                <template #image>
                    <!-- Imagen para El Yo Absoluto -->
                </template>
            </TextImage>
        </Section>
        <Section>
            <TextImage title="Nueva Jerusalén">
                <p>
                    Las sociedades armónicas son el reflejo de una humanidad
                    renovada.
                </p>
                <p>
                    Tras la llegada del rayo sincronizador, surgirán sociedades
                    donde predominen la hermandad, la libertad y la
                    autosostenibilidad.
                </p>
                <template #image>
                    <!-- Imagen para Nueva Jerusalén -->
                </template>
            </TextImage>
        </Section>
        <Section>
            <TextImage title="Siembra de una nueva humanidad">
                <p>
                    La evolución abre paso a una nueva generación de seres
                    conscientes.
                </p>
                <p>
                    La humanidad ascenderá a una dimensión de mayor vibración,
                    coexistiendo o dando paso a nuevas formas de vida
                    conscientes.
                </p>
                <template #image>
                    <!-- Imagen para Siembra de una nueva humanidad -->
                </template>
            </TextImage>
        </Section>
        <Section>
            <TextImage title="El mundo es ilusión">
                <p>
                    Vivimos en un teatro cósmico; la verdadera realidad está más
                    allá de la escenificación.
                </p>
                <p>
                    Este mundo es una ilusión que permite el aprendizaje y la
                    evolución. No olvides tu origen y tu destino trascendente.
                </p>
                <template #image>
                    <!-- Imagen para El mundo es ilusión -->
                </template>
            </TextImage>
        </Section>


        <Section>
            <TextImage title="Felicidad y hermandad">
                <p>La felicidad se alcanza en la unidad y el compartir.</p>
                <p>
                    La auténtica felicidad no es individual, sino que se
                    manifiesta en la hermandad, la divulgación y el equilibrio
                    con uno mismo y con todos.
                </p>
                <template #image>
                    <!-- Imagen para Felicidad y hermandad -->
                </template>
            </TextImage>
        </Section>


        <!-- Sección: Quiero ser de Tseyor -->
        <Section>
            <TextImage title="Únete a Tseyor" buttonLabel="Formulario de inscripción" href="/inscripcion"
                srcWidth="2432" srcHeight="1664" srcImage="/almacen/medios/paginas/inscribirse.jpg"
                class="pt-20 pb-40">
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
