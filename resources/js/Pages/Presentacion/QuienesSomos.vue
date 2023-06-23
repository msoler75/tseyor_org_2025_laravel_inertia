<template>
    <Sections>

        <Hero title="¿Quiénes Somos?" subtitle="TSEYOR es un grupo de contacto extraterrestre que mantiene conversaciones regulares con seres
                        humanos del cosmos pertenecientes a la Confederación de Mundos Habitados de la Galaxia."
            srcImage="https://via.placeholder.com/1600x1000.png/00088f?text=quienes-somos.jpg" image-right></Hero>


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
            <TextImage title="¿Donde estamos?" srcImage="https://via.placeholder.com/1263x569.png/000fdd?text=Mapamundi"
            buttonLabel="Ver Mapa" :href="route('contactos')">
                <p>Encuentra alguno de nuestros centros o representantes cerca de ti.</p>
            </TextImage>
        </Section>

        <Section>
            <TextImage title="¿Con quiénes contactamos?"
                srcImage="https://via.placeholder.com/363x569.png/028f0d?text=contacto-estelar"
                buttonLabel="Conoce a los guías Estelares" href="/guias" image-right>
                <p>Nuestros tutores proceden de distintos planetas y todos son miembros de la Confederación.</p>
                <p>Actualmente nos comunicamos con Shilcars (un ser humano del planeta Agguniom en la Constelación de
                    Áuriga) y otros hermanos como Melcor, Aumnor y Noiwanak.</p>
            </TextImage>
        </Section>

        <Section>
            <!-- Sección: Los guías estelares -->
            <TextImage title="Libro: Los Guías Estelares" subtitle="Libro que recoge toda la historia del grupo e información detallada de cada guía estelar de
                            TSEYOR." srcImage="https://via.placeholder.com/700x1000.png/0000ff?text=guia-estelar.jpg"
                buttonLabel="Descargar libro" href="/libro" />
        </Section>

        <Section>
            <!-- Sección: Comunicados -->
            <TextImage title="Comunicados recibidos telepáticamente"
                srcImage="https://via.placeholder.com/700x600.png/0000ff?text=Puente.jpg" buttonLabel="Ver Comunicados"
                href="/comunicados"
                image-right >
                <p>Los comunicados son las conversaciones interdimensionales realizadas telepáticamente por nuestro hermano
                    Chac-Mool Puente, quien ha recibido una preparación desde
                    hace más de 40 años para asumir dicha labor, como intermediario entre los dos mundos.</p>
                <p>
                    En nuestra biblioteca web podéis encontrar todos los comunicados o conversaciones
                    telepáticas interdimensionales mantenidas con los hermanos de la Confederación de Mundos Habitados de la
                    Galaxia.</p>
            </TextImage>
        </Section>


        <Section>
            <!-- Sección: Preguntas más frecuentes -->
            <TextText title="Preguntas más frecuentes"
                srcImage="https://via.placeholder.com/700x600.png/0000ff?text=Puente.jpg" buttonLabel="Ver Comunicados"
                href="/comunicados">
                <template #text1>
                    <p>
                    <ul>
                        <li>¿Cómo contactan?</li>
                        <li>¿Cómo son los seres de otros planetas? </li>
                        <li>¿Cómo viven? </li>
                        <li>¿Por qué están aquí?</li>
                        <li>¿Cómo son sus naves?</li>
                        <li>¿Cómo puedo comunicarme con ellos?</li>
                        <li>¿Por qué no arreglan los problemas de nuestro mundo?</li>
                        <li>¿Por qué no se presentan?</li>
                    </ul>
                    </p>
                </template>
                <template #text2>
                    <p> En nuestra biblioteca web podéis encontrar todos los comunicados o conversaciones telepáticas
                        interdimensionales mantenidas con los hermanos de la Confederación de Mundos Habitados de la
                        Galaxia.</p>
                    <p> Estas y otras muchas preguntas ordenadas por temática puedes consultarlas en el libro-documento.</p>
                    <p class="flex justify-center mt-12">
                        <Link href="/libro-documento" class="btn btn-primary">Ver Preguntas</Link>
                    </p>
                </template>
            </TextText>
        </Section>


        <!-- Sección: ONG -->
        <Section>
            <TextImage title="ONG Mundo Armónico Tseyor"
                srcImage="https://via.placeholder.com/700x600.png/0000ff?text=ONG.jpg" buttonLabel="Conoce la ONG"
                href="/ong" image-right>
                <p>Conoce nuestra ONG Mundo Armónico Tseyor, dedicada a la ayuda humanitaria en su vertiente
                    material y espiritual.</p>
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


        <!-- Sección: Quiero ser de Tseyor -->
        <Hero title="Quiero ser de Tseyor" subtitle="Entra a forma parte del grupo" buttonLabel="Formulario de inscripción"
            href="/inscripcion" textClass="space-y-8" />
    </Sections>
</template>


<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

</script>
