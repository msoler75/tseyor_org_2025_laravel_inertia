<template>
    <Sections white-first>


        <div>
            <div class="container w-full mx-auto mt-4 lg:mt-12 flex justify-end mb-10">
                <span />
                <Share />
            </div>
        </div>


        <Section>
            <Text>
                <Prose>
                    <h1 class="hidden">ONG Mundo Armónico Tseyor</h1>

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


        <Section>
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


        <SectionBook
            titulo="Libro: ONG Mundo Armónico Tseyor"
            slug="la-ong-mundo-armonico-tseyor"
            portada="/almacen/medios/libros/227/La%20ONG%20Mundo%20Armonico%20Tseyor.jpg"
            enlace="/libros/la-ong-mundo-armonico-tseyor"
            buttonLabel="Ver en Biblioteca">
            <p>Todo el material recopilado de la ONG de Tseyor, desde julio de 2010 que la Confederación nos
                propuso su creación.</p>
        </SectionBook>

        <Section>
            <EvaluacionSeccion seccion="Voluntarios" :idoneidad="95" :claridad="90" :composicion="85" :posicion="90" />
            <TextImage title="Voluntarios TSEYOR" srcImage="/almacen/medios/paginas/quienes-somos.jpg" image-right>
                <p>Ser voluntario en TSEYOR es <strong>formar parte activa de una comunidad viva</strong>. No hay cargos ni jerarquías — todos somos aprendices, todos compartimos y crecemos juntos.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div class="bg-base-200/50 p-4 rounded-xl text-left">
                        <h4 class="font-bold text-sm">Divulgación del mensaje</h4>
                        <p class="text-xs mt-1">Compartimos la filosofía cósmico-crística a través de conversaciones, escritos y encuentros.</p>
                    </div>
                    <div class="bg-base-200/50 p-4 rounded-xl text-left">
                        <h4 class="font-bold text-sm">Talleres vivenciales</h4>
                        <p class="text-xs mt-1">Participamos en experiencias prácticas de autodescubrimiento y conexión grupal.</p>
                    </div>
                    <div class="bg-base-200/50 p-4 rounded-xl text-left">
                        <h4 class="font-bold text-sm">Convivencias y viajes</h4>
                        <p class="text-xs mt-1">Compartimos momentos de encuentro presencial, aprendizaje colectivo y naturaleza.</p>
                    </div>
                    <div class="bg-base-200/50 p-4 rounded-xl text-left">
                        <h4 class="font-bold text-sm">Aprendizaje continuo</h4>
                        <p class="text-xs mt-1">Todos enseñamos y todos aprendemos. El Curso Holístico es nuestra guía compartida.</p>
                    </div>
                </div>
                <p class="text-sm mt-4 italic">No se necesita experiencia previa ni conocimientos especiales. Solo las ganas de crecer y servir.</p>
                <Link :href="route('cursos.inscripcion.nueva')" class="btn btn-primary btn-sm mt-4">Quiero ser voluntario</Link>
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
