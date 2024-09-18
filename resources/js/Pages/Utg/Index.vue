<template>
    <Sections white-first>

        <Section :height="screen && screen.width < 768 ? '400px' : '550px'" style="--sectionHeight: 500px">
            <TextImage title="Universidad Tseyor de Granada" srcImage="/almacen/medios/logos/utg.jpg" srcWidth="819"
                srcHeight="1191" image-right buttonLabel="Ver Estatutos" href="/utg/estatutos" :image-lazy="false">
                <Prose>
                    <blockquote>
                        <p>
                            La Universidad Tseyor de Granada mantendrá comunicación, información e interrelación con los
                            distintos Muulasterios en todo el mundo, las casas Tseyor y los pueblos Tseyor también. Y
                            será una
                            fuente en la que todo Muul podrá acceder de pleno derecho.
                        </p>
                    </blockquote>
                </Prose>
            </TextImage>
        </Section>

        <Section class="pb-10">
            <TextText class="container">
                <template #text1>
                    <blockquote>
                        <p>
                            La Universidad Tseyor de Granada, podra incluir entre sus archivos trabajos especializados
                            en
                            diferentes temas, que se vayan generando, ya sea a nivel científico, tecnológico,
                            filosófico,
                            de salud, alimentacion etc.., que los distintos miembros de Tseyor vayan recopilando, vayan
                            generando.
                            Y, a través de la bondan que anida en sus corazones, sepan depositarlo con la total
                            confianza en
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
                            mantenimiento de la pureza de los mensajes transmitidos, velando para que los mismos no
                            puedan
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
            <div class="container mx-auto space-y-12 py-12">
                <h2>Departamentos</h2>
                <GridFill w="24rem">
                    <Link :href="route('utg.departamento', departamento.slug)"
                        class="group font-bold text-lg flex gap-3 items-start" v-for="departamento of departamentos"
                        :key="departamento.nombre">
                    <div class="overflow-hidden flex-shrink-0">
                        <Image :src="departamento.imagen" width="100" />
                    </div>
                    <div class="transition duration-250 text-primary group-hover:text-secondary">
                        {{ departamento.nombre }}
                    </div>
                    </Link>
                </GridFill>
                <div class="flex justify-end">
                    <Link :href="route('utg.departamentos')" class="flex gap-3 items-center">
                    <Icon icon="ph:arrow-fat-line-right-duotone" />Ver todos los Departamentos</Link>
                </div>
            </div>
        </Section>


        <Section class="py-14">
            <TextImage title="Libro: UTG Universidad Tseyor de Granada" buttonLabel="Descargar libro" class="container"
                href="/libros/la-universidad-tseyor-de-granada">
                <p>Todo el material recopilado de la UTG desde su creación.</p>

                <template #image>
                    <Libro3d
                        :libro="{ slug: 'la-universidad-tseyor-de-granada', imagen: '/almacen/medios/libros/249/La%20Universidad%20Tseyor%20de%20Granada.jpg' }"
                        imageClass="w-[200px]" />
                </template>
            </TextImage>
        </Section>



    </Sections>
</template>

<script setup>


defineProps({
    departamentos: {}
})
</script>
