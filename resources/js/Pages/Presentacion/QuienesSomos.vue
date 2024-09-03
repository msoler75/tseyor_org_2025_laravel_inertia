<template>
    <Sections height="500">

        <Hero title="¿Quiénes Somos?" subtitle="Mundo Armónico Tseyor es una ONG dedicada al autodescubrimiento y a la formación de las Sociedades Armónicas. Sus principios se basan en el contacto extraterrestre, manteniendo conversaciones regulares con la Confederación de Mundos Habitados de la Galaxia."
            srcImage="/almacen/medios/paginas/quienes-somos.jpg" image-right
            buttonLabel="Conoce nuestros orígenes"
            :href="route('origenes-de-tseyor')">
        </Hero>

        <Section>
            <TextImage title="¿Donde estamos?" srcImage="/almacen/medios/paginas/mapa.jpg"
            buttonLabel="Ver Mapa" :href="route('contactos')" >
                <p>Encuentra alguno de nuestros centros o representantes cerca de ti.</p>
            </TextImage>
        </Section>



        <Section>
            <TextImage title="¿Con quiénes contactamos?"
                srcImage="/almacen/medios/paginas/todos-los-guias.jpg"
                buttonLabel="Conoce a los guías Estelares" href="/guias" image-right >
                <p>Nuestros tutores proceden de distintos planetas y todos son miembros de la <Referencia>Confederación de Mundos Habitados de la Galaxia</Referencia>.</p>
                <p>Actualmente nos comunicamos con <Referencia colecciones="guias">Shilcars</Referencia> (un ser humano del <Referencia colecciones="lugares">planeta Agguniom</Referencia> en la <Referencia>Constelación de
                    Áuriga</Referencia>) y otros hermanos como <Referencia>Melcor</Referencia>, <Referencia>Aumnor</Referencia> y <Referencia>Noiwanak</Referencia>.</p>
            </TextImage>
        </Section>


        <Section>
            <TextImage title="Nuestra filosofía" srcImage="/almacen/medios/paginas/manos-entre-mundos.jpg"
            buttonLabel="Conoce nuestra filosofía" :href="route('filosofia')" >
                <p>Seguimos la filosofía que nos ha patrocinado la <Referencia>Confederación de Mundos Habitados de la Galaxia</Referencia>, basada en la unión, la hermandad y el <Referencia>Conocimiento Trascendente</Referencia>.</p>
            </TextImage>
        </Section>


        <Section>
            <!-- Sección: Comunicados -->
            <TextImage title="Comunicados recibidos telepáticamente"
                srcImage="/almacen/medios/paginas/Puente.jpg" buttonLabel="Ver Comunicados"
                href="/comunicados"
                gridClass="md:grid-cols-[2fr_1fr]"
                image-right>
                <p>Los comunicados son las <Referencia>conversaciones interdimensionales</Referencia> realizadas <Referencia r="telepatía">telepáticamente</Referencia> por nuestro hermano
                    <Referencia>Chac-Mool Puente</Referencia>, quien ha recibido una preparación desde
                    hace más de 40 años para asumir dicha labor, como intermediario entre los dos mundos.</p>
                <p>
                    En nuestra <Referencia>biblioteca web</Referencia> podéis encontrar todos los comunicados o conversaciones
                    telepáticas interdimensionales mantenidas con los hermanos de la <Referencia>Confederación de Mundos Habitados de la
                    Galaxia</Referencia>.</p>
            </TextImage>
        </Section>


          <!-- Sección: Blogs -->
          <Section>
            <TextImage title="Blog"
                srcImage="/almacen/medios/paginas/blogs.jpg" buttonLabel="Leer Blog"
                href="/entradas" >
                <p>Conoce algunas de nuestras aventuras en este camino del autorreconocimiento, podrás vernos en muchas fotografías de eventos que hemos realizado.</p>
            </TextImage>
        </Section>


        <Section>
            <!-- Sección: Preguntas más frecuentes -->
            <TextText title="Preguntas más frecuentes" class="container lg:max-w-[1024px]"
                srcImage="https://via.placeholder.com/700x600.png/0000ff?text=Puente.jpg" buttonLabel="Ver Comunicados"
                href="/comunicados">
                <template #text1>
                    <p>
                    <ul>
                        <Li><Link href="/preguntas-frecuentes/1#_Toc301129944">¿Cómo contactan?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129901">¿Cómo son los seres de otros planetas?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129906">¿Cómo viven? </Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129919">¿Por qué están aquí?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129895">¿Cómo son sus naves?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129948">¿Cómo puedo comunicarme con ellos?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129928">¿Por qué no arreglan los problemas de nuestro mundo?</Link></li>
                        <li><Link href="/preguntas-frecuentes/1#_Toc301129956">¿Por qué no se presentan?</Link></li>
                    </ul>
                    </p>
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
        <Hero title="Quiero ser de Tseyor" subtitle="Entra a forma parte del grupo" buttonLabel="Formulario de inscripción"
            :href="route('cursos.inscripcion.nueva')" textClass="space-y-8" />


    </Sections>
</template>


<script setup>


</script>
