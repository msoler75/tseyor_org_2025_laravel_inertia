<template>
    <Sections white-first>
        <Section>
            <Text>
                <Prose>

                    <img src="/archivos/logos/logo-ong-mundo-armonico.JPG"
                        class="w-full h-auto max-w-[709px] aspect-709/278" alt="ONG Mundo Armónico Tseyor"/>
                    <blockquote>
                        <p>Se está preparando un gran cambio y ello comportará también grandes dificultades. Nuestro
                            grupo, el grupo Tseyor, pondrá su granito de arena para paliarlas.</p>
                    </blockquote>
                    <blockquote>
                        <p>
                            Habéis recibido cantidad de información; muchos estómagos necesitan de dicho alimento
                            espiritual.
                            Poneros de acuerdo con ese figurado eje central que es la universidad, para preparar
                            trabajos, escritos, monografías...
                        </p>
                        <p>
                            Ahí también está una parte importante de la ayuda humanitaria.
                        </p>
                    </blockquote>

                </Prose>
            </Text>
        </Section>


        <Section class="container">
            <Text>
                <h2>Objetivos de la ONG Mundo Armónico Tseyor</h2>
                <p>El objeto general de esta Asociación es la cooperación en la consecución de una sociedad
                    armónica, justa y solidaria, partiendo de una verdadera conciencia de la solidaridad, que contribuya
                    a la participación
                    social, a la reflexión sobre las causas de la injusticia y a la creación de sociedades armónicas.
                </p>

                <p>Este fin se concreta en los objetivos específicos:</p>

                <ol class="list-decimal pl-4">
                    <li>Proporcionar ayuda humanitaria a personas, grupos y colectivos que la necesiten:
                        alimentación,
                        medios,
                        equipos, semillas y recursos de salud.</li>
                    <li>Cooperar a favor de un desarrollo humano sostenible.</li>
                    <li>Aportar formación cultural, científica y técnica a personas y grupos que la demanden y no
                        tengan
                        acceso
                        a la misma.</li>
                    <li>Realizar actividades que redunden en el beneficio de personas y colectivos, sobre todo de
                        los más
                        carentes, necesitados o indefensos, y en casos de catástrofes naturales o de origen
                        antrópico.</li>
                    <li>Apoyo para la gestión de proyectos, en comunidades empobrecidas, que persigan un desarrollo
                        endógeno
                        sostenible.</li>
                    <li>Sensibilizar a la población en valores relacionados con la armonía, la cooperación, la
                        solidaridad,
                        la
                        justicia social y el respeto de los seres humanos, cualesquiera que sea su condición.</li>
                    <li>Proporcionar medios y formación en el ámbito de la salud, la alimentación, la agricultura,
                        la
                        expresión
                        de ideas y sentimientos que despierten los potenciales creativos de los seres humanos en
                        armonía con
                        el
                        entorno natural.</li>
                </ol>

                <p class="flex justify-end">
                    <ActionButton :href="estatutosUrl">
                        Ver Estatutos completos
                    </ActionButton>
                </p>
            </Text>
        </Section>


        <Section>
            <TextImage title="Libro: ONG Mundo Armónico Tseyor" class="container" buttonLabel="Ver en Biblioteca"
                href="/libros/la-ong-mundo-armonico-tseyor">
                <p>Todo el material recopilado de la ONG de Tseyor, desde julio de 2010 que la Confederación nos
                    propuso su creación.</p>
                <template #image>
                    <Libro3d
                        :libro="{ slug: 'la-ong-mundo-armonico-tseyor', imagen: '/almacen/medios/libros/227/La%20ONG%20Mundo%20Armonico%20Tseyor.jpg' }"
                        imageClass="w-[200px]" />
                </template>
            </TextImage>
        </Section>

        <Section>
            <TextImage title="Muular" srcImage="/almacen/medios/paginas/muular.png" buttonLabel="Conoce el Muular"
                :href="route('muular')" image-right>
                <p>La moneda social y espiritual de Tseyor</p>
            </TextImage>
        </Section>


    </Sections>
</template>

<script setup>

defineProps({
    estatutosUrl: {
        type: String,
        required: false
    }
})

</script>
