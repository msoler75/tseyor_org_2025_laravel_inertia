
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>guias del Cosmos</h1>
        <p>Aquí puedes conocer sobre la vida en otros guias del cosmos que desconocías.</p>

         <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="w-full flex-grow">

                <div class="grid gap-8"
                :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
                    <div v-if="listado.data.length > 0" v-for="guia in listado.data" :key="guia.id"
                        class="card bg-base-100 shadow">
                        <img :src="guia.imagen" :alt="guia.nombre" class="h-48 object-cover w-full" />
                        <div class="p-4">
                            <h2 class="text-lg font-bold mb-2">{{ guia.nombre }}</h2>
                            <p class="text-gray-700 text-sm">{{ guia.descripcion }}</p>
                            <Link :href="`/guias/${guia.slug}`"
                                class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                                Ver guia
                            </Link>
                        </div>
                    </div>
                </div>


                <pagination class="mt-6" :links="listado.links" />

            </div>

            <div class="min-w-[250px] lg:min-w-[440px]">
                <div class="card bg-base-100 shadow p-10 space-y-7">
                    <h2 class="mb-5">guias</h2>
                    <ul class="list-disc">
                        <li v-for="guia in todos" :key="guia.id">
                            <Link :href="`/guias/${guia.slug}`"
                                class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                                {{ guia.titulo }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
   listado: {
        default: () => {data:[]}
    },
    todos: {
        default: () => []
    }
});

const listado= ref(props.listado);
const todos = ref(props.todos)


</script>
