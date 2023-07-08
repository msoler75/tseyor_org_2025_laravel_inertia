<template>
    <div>
        <div class="sticky top-8 pt-10 bg-base-100 pb-4 border-b border-base-300 z-30">
            <h1 class="container mx-auto my-2">
                {{ equipo.nombre }}
            </h1>
            <div class="container mx-auto">{{ equipo.descripcion }}</div>
            <small class="container mx-auto text-right block">{{ equipo.categoria }}</small>
        </div>
        <div class="container py-7 mx-auto space-y-12">
            <div>
            </div>
            <div class="w-full md:w-1/2">
                <div class="lg:max-w-[500px]">
                    <img :src="'/storage/'+equipo.imagen" alt="Imagen del equipo" class="w-full mb-4">
                    <p class="mb-4">{{ equipo.descripcion }}</p>
                </div>
            </div>

            <div class="card bg-base-100 shadow p-5">
                <Users v-if="equipo" :users="equipo.usuarios"
                :count="13"
                />
            </div>
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
    totalMiembros: Number
})

</script>
