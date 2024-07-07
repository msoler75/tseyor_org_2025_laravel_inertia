<template>
    <div class="container py-12 mx-auto">

        <div class="flex justify-between items-center mb-7">
            <Back :href="route('lugares')" inline>Lugares</Back>
            <Link href="/libros/glosario-terminologico" class="btn btn-sm btn-primary flex gap-2 items-center"
            title='Descarga todo el glosario en pdf'>
            <Icon icon="ph:download-duotone" />Descargar libro</Link>
            <AdminPanel modelo="lugar" necesita="administrar contenidos" :contenido="lugar" />
        </div>

        <div class="mx-auto flex flex-col justify-center items-center">
            <h1>Glosario</h1>
            <GlosarioTabs />
        </div>

        <div class="flex justify-end mb-5">
            <SearchInput :doSearch="false" @search="buscarClick" />
        </div>

        <ContentBar>
            <div class="w-full flex gap-2 items-center justify-between">
                <Back :href="route('lugares')" inline>Lugares</Back>
                <div @click="useNav().scrollToTopPage" class="flex items-center gap-2 font-bold">Glosario
                    <Icon icon="ph:arrow-circle-up-duotone" class="transform scale-150" />
                </div>
                <Back :href="route('lugares')" inline class="pointer-events-none opacity-0">Lugares</Back>
            </div>
        </ContentBar>

        <ContentMain class="flex flex-wrap lg:flex-nowrap gap-10">
            <div class="w-full max-w-[350px] mx-auto lg:max-w-full lg:w-1/3">
                <div class="w-full h-auto mx-auto">
                    <Image :src="lugar.imagen" :alt="lugar.nombre" class="w-full h-auto" />
                </div>
            </div>

            <div class="w-full lg:w-[60%] flex-shrink-0 text-left">
                <h1 class="text-center lg:text-left">{{ lugar.nombre }}</h1>
                <p class="text-gray-600 text-xs my-5 text-right">
                    Última actualización:
                    <TimeAgo :date="lugar.updated_at" />
                </p>
                <div class="mb-4"></div>
                <tabs :options="{ disableScrollBehavior: true }">
                    <tab v-if="lugar.descripcion || lugar.texto" name="Descripción">
                        <div v-if="lugar.texto" class="prose" v-html="lugar.texto"></div>
                        <div v-else class="prose">{{ lugar.descripcion }}</div>
                    </tab>

                    <tab v-if="lugar.libros" name="Bibliografía">
                        <div v-if="lugar.libros" class="flex flex-wrap gap-4 justify-center">
                            <Link v-for="libro, index of libros" :key="index"
                                :href="route('libro', libro.slug)" class="w-[180px]">
                                <Image :src="libro.imagen+'?w=180'"/>
                                <div class="mt-1 text-sm text-center font-bold">{{libro.titulo}}</div>
                            </Link>
                        </div>
                    </tab>

                    <!-- ... relacionados -->

                </tabs>
            </div>

        </ContentMain>

        <hr class="my-12" />

        <div class="card bg-base-100 shadow flex-wrap flex-row p-5 lg:p-10 gap-4">
            <div v-for="item of lugares" :key="item.slug" class="flex gap-2">
                <Link v-show="item.slug != lugar.slug" :href="route('lugar', item.slug)">
                <span class="capitalize">{{ item.nombre }}</span>
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Tabs, Tab } from 'vue3-tabs-component';
import AppLayout from '@/Layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout })

const props = defineProps({
    lugar: {
        type: Object,
        required: true,
    },
    lugares: {
        type: Array,
        required: true
    },
    libros: {
        type: Array,
        required: true
    }
});

function buscarClick(query) {
    router.visit(route('lugares') + '?buscar=' + query)
}

</script>
