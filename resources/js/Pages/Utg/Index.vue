<template>
    <Sections white-first>

        <Section :height="screen && screen.width < 768 ? '400px' : '550px'">
            <TextImage title="Universidad Tseyor de Granada"
                srcImage="https://tseyor.org/images/sello-utg.webp?ts=1433235750&thumb=299x435&quality=87&fe=jpg"
                image-right buttonLabel="Ver Estatutos" href="/estatutos/utg">
                <Prose>
                    <blockquote>
                        <p>
                            La Universidad Tseyor de Granada mantendrá comunicación, información e interrelación con los
                            distintos Muulasterios en todo el mundo, las casas Tseyor y los pueblos Tseyor también. Y será una
                            fuente en la que todo Muul podrá acceder de pleno derecho.
                        </p>
                    </blockquote>
                </Prose>
            </TextImage>
        </Section>

        <Section class="pb-10">
            <TextText>
                <template #text1>
                    <blockquote>
                        <p>
                            La Universidad Tseyor de Granada, podra incluir entre sus archivos trabajos especializados en
                            diferentes temas, que se vayan generando, ya sea a nivel científico, tecnológico, filosófico,
                            de salud, alimentacion etc.., que los distintos miembros de Tseyor vayan recopilando, vayan
                            generando.
                            Y, a través de la bondan que anida en sus corazones, sepan depositarlo con la total confianza en
                            estos archivos que respetaran siempre la integridad de los trabajos que se hayan realizado.
                        </p>
                    </blockquote>
                </template>
                <template #text2>
                    <blockquote>
                        <p>
                            La Universidad Tseyor en Granada cuidará de todo el patrimonio bibliográfico de Tseyor, del
                            presente y del futuro patrimonio que se generará en estos años venideros, que será mucho
                            patrimonio. Y, por lo tanto, habrá que darle el cauce adecuado para la debida organización y
                            mantenimiento de la pureza de los mensajes transmitidos, velando para que los mismos no puedan
                            ser modificados ni alterados.
                        </p>
                    </blockquote>
                </template>
            </TextText>
            <div class="container mx-auto text-right">
                <div>Shilcars, Comunicado interdimensional 429</div>
            </div>
        </Section>


        <Section class="py-14">
            <div class="container mx-auto space-y-12">
                <h2>Departamentos</h2>
                <GridFill w="24rem">
                    <div v-for="departamento of departamentos" :key="departamento.nombre" class="flex gap-3 items-start">
                        <div class="w-14 h-14 overflow-hidden">
                            <Image :src="'/storage/' + departamento.imagen"/>
                        </div>
                        <div class="flex flex-col gap-4">
                            <Link :href="route('utg.departamento', departamento.slug)" class="font-bold text-lg">{{
                                departamento.nombre }}</Link>
                            <span>{{ departamento.descripcion }}</span>
                        </div>
                    </div>
                </GridFill>
                <div class="flex justify-end">
                    <Link :href="route('utg.departamentos')" class="flex gap-3 items-center"><Icon icon="ph:arrow-fat-line-right-duotone"/>Ver todos los Departamentos</Link>
                </div>
            </div>
        </Section>


        <Section>
            <TextImage title="Libro: UTG Universidad Tseyor de Granada"
                srcImage="https://via.placeholder.com/300x400.png/0000ff?text=UTG.jpg" buttonLabel="Descargar libro"
                href="/ong">
                <p>Todo el material recopilado de la UTG desde su creación.</p>
            </TextImage>
        </Section>



    </Sections>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

defineProps({
    departamentos: {}
})
</script>
