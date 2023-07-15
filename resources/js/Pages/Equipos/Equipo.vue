<template>
    <div>
        <div class="sticky top-14 py-5 bg-base-100 border-b border-gray-300 z-30"
            :class="nav.scrollY < 200 ? 'hidden' : ''">
            <div class="container mx-auto flex gap-5 items-center">
                <Image :src="equipo.imagen" alt="Imagen del equipo" class="h-10 object-cover rounded-lg"/>
                <h1 class="my-2 text-2xl">
                    {{ equipo.nombre }}
                </h1>
                <div class="hidden sm:flex ml-auto gap-3 text-2xl items-center">
                    <Icon icon="ph:user-duotone" />
                    {{ equipo.usuarios.length }}
                </div>
            </div>
        </div>

        <div class="container mx-auto py-12">
            <GridFill class="gap-7" w="20rem">

                <div class="sm:card sm:bg-base-100 sm:shadow sm:p-5 flex gap-5 sm:col-span-2">
                    <div class="w-full flex flex-wrap h-full gap-5 justify-center items-center">
                        <Image :src="equipo.imagen" alt="Imagen del equipo" class="w-[200px] sm:w-40 object-cover rounded-lg"/>
                        <div class="flex-grow space-y-5 justify-center sm:justify-start text-center sm:text-left">
                            <h2 class="my-2">
                                {{ equipo.nombre }}
                            </h2>
                            <div class="opacity-80">{{ equipo.descripcion }}</div>
                            <small class="badge badge-neutral">{{ equipo.categoria }}</small>
                            <div class="sm:hidden flex justify-center text-2xl mt-12">
                                <Icon icon="ph:user-duotone" />
                                {{ equipo.usuarios.length }}
                            </div>

                        </div>

                        <div class="hidden sm:flex sm:ml-auto gap-3 text-2xl items-center self-end justify-center">
                            <Icon icon="ph:user-duotone" />
                            {{ equipo.usuarios.length }}
                        </div>
                    </div>
                </div>

                <div class="card shadow p-5 bg-base-100 border border-orange-400 justify-center items-center" v-if="equipo.anuncio">
                    <div class="prose" v-html="equipo.anuncio"/>
                </div>

                <div class="card bg-base-100 shadow p-5" v-if="equipo.reuniones">
                    <h2>Reuniones</h2>
                    <div class="prose" v-html="equipo.reuniones"/>
                </div>

                <div class="card bg-base-100 shadow p-5" v-if="ultimosArchivos.length">
                    <h2>Últimos Archivos</h2>
                    <div class="w-full">
                        <div v-for="item, index of ultimosArchivos" :key="index"
                            class="flex gap-3 items-center py-2 w-full">
                            <FileIcon :url="item.url" :name="item.archivo" />
                            <Link :href="item.url" class="py-1 hover:underline">{{ item.url.substring(item.url.lastIndexOf('/') +
                                1)
                            }}</Link>
                            <TimeAgo class="ml-auto" :date="item.fecha_modificacion" />
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow p-5" v-if="carpetas.length">
                    <h2>Carpetas</h2>
                    <div>
                        <div v-for="item, index of carpetas" :key="index" class="flex gap-3 items-baseline py-2">
                            <FolderIcon :url="item.ruta" />
                            <Link :href="'/' + item.ruta" class="py-1 hover:underline">{{
                                item.ruta.substring(item.ruta.lastIndexOf('/') + 1) }}</Link>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow p-5" v-if="false">
                    <h2>Publicaciones</h2>
                    <div>
                        <ul>
                            <li> item 1</li>
                            <li> item 2</li>
                        </ul>
                    </div>
                </div>

                <div class="card bg-base-100 shadow p-5">
                    <h2>Miembros</h2>
                    <Users v-if="equipo" :users="equipo.usuarios" :count="13" />
                </div>

                <div class="card bg-base-100 shadow p-5">
                    <h2>Libros recomendados</h2>
                    <div>
                        <ul>
                            <li> item 1</li>
                            <li> item 2</li>
                        </ul>
                    </div>
                </div>

            </GridFill>
        </div>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useNav } from '@/Stores/nav'

defineOptions({ layout: AppLayout })

const props = defineProps({
    equipo: {
        type: Object,
        required: true,
    },
    totalMiembros: Number,
    ultimosArchivos: {},
    carpetas: {}
})

const nav = useNav()

</script>
