<template>
    <Sections height="500">

        <Hero title="¿Quiénes Somos?"
            srcImage="/almacen/medios/paginas/quienes-somos.jpg"
            src-width="1600"
            src-height="1200"
            buttonLabel="Conoce nuestros orígenes"
            :href="route('origenes-de-tseyor')">
            <p>
Mundo Armónico Tseyor es una ONG dedicada al <Referencia>autodescubrimiento</Referencia> y a la creación de las <Referencia>Sociedades Armónicas</Referencia> en este planeta.
            </p>
            <p>
                Sus principios se basan en la experimentación de la <Referencia>filosofía cósmico crística</Referencia> que nos traen nuestros <Referencia>Guías Estelares</Referencia>, con quienes mantenemos conversaciones regulares con la <Referencia>Confederación de Mundos Habitados de la Galaxia</Referencia>.
            </p>
            <p>
                Esta es una <Referencia>filosofía viva</Referencia>, no dogmática, y plena de vigor espiritual y creativo, en estos <Referencia>tiempos de transformación</Referencia>.
            </p>
        </Hero>

        <Section>
            <TextImage title="¿Donde estamos?" srcImage="/almacen/medios/paginas/mapa.jpg"
            buttonLabel="Ver Mapa" :href="route('contactos')" image-right>
           <p>Encuentra un centro o representante de Mundo Armónico TSEYOR cerca de ti.</p>
           <p>Estamos creciendo. Si aún no hay uno en tu zona, ¡anímate a crear uno! Únete a nuestro proyecto y te acompañaremos en el camino.</p>
            </TextImage>
        </Section>



        <Section>
            <TextImage title="¿Quiénes son nuestros amigos del espacio?"
                srcImage="/almacen/medios/paginas/todos-los-guias.jpg"
                buttonLabel="Conoce a los guías Estelares" href="/guias"  >
                <p>Nuestros tutores proceden de distintos planetas y todos son miembros de la <Referencia>Confederación de Mundos Habitados de la Galaxia</Referencia>.</p>
                <p>Actualmente nos comunicamos con <Referencia colecciones="guias">Shilcars</Referencia> (un ser humano del planeta <Referencia colecciones="lugares">Agguniom</Referencia> en la <Referencia>Constelación de
                    Áuriga</Referencia>) y otros hermanos como <Referencia>Melcor</Referencia>, <Referencia>Aumnor</Referencia> y <Referencia>Noiwanak</Referencia>.</p>
            </TextImage>
        </Section>


         <Section>
            <TextImage title="El Rayo Sincronizador"
            src-image="/almacen/medios/portada/rayo_sincronizador.jpg"
            src-width="1024"
            src-height="1024"
            image-right
            href="/el-rayo-sincronizador"
            button-label="Más información"
            >
                <p>Nos estamos preparando para el próximo evento cósmico: El Rayo Sincronizador molecular. Una energía que catapultará nuestro universo manifestado a un nuevo nivel vibratorio. </p>
                <p>Este cambio se verá precedido por un conjunto innumerable de situaciones que deberemos afrontar todos juntos, en <Referencia>unidad</Referencia>, comunidad. </p>
                <p>Así podremos dar entrada a las futuras <Referencia>Sociedades Armónicas</Referencia>, en un nuevo nivel <Referencia r="vibración">vibracional</Referencia> y de consciencia.</p>
            </TextImage>
        </Section>


        <Section>
            <TextImage title="Nuestra filosofía" srcImage="/almacen/medios/paginas/manos-entre-mundos.jpg"
            buttonLabel="Conoce nuestra filosofía" :href="route('filosofia')">
                <p>Practicamos la filosofía que nos ha patrocinado la <Referencia>Confederación de Mundos Habitados de la Galaxia</Referencia>, basada en la <Referencia>unidad en la diversidad</Referencia>, el <Referencia>hermanamiento</Referencia> y el <Referencia>Conocimiento Trascendente</Referencia>.</p>
                <p>Esta filosofía práctica nos permite conocernos a nosotros mismos, y transmutar lo necesario para lograr un equilibrio y una preparación para dar el <Referencia>Salto Cuántico</Referencia> con la llegada del <Referencia>Rayo Sincronizador</Referencia>.</p>
            </TextImage>
        </Section>


        <Section>
            <!-- Sección: Comunicados -->
            <TextImage title="Comunicados recibidos telepáticamente"
                srcImage="/almacen/medios/paginas/Puente.jpg" buttonLabel="Ver Comunicados"
                href="/comunicados"
                image-right
                gridClass="md:grid-cols-[2fr_1fr]"
                >
                <p>Los comunicados son las <Referencia>conversaciones interdimensionales</Referencia> realizadas <Referencia r="telepatía">telepáticamente</Referencia> por nuestro hermano
                    <Referencia>Chac-Mool</Referencia> Puente, quien ha recibido una preparación desde
                    hace más de 40 años para asumir dicha labor, como intermediario entre los dos mundos.</p>
                <p>
                    En nuestra <Link href="biblioteca">biblioteca web</Link> podéis encontrar todos los comunicados o conversaciones
                    telepáticas interdimensionales mantenidas con los hermanos de la <Referencia>Confederación de Mundos Habitados de la
                    Galaxia</Referencia>.</p>
                    <p>Estos son comunicados grupales para crear sociedades sin <Referencia>líderes</Referencia>, sin piramidalismo vertical, para impulsar la <Referencia>auténtica libertad</Referencia> del invididuo, sabiéndose el mismo participante de una Comunidad Universal.</p>
            </TextImage>
        </Section>


          <!-- Sección: Blogs -->
          <Section>
            <TextImage title="Blog"
                srcImage="/almacen/medios/paginas/blogs.jpg" buttonLabel="Leer Blog"
                href="/entradas">
                <p>Conoce algunas de nuestras aventuras en este camino del autorreconocimiento, podrás vernos en muchas fotografías de eventos que hemos realizado.</p>
            </TextImage>
        </Section>


        <Section>
            <!-- Sección: Preguntas más frecuentes -->
            <TextText title="Preguntas más frecuentes" class="container lg:max-w-[1024px]"
            image-right
                srcImage="https://via.placeholder.com/700x600.png/0000ff?text=Puente.jpg" buttonLabel="Ver Comunicados"
                href="/comunicados">
                <template #text1>
                    <ul>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129901">¿Cómo son los seres de otros planetas?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129944">¿Cómo contactan?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129906">¿Cómo viven? </Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129919">¿Por qué están aquí?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129895">¿Cómo son sus naves?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129948">¿Cómo puedo comunicarme con ellos?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129928">¿Por qué no arreglan los problemas de nuestro mundo?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129956">¿Por qué no se presentan?</Link></li>
                    </ul>
                </template>
                <template #text2>
                    <p> En nuestra biblioteca web podéis encontrar todos los comunicados o conversaciones telepáticas
                        interdimensionales mantenidas con los hermanos de la Confederación de Mundos Habitados de la
                        Galaxia.</p>
                    <p> Estas y otras muchas preguntas ordenadas por temática puedes consultarlas en el libro-documento.</p>
                    <p class="flex justify-center mt-12">
                        <ActionButton href="/preguntas-frecuentes" class="btn btn-primary">Ver Preguntas</ActionButton>
                    </p>
                </template>
            </TextText>
        </Section>


        <!-- Sección: ONG -->
        <Section>
            <TextImage title="ONG Mundo Armónico Tseyor"
                srcImage="/almacen/medios/logos/ong.jpg" buttonLabel="Conoce la ONG"
                href="/ong" image-right>
                <p>Conoce nuestra ONG Mundo Armónico Tseyor, dedicada a la ayuda humanitaria en su vertiente
                    material y espiritual.</p>
            </TextImage>
        </Section>

                <!-- Sección: Qué significa TSEYOR -->
                <Section>
            <Hero title="¿Qué significa TSEYOR?" buttonLabel="Más información" href="/libros/quienes-somos-en-tseyor"
                src-width="600" src-height="400">
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


        <!-- Sección: Quiero ser de Tseyor -->
        <Hero title="Quiero ser de Tseyor" buttonLabel="Curso Holístico"
            :href="route('cursos')" textClass="space-y-8" >
            <p>Para entrar a formar parte de la comunidad debes realizar nuestro Curso Holístico, de forma totalmente gratuita.</p>
            </Hero>


    </Sections>
</template>


<script setup>


</script>
