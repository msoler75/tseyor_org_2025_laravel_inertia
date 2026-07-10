<template>
    <Sections height="500">

        <!-- 1. Hero: ¿Quiénes somos? -->
        <Section>
            <EvaluacionSeccion seccion="Hero: ¿Quiénes somos?" :idoneidad="95" :claridad="90" :composicion="85" :posicion="95" />
            <Hero title="¿Quiénes Somos?"
                srcImage="/almacen/medios/paginas/quienes-somos.jpg"
                src-width="1600"
                src-height="1200"
                buttonLabel="Conoce nuestros orígenes"
                :href="route('origenes-de-tseyor')">
                <p>Mundo Armónico Tseyor es una ONG dedicada al autodescubrimiento y a la creación de las Sociedades Armónicas en este planeta.</p>
                <p>Sus principios se basan en la experimentación de la filosofía cósmico-crística que nos traen nuestros Guías Estelares, con quienes mantenemos conversaciones regulares con la Confederación de Mundos Habitados de la Galaxia.</p>
                <p>Esta es una filosofía viva, no dogmática, y plena de vigor espiritual y creativo, en estos tiempos de transformación.</p>
                <p>Somos una ONG sin ánimo de lucro. Nuestra comunidad está conformada por miembros de Mundo Armónico TSEYOR y nuestras actividades se desarrollan desde el servicio, la ayuda humanitaria y el conocimiento libre.</p>
                <div class="flex flex-wrap justify-center gap-2 mt-4">
                    <span class="badge badge-primary text-xs">ONG sin ánimo de lucro</span>
                    <span class="badge badge-secondary text-xs">Sin dogmas</span>
                    <span class="badge badge-accent text-xs">Comunidad de miembros</span>
                    <span class="badge badge-ghost text-xs">Conocimiento gratuito</span>
                </div>
                <template #action>
                    <Link :href="route('cursos')" class="btn btn-secondary btn-sm">Ver Curso Holístico</Link>
                </template>
            </Hero>
        </Section>

        <!-- 2. ¿Dónde estamos? -->
        <Section>
            <EvaluacionSeccion seccion="¿Dónde estamos?" :idoneidad="75" :claridad="85" :composicion="80" :posicion="85" />
            <TextImage title="¿Donde estamos?" srcImage="/almacen/medios/paginas/mapa.jpg"
                buttonLabel="Ver Mapa" :href="route('contactos')" image-right>
                <p>Encuentra un centro o representante de Mundo Armónico TSEYOR cerca de ti.</p>
                <p>Estamos creciendo. Si aún no hay uno en tu zona, ¡anímate a crear uno! Únete a nuestro proyecto y te acompañaremos en el camino.</p>
                <Link :href="route('cursos')" class="btn btn-secondary btn-sm mt-2">Quiero unirme</Link>
            </TextImage>
        </Section>

        <!-- 3. ¿Quiénes son nuestros amigos del espacio? -->
        <Section>
            <EvaluacionSeccion seccion="Amigos del espacio" :idoneidad="85" :claridad="80" :composicion="75" :posicion="80" />
            <TextImage title="¿Quiénes son nuestros amigos del espacio?"
                srcImage="/almacen/medios/paginas/todos-los-guias.jpg"
                buttonLabel="Conoce a los guías Estelares" href="/guias">
                <p>Nuestros tutores proceden de distintos planetas y todos son miembros de la Confederación de Mundos Habitados de la Galaxia.</p>
                <p>Actualmente nos comunicamos con <Referencia colecciones="guias">Shilcars</Referencia> (un ser humano del planeta <Referencia colecciones="lugares">Agguniom</Referencia> en la <Referencia>Constelación de Áuriga</Referencia>) y otros hermanos como <Referencia>Melcor</Referencia>, <Referencia>Aumnor</Referencia> y <Referencia>Noiwanak</Referencia>.</p>
                <Link :href="route('comunicados')" class="btn btn-secondary btn-sm mt-2">Ver comunicados</Link>
            </TextImage>
        </Section>

        <!-- 4. Nuestra filosofía -->
        <Section>
            <EvaluacionSeccion seccion="Nuestra filosofía" :idoneidad="80" :claridad="85" :composicion="75" :posicion="75" />
            <TextImage title="Nuestra filosofía" srcImage="/almacen/medios/paginas/manos-entre-mundos.jpg"
                buttonLabel="Conoce nuestra filosofía" :href="route('filosofia')">
                <p>Practicamos la filosofía que nos ha patrocinado la Confederación de Mundos Habitados de la Galaxia, basada en la unidad en la diversidad, el hermanamiento y el Conocimiento Trascendente.</p>
                <p>Esta filosofía práctica nos permite conocernos a nosotros mismos, y transmutar lo necesario para lograr un equilibrio y una preparación para dar el Salto Cuántico con la llegada del Rayo Sincronizador.</p>
                <p>No se trata de creer por creer. Cada persona está invitada a experimentar, compartir, comprobar y extraer sus propias conclusiones.</p>
                <Link :href="route('cursos')" class="btn btn-secondary btn-sm mt-2">Curso gratuito</Link>
            </TextImage>
        </Section>

        <!-- 5. Comunicados recibidos telepáticamente -->
        <Section>
            <EvaluacionSeccion seccion="Comunicados" :idoneidad="85" :claridad="80" :composicion="70" :posicion="70" />
            <TextImage title="Comunicados recibidos telepáticamente"
                srcImage="/almacen/medios/paginas/Puente.jpg" buttonLabel="Ver Comunicados"
                href="/comunicados"
                image-right
                gridClass="md:grid-cols-[2fr_1fr]">
                <p>Los comunicados son las conversaciones interdimensionales realizadas telepáticamente por nuestro hermano Chac-Mool Puente, quien ha recibido una preparación desde hace más de 40 años para asumir dicha labor, como intermediario entre los dos mundos.</p>
                <p>En nuestra biblioteca web podéis encontrar todos los comunicados o conversaciones telepáticas interdimensionales mantenidas con los hermanos de la Confederación de Mundos Habitados de la Galaxia.</p>
                <p>Estos son comunicados grupales para crear sociedades sin líderes, sin piramidalismo vertical, para impulsar la auténtica libertad del individuo, sabiéndose el mismo participante de una Comunidad Universal.</p>
                <p>Los comunicados, libros PDF, audios y materiales relacionados están disponibles para libre consulta y descarga.</p>
                <Link :href="route('biblioteca')" class="btn btn-secondary btn-sm mt-2">Explorar biblioteca</Link>
            </TextImage>
        </Section>

        <!-- 6. Conocimiento gratuito y libre descarga -->
        <Section>
            <EvaluacionSeccion seccion="Conocimiento gratuito" :idoneidad="65" :claridad="75" :composicion="60" :posicion="65" />
            <div class="container mx-auto py-12">
                <h2 class="text-center mb-8">Conocimiento gratuito y libre descarga</h2>
                <div class="max-w-3xl mx-auto text-center space-y-4">
                    <p>Creemos en el acceso libre y gratuito al conocimiento. Por eso hemos recopilado libros PDF, comunicados, audios, meditaciones y otros materiales en una biblioteca web de fácil acceso para todos.</p>
                    <p>El acceso a estos materiales invita a la experimentación personal y a la reflexión libre, no a la aceptación dogmática.</p>
                </div>
                <div class="flex flex-wrap justify-center gap-4 mt-8">
                    <Link :href="route('biblioteca')" class="btn btn-primary">Explorar biblioteca</Link>
                    <Link :href="route('libros')" class="btn btn-secondary">Ver libros</Link>
                </div>
            </div>
        </Section>

        <!-- 7. Blog -->
        <Section>
            <EvaluacionSeccion seccion="Blog en quienes-somos" :idoneidad="70" :claridad="75" :composicion="65" :posicion="60" />
            <TextImage title="Blog"
                srcImage="/almacen/medios/paginas/blogs.jpg" buttonLabel="Leer Blog"
                href="/entradas" image-right>
                <p>Conoce algunas de nuestras aventuras en este camino del autorreconocimiento, podrás vernos en muchas fotografías de eventos que hemos realizado.</p>
                <Link :href="route('novedades')" class="btn btn-secondary btn-sm mt-2">Ver novedades</Link>
            </TextImage>
        </Section>

        <!-- 8. Testimonios de la comunidad -->
        <Section>
            <EvaluacionSeccion seccion="Testimonios" :idoneidad="60" :claridad="65" :composicion="65" :posicion="60" />
            <div class="container mx-auto py-12">
                <h2 class="text-center mb-8">Testimonios de la comunidad</h2>
                <p class="text-center text-gray-500 mb-8 max-w-2xl mx-auto">Personas de la comunidad comparten su experiencia en este camino de autodescubrimiento, hermanamiento y retroalimentación.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                    <div v-for="testimonio in testimonios" :key="testimonio.name" class="card bg-base-200 p-6 rounded-xl space-y-3">
                        <p class="italic text-sm">"{{ testimonio.text }}"</p>
                        <p class="font-bold text-sm">{{ testimonio.name }}</p>
                        <p class="text-xs text-gray-500">{{ testimonio.role }}</p>
                    </div>
                </div>
                <div class="flex justify-center gap-3 mt-8">
                    <Link :href="route('cursos')" class="btn btn-primary">Ver Curso Holístico</Link>
                    <Link href="/blog" class="btn btn-secondary">Leer blog</Link>
                </div>
            </div>
        </Section>

        <!-- 9. Preguntas más frecuentes -->
        <Section>
            <EvaluacionSeccion seccion="FAQ" :idoneidad="80" :claridad="75" :composicion="70" :posicion="70" />
            <TextText title="Preguntas más frecuentes" class="container lg:max-w-[1024px]"
                image-right
                srcImage="https://via.placeholder.com/700x600.png/0000ff?text=Puente.jpg" buttonLabel="Ver Preguntas"
                href="/preguntas-frecuentes">
                <template #text1>
                    <ul>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129901">¿Cómo son los seres de otros planetas?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129944">¿Cómo contactan?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129906">¿Cómo viven?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129919">¿Por qué están aquí?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129895">¿Cómo son sus naves?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129948">¿Cómo puedo comunicarme con ellos?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129928">¿Por qué no arreglan los problemas de nuestro mundo?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129956">¿Por qué no se presentan?</Link></li>
                    </ul>
                </template>
                <template #text2>
                    <p>En nuestra biblioteca web podéis encontrar todos los comunicados o conversaciones telepáticas interdimensionales mantenidas con los hermanos de la Confederación de Mundos Habitados de la Galaxia.</p>
                    <p>Estas y otras muchas preguntas ordenadas por temática puedes consultarlas en el libro-documento.</p>
                    <p class="flex justify-center mt-12">
                        <ActionButton href="/preguntas-frecuentes" class="btn btn-primary">Ver Preguntas</ActionButton>
                    </p>
                </template>
            </TextText>
        </Section>

        <!-- 10. ONG Mundo Armónico Tseyor -->
        <Section>
            <EvaluacionSeccion seccion="ONG" :idoneidad="80" :claridad="80" :composicion="75" :posicion="75" />
            <TextImage title="ONG Mundo Armónico Tseyor"
                srcImage="/almacen/medios/logos/ong.jpg" buttonLabel="Conoce la ONG"
                href="/ong" image-right>
                <p>Conoce nuestra ONG Mundo Armónico Tseyor, dedicada a la ayuda humanitaria en su vertiente material y espiritual.</p>
                <p>Todas sus actividades se desarrollan sin ánimo de lucro y desde la participación de sus miembros.</p>
                <Link :href="route('contactos')" class="btn btn-secondary btn-sm mt-2">Ver contactos</Link>
            </TextImage>
        </Section>

        <!-- 11. ¿Qué significa TSEYOR? -->
        <Section>
            <EvaluacionSeccion seccion="Significado TSEYOR" :idoneidad="75" :claridad="80" :composicion="75" :posicion="80" />
            <Hero title="¿Qué significa TSEYOR?" buttonLabel="Más información" href="/libros/quienes-somos-en-tseyor"
                src-width="600" src-height="400">
                <p class="text-center">Tseyor es un acrónimo que corresponde a:</p>
                <p class="text-center text-xl"><b>T</b>iempo <b>S</b>imbólico <b>E</b>stelar del <b>YO</b> en <b>R</b>etroalimentación</p>
                <p class="text-center">Y en él están incluidas miles de personas que anhelan el perfeccionamiento de su pensamiento.</p>
                <Link :href="route('cursos')" class="btn btn-secondary btn-sm mt-4">Curso Holístico</Link>
            </Hero>
        </Section>

        <!-- 12. Quiero ser de Tseyor -->
        <Section>
            <EvaluacionSeccion seccion="CTA final" :idoneidad="85" :claridad="90" :composicion="80" :posicion="85" />
            <Hero title="Quiero ser de Tseyor" buttonLabel="Curso Holístico"
                :href="route('cursos')" textClass="space-y-8">
                <p>Para entrar a formar parte de la comunidad de miembros de la ONG debes realizar nuestro Curso Holístico, de forma totalmente gratuita.</p>
                <Link :href="route('cursos.inscripcion.nueva')" class="btn btn-primary btn-lg mt-4">Inscríbete</Link>
            </Hero>
        </Section>

    </Sections>
</template>

<script setup>
import EvaluacionSeccion from "@/Components/EvaluacionSeccion.vue"
const testimonios = [
    {
        name: "Ana",
        role: "Alumna del curso",
        text: "El curso me ayudó a ordenar la filosofía y a encontrar respuestas que buscaba desde hace años.",
    },
    {
        name: "Carlos",
        role: "Miembro de la comunidad",
        text: "Encontré un espacio donde puedo explorar mi espiritualidad sin dogmas, a mi ritmo, y con personas afines.",
    },
    {
        name: "Marta",
        role: "Participante de encuentros",
        text: "La biblioteca me permitió investigar a mi ritmo antes de decidirme a hacer el curso. Todo es gratuito.",
    },
]
</script>
