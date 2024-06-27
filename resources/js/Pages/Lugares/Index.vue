
<template>
    <div class="container py-12 mx-auto">


        <div class="flex justify-between items-center mb-7">
            <span/>
            <Link href="/libros/glosario-terminologico" class="flex gap-2 items-center" title='Descarga todo el glosario en pdf'><Icon icon="ph:download-duotone" />Descargar</Link>
            <AdminPanel modelo="lugar" necesita="administrar contenidos" />
        </div>


        <div class="mx-auto flex flex-col justify-center items-center">
            <h1>Glosario</h1>
            <GlosarioTabs />
        </div>


        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>

        <ContentBar>
            <div class="w-full flex gap-2 items-center justify-between">
                <span />
                <div @click="useNav().scrollToTopPage" class="flex items-center gap-2 font-bold">Glosario
                    <Icon icon="ph:arrow-circle-up-duotone" class="transform scale-150" />
                </div>
                <span />
            </div>
        </ContentBar>

        <ContentMain class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="w-full flex-grow">

                <GridAppear class="gap-8" col-width="16rem">
                    <CardContent v-for="contenido in listado.data" :key="contenido.id" :image="contenido.imagen"
                        :href="route('lugar', contenido.slug)" imageClass="h-60"
                        preserve-page>
                        <div
                            class="text-center p-2 text-xl font-bold transition duration-300 text-primary group-hover:text-secondary  group-hover:drop-shadow">
                            {{ contenido.nombre }}</div>
                    </CardContent>
                </GridAppear>


                <pagination class="mt-6" :links="listado.links" />

            </div>

            <div class="min-w-[250px] lg:min-w-[440px]">
                <div class="card bg-base-100 shadow p-10 space-y-7">
                    <h2 class="mb-5">Lugares</h2>
                    <ul class="list-disc">
                        <li v-for="lugar in todos" :key="lugar.id">
                            <Link :href="route('lugar', lugar.slug)"
                                class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                            {{ lugar.nombre }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </ContentMain>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    listado: {
        default: () => { data: [] }
    },
    todos: {
        default: () => []
    }
});

const listado = ref(props.listado);
const todos = ref(props.todos)


</script>
