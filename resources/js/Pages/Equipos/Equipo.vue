<template>
    <div>
        <div class="sticky top-8 pt-10 bg-base-100 pb-7 border-b border-gray-300 z-30">
            <div class="container mx-auto flex gap-5">
                <img :src="urlImage" alt="Imagen del equipo" class="w-32 object-cover rounded-lg">
                <div class="space-y-3">
                    <h1 class="my-2">
                        {{ equipo.nombre }}
                    </h1>
                    <div class="">{{ equipo.descripcion }}</div>
                    <small class="badge badge-neutral">{{ equipo.categoria }}</small>
                </div>
            </div>
        </div>

        <div class="container mx-auto py-12">
            <GridFill class="gap-7" w="24rem">

                <div class="card shadow p-5 bg-base-300 border-4 border-orange-400">
                    <h2>Anuncio</h2>
                    <div class="prose">
                        <p>WEriwqeriqw roiwqe owpeq riowqeriowirowe wer wer owjqer wrwqe rewq ewr ww qer wqer wqrwer we</p>
                    </div>
                </div>

                <div class="card bg-base-100 shadow p-5" v-if="ultimosArchivos.length">
                    <h2>Últimos Archivos</h2>
                    <div>
                        <div v-for="item, index of ultimosArchivos" :key="index" class="flex gap-3 items-baseline" >
                            <FileIcon :url="item.url" :name="item.archivo"/>
                            <Link :href="item.url">{{ item.url.substring(item.url.lastIndexOf('/')+1) }}</Link>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow p-5" v-if="carpetas.length">
                    <h2>Carpetas</h2>
                    <div>
                        <div v-for="item, index of carpetas" :key="index" class="flex gap-3 items-baseline">
                            <FolderIcon :url="item.ruta" />
                            <Link :href="'/'+item.ruta">{{ item.ruta.substring(item.ruta.lastIndexOf('/')+1) }}</Link>
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
                    <h2>Libros recomendados</h2>
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
            </GridFill>
        </div>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

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


const urlImage = computed(() => {
    if (!props.equipo.imagen) return '/storage/profile-photos/user.png'
    if (props.equipo.imagen.match(/^https?:\/\//)) return props.equipo.imagen
    return '/storage/' + props.equipo.imagen
})
</script>
