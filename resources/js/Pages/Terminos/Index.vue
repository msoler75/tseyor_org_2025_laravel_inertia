
<template>
    <div class="container py-12 mx-auto">


        <div class="flex justify-between items-center mb-20">
            <Back class="opacity-0 pointer-events-none">Glosario</Back>
            <AdminPanel modelo="termino" necesita="administrar contenidos" />
        </div>


        <div class="mx-auto flex flex-col justify-center items-center">
            <h1>Glosario</h1>
            <GlosarioTabs/>
        </div>


        <div class="flex justify-end mb-5">
            <SearchInput/>
        </div>

        <div class="w-full flex gap-7 lg:gap-12 flex-wrap md:flex-nowrap">

            <div class="w-full md:w-[7rem] flex-shrink-0 card bg-base-100 shadow p-5 h-fit md:sticky md:top-20">
                <div class="flex flex-wrap md:hidden  gap-2">
                    <Link v-for="letraItem, index in letras" :key="index" class="p-2"
                        :href="route('terminos') + '?letra=' + letraItem">
                    {{ letraItem }}
                    </Link>
                </div>

                <div class="hidden md:grid grid-cols-2 gap-2">
                    <Link v-for="letraItem, index in letras" :key="index" class="p-2"
                        :style="{ order: reposicionar(index) }" :href="route('terminos') + '?letra=' + letraItem">
                    {{ letraItem }}
                    </Link>
                </div>
            </div>


            <div class="w-full flex-grow">

                <SearchResultsHeader :results="listado" />

                <div class="grid gap-8 mb-14" :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(16rem, 1fr))` }">
                    <Link v-for="contenido in listado.data" :key="contenido.id" :href="route('termino', contenido.slug)"
                        class="hover:text-primary transition-color duration-200 w-fit card shadow hover:shadow-lg px-5 py-2 bg-base-100 h-fit"
                        >
                        <div v-html="contenido.nombre" class="capitalize lowercase font-bold text-lg"/>
                        <div v-if="filtrado" v-html="contenido.descripcion" class="mt-3"/>
                </Link>
                </div>

                <pagination class="mt-6" :links="listado.links" />

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
    letras: {},
    letra: {},
    filtrado:{}
});

const listado = ref(props.listado);


function reposicionar(index) {
    const mid = props.letras.length / 2
    return index < mid ? index * 2 : ((index - mid - props.letras.length % 2) * 2 + 1)
}
</script>

