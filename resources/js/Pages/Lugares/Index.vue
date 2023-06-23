
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>Lugares del Cosmos</h1>
        <p>Aquí puedes conocer sobre la vida en otros lugares del cosmos que desconocías.</p>

         <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="w-full flex-grow">

                <div class="grid gap-8"
                :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
                    <div v-if="listado.data.length > 0" v-for="lugar in listado.data" :key="lugar.id"
                        class="card bg-base-100 shadow">
                        <img :src="lugar.imagen" :alt="lugar.nombre" class="h-48 object-cover w-full" />
                        <div class="p-4">
                            <h2 class="text-lg font-bold mb-2">{{ lugar.nombre }}</h2>
                            <p class="text-gray-700 text-sm">{{ lugar.descripcion }}</p>
                            <Link :href="`/lugares/${lugar.slug}`"
                                class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                                Ver Lugar
                            </Link>
                        </div>
                    </div>
                </div>


                <pagination class="mt-6" :links="listado.links" />

            </div>

            <div class="min-w-[250px] lg:min-w-[440px]">
                <div class="card bg-base-100 shadow p-10 space-y-7">
                    <h2 class="mb-5">Lugares</h2>
                    <ul class="list-disc">
                        <li v-for="lugar in todos" :key="lugar.id">
                            <Link :href="`/lugares/${lugar.slug}`"
                                class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                                {{ lugar.titulo }}
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
