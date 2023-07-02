<template>
    <Sections class="white-first">

        <Section>
            <TextImage title="Universidad Tseyor de Granada"
                srcImage="https://tseyor.org/images/sello-utg.webp?ts=1433235750&thumb=299x435&quality=87&fe=jpg"
                image-right buttonLabel="Ver Estatutos" href="/estatutos">


            </TextImage>
        </Section>

        <Section>
            werwer
            <TextText>
                <template #text1>
                    <blockquote>
                        <p>
                            La Universidad Tseyor de Granada, podra incluir entre sus archivos trabajos especializados en
                            diferentes temas , que se vayan generando, ya sea a nivel científico, tecnológico, filosófico, de
                            salud, alimentacion etc.., que los distintos miembros de Tseyor vayan recopilando, vayan generando.
                            Y, a través de la bondan que anida en sus corazones, sepan depositarlo con la total confianza en
                            estos archivos que respetaran siempre la integridad de los trabajos que se hayan realizado.
                        </p>
                    </blockquote>
                </template>
                <template #text2>
                    <blockquote>
                        <p>
                            La Universidad Tseyor en Granada cuidará de todo el patrimonio bibliográfico de Tseyor, del presente
                            y del futuro patrimonio que se generará en estos años venideros, que será mucho patrimonio. Y, por
                            lo tanto, habrá que darle el cauce adecuado para la debida organización y mantenimiento de la pureza
                            de los mensajes transmitidos, velando para que los mismos no puedan ser modificados ni alterados.
                        </p>
                    </blockquote>
                </template>
            </TextText>
            <p>Shilcars, Comunicado interdimensional 429</p>
        </Section>


        <Section>
            <Text>
                <h2>Fundamentos de la Universidad Tseyor de Granada</h2>

                <p class="flex justify-end">
                    <a href="1.pdf" class="btn btn-primary" download>Descargar Estatutos de la ONG</a>
                </p>
            </Text>
        </Section>


        <Section>
            <TextImage title="Libro: ONG Mundo Armónico Tseyor"
                srcImage="https://via.placeholder.com/300x400.png/0000ff?text=ONG.jpg" buttonLabel="Descargar libro"
                href="/ong">
                <p>Todo el material recopilado de la ONG de Tseyor.</p>
            </TextImage>
        </Section>

        <Section>
            <TextImage title="Muular" srcImage="https://via.placeholder.com/1200x400.png/0000ff?text=Muular"
                buttonLabel="Conoce el Muular" :href="route('muular')" image-right>
                <p>La moneda social y espiritual de Tseyor</p>
            </TextImage>
        </Section>


    </Sections>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })
</script>
