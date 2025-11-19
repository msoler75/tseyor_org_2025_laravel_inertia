<template>
    <Sections white-first>
        <div>
            <div class="container w-full mx-auto mt-4 sm:mt-12 flex justify-end mb-10">
                <span />
                <Share />
            </div>
        </div>

        <Section>
            <TextImage title="Universidad Tseyor de Granada" srcImage="/almacen/medios/logos/utg.jpg" srcWidth="819"
                srcHeight="1191" image-right buttonLabel="Ver Estatutos" :href="estatutosUrl" :image-lazy="false">
                <Prose>
                    <blockquote>
                        <p>
                            La Universidad Tseyor de Granada mantendrá comunicación, información e interrelación con los
                            distintos Muulasterios en todo el mundo, las casas Tseyor y los pueblos Tseyor también. Y
                            será una fuente en la que todo Muul podrá acceder de pleno derecho.
                        </p>
                    </blockquote>
                </Prose>
            </TextImage>
        </Section>

        <Section class="pb-10">
            <TextText class="container" title="">
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
            <div class="container text-right">
                <div>Shilcars, Comunicado interdimensional 429</div>
            </div>
        </Section>


        <Section class="py-14">
            <div class="container space-y-12 py-12">
                <h2>Espacios de los Equipos y Departamentos</h2>
                <span>A continuación encontraréis algunos de los equipos y departamentos de la UTG. El listado completo
                    está en
                    los
                    <Link :href="estatutosUrl" class="text-primary hover:underline">estatutos de la UTG</Link> en el
                    apartado
                    4.1
                </span>
                <GridFill colWidth="16rem" class="gap-4 my-8">
                    <CardContent
                    v-for="departamento of departamentos"
                    :key="departamento.nombre"
                    :href="route('utg.departamento', departamento.slug)"
                    :image="departamento.imagen"
                        :title="departamento.nombre"/>
                </GridFill>
                <div class="flex justify-end">
                    <Link :href="route('utg.departamentos')" class="flex gap-3 items-center">
                    <Icon icon="ph:arrow-fat-line-right-duotone" />Ver todos los Equipos</Link>
                </div>
            </div>
        </Section>


        <SectionBook
            titulo="Libro: UTG Universidad Tseyor de Granada"
            slug="la-universidad-tseyor-de-granada"
            portada="/almacen/medios/libros/249/La%20Universidad%20Tseyor%20de%20Granada.jpg"
            enlace="/libros/la-universidad-tseyor-de-granada"
            buttonLabel="Ver en Biblioteca">
            <p>Todo el material recopilado de la UTG desde su creación.</p>
        </SectionBook>



    </Sections>
</template>

<script setup>


defineProps({
    departamentos: {},
    estatutosUrl: {}
})
</script>
