<template>
    <AppLayout :title="centro.nombre">
        <div class="container mx-auto px-4 py-8">

            <div class="flex justify-between items-center mb-20">
                <Back>Centros Tseyor</Back>
                <AdminLinks modelo="centro" necesita="administrar directorio" :contenido="centro" />
            </div>

            <TextText :srcImage="centro.imagen" cover full>
                <template #text1>
                    <!-- Imágenes de los Muulasterios -->
                    <Carousel :per-page="1" autoplay autoplayTimeout="4000" autoplayHoverPause :loop="true"
                        paginationActiveColor="#0088ff" paginationColor="gray">
                        <Slide v-for="imagen in imagenes" :key="imagen">
                            <div :style="`background-image: url(${getImageUrl(imagen)}); background-repeat: no-repeat; background-size: contain; background-position: top center;`"
                                class="block w-full h-[400px] lg:h-[60vh]"></div>
                        </Slide>
                    </Carousel>
                </template>
                <template #text2>

                    <Card class="text-left p-8 lg:p-14">
                        <h1>{{ centro.nombre }}</h1>
                        <h3 class="">{{ centro.poblacion }}, {{ centro.pais }}</h3>
                        <div class="text-right text-xs">
                            Última actualización:
                            <TimeAgo :date="centro.updated_at" />
                        </div>

                        <Content :content="centro.descripcion" class="mt-12" />

                        <div v-if="centro.contacto" class="flex justify-end mt-12">
                            <Link :href="route('contacto', centro.contacto?.slug)" class="btn btn-primary">Ver Ficha de
                            Contacto
                            </Link>
                        </div>
                    </Card>
                </template>
            </TextText>

            <div v-if="entradas.length" class="mt-20">
                <h2 class="text-center md:text-left">Artículos de Blog relacionados</h2>
                <GridAppear col-width="24rem" class="gap-4">
                    <CardContent v-for="entrada in entradas" :key="entrada.slug" image-left :title="entrada.titulo"
                        :image="entrada.imagen" :href="route('entrada', entrada.slug)"
                        :description="entrada.descripcion" :date="entrada.updated_at" />
                </GridAppear>
            </div>


            <div v-if="libros.length" class="my-20">
                <h2 class="text-center md:text-left">Libros relacionados</h2>
                <GridAppear col-width="200px" class="gap-6  place-items-center md:place-items-start">
                    <Link v-for="libro in libros" :key="libro.slug" :href="route('libro', libro.slug)">
                    <Image :src="libro.imagen" class="w-[200px] h-[300px]" />
                    </Link>
                </GridAppear>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { Carousel, Slide } from '@jambonn/vue-concise-carousel';
import '@jambonn/vue-concise-carousel/lib/vue-concise-carousel.css'
import { getImageUrl } from '@/composables/imageutils.js'

const props = defineProps({
    centro: {
        type: Object,
        required: true,
    },
    imagenes: Array,
    libros: Array,
    entradas: Array
});
</script>
