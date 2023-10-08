
<template>
    <div class="container py-12 mx-auto">


        <div class="flex justify-between items-center mb-20">
            <Back class="opacity-0 pointer-events-none">Glosario</Back>
            <AdminPanel modelo="lugar" necesita="administrar contenidos" />
        </div>


        <div class="mx-auto flex flex-col justify-center items-center">
            <h1>Glosario</h1>
            <GlosarioTabs />
        </div>


        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>



        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="w-full flex-grow">

                <div class="grid gap-8" :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(16rem, 1fr))` }">
                    <CardContent v-for="contenido in listado.data" :key="contenido.id" :image="contenido.imagen"
                        :href="route('lugar', contenido.slug)" imageClass="h-60">
                        <div
                            class="text-center p-2 text-xl font-bold transition duration-300 group-hover:text-primary  group-hover:drop-shadow">
                            {{ contenido.nombre }}</div>
                    </CardContent>
                </div>


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
        </div>
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
