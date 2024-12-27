<template>

    <div class="py-4 md:py-12 w-full container mx-auto">

        <div class="flex justify-between mb-20">
            <span />
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="centro" necesita="administrar directorio"  />
            </div>
        </div>

        <!-- Encabezado de la página -->
        <header v-if="false" class="my-12">
            <div class="px-2 sm:px-5">
                <h1 class="text-4xl font-bold mb-2">Centros Tseyor</h1>
                <p class="text-lg">Descubre nuestros lugares de hermandad y paz interior.</p>
            </div>
        </header>

        <Hero title="Centros Tseyor" subtitle="Descubre nuestros lugares de hermandad y paz interior."
            buttonLabel="Ver Mapa" :href="route('contactos')" />

        <div class="flex flex-col lg:flex-row gap-12 justify-center relative">
            <div class="mx-auto max-w-[800px] space-y-12">
                <!-- Sección de Muulasterios Tseyor -->
                <section class="card bg-base-100 shadow p-7">
                    <div class="mx-auto px-2 sm:px-5">
                        <h2 class="">Muulasterios Tseyor</h2>
                        <p class="text-lg mb-8">Los Muulasterios Tseyor son nuestros centros energéticos más
                            destacados. Son
                            lugares para la meditación, el equilibrio y la paz interior.</p>
                        <!-- Imágenes de los Muulasterios -->
                        <Carousel :per-page="1">
                            <Slide v-for="centro in muulasterios" :key="centro.id">
                                <Link :href="route('centro', centro.id)"
                                    :style="`background-image: url(${getImageUrl(centro.imagen)}); background-size: cover; background-position: center center;`"
                                    class="block w-full h-[260px] md:h-[45vh]">
                                </Link>
                            </Slide>
                        </Carousel>
                    </div>
                </section>

                <!-- Sección de Casas Tseyor -->
                <section class="card bg-base-100 shadow p-7 ">
                    <div class=" mx-auto px-2 sm:px-5">
                        <h2 class="">Casas Tseyor</h2>
                        <p class="text-lg mb-8">Nuestras Casas Tseyor también forman parte de los Pueblos Tseyor.
                            Son
                            espacios para la hermandad y el autodescubrimiento.</p>

                        <!-- Imágenes de las Casas -->
                        <Carousel :per-page="1">
                            <Slide v-for="centro in casas" :key="centro.id">
                                <div :style="`background-image: url(${getImageUrl(centro.imagen)}); background-size: cover; background-position: center center;`"
                                    class="w-full h-[260px] md:h-[45vh]"></div>
                            </Slide>
                        </Carousel>
                    </div>
                </section>

                <!-- Sección de Casas Tseyor -->
                <section class="card bg-base-100 shadow p-7 ">
                    <h2 class="">Mapa de Centros</h2>
                    <div class="flex flex-wrap justify-between gap-7 items-center">
                        <p>Consulta si tienes un centro Tseyor cerca de ti.</p>
                        <Link :href="route('contactos')" class="btn btn-primary w-fit">Ver Mapa</Link>
                    </div>
                </section>


                <!-- Sección de Blogs -->
                <section class="card bg-base-100 shadow p-7 ">
                    <h2>Blog de Pueblo Tseyor</h2>
                    <div class="flex flex-wrap sm:flex-nowrap justify-between gap-7 items-center">
                        <p>En nuestro blog podrás conocer algunas de las experiencias que hemos vivido.</p>
                        <Link :href="`${route('entradas')}?categoria=pueblo tseyor`" class="btn btn-primary w-fit">
                        Visitar Blog
                        </Link>
                    </div>
                </section>

            </div>

            <section class="card bg-base-100 shadow p-7 lg:w-[460px] self-baseline sticky top-20">
                <div class="flex flex-col px-2 sm:px-5">
                    <h2>Nuestros centros</h2>
                    <div class="flex flex-wrap lg:grid grid-cols-2 gap-10">
                        <div v-for="pais of paises" :key="pais.nombre" class="flex gap-2">
                            <Link :href="`${route('contactos')}?pais=${pais.codigo}`">
                            <span class="capitalize">{{ pais.nombre }}</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>




        </div>

    </div>
</template>



<script setup>
import { Carousel, Slide } from '@jambonn/vue-concise-carousel';
import '@jambonn/vue-concise-carousel/lib/vue-concise-carousel.css'
import { getImageUrl } from '@/composables/imageutils';

const props = defineProps({
    casas: {
        default: () => []
    },
    muulasterios: {
        default: () => []
    },
    paises: {
        default: () => []
    }
});

</script>
