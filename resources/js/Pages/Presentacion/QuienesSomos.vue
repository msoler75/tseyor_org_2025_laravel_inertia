<template>
    <Sections>

        <Hero title="¿Quiénes Somos?" subtitle="TSEYOR es un grupo de contacto extraterrestre que mantiene conversaciones regulares con seres
                        humanos del cosmos pertenecientes a la Confederación de Mundos Habitados de la Galaxia."
            srcImage="https://via.placeholder.com/1600x1000.png/00088f?text=quienes-somos.jpg" image-right
            buttonLabel="Conoce nuestros orígenes"
            :href="route('origenes-de-tseyor')">
        </Hero>

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
            <TextImage title="Nuestra filosofía" srcImage="https://via.placeholder.com/1263x569.png/000fdd?text=Filosofía"
            buttonLabel="Conoce nuestra filosofía" :href="route('filosofia')">
                <p>Seguimos la filosofía nos ha patrocinado la Confederación de Mundos Habitados de la Galaxia.</p>
            </TextImage>
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

          <!-- Sección: ONG -->
          <Section>
            <TextImage title="Blog"
                srcImage="https://via.placeholder.com/700x600.png/0000ff?text=ONG.jpg" buttonLabel="Leer Blog"
                href="/blog">
                <p>En nuestros blog podrás ver algunas de nuestras actividades que hemos ido realizando todos estos años.</p>
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
