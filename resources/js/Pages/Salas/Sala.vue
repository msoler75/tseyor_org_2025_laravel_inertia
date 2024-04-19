<template>
    <div class="container mx-auto py-12">

        <div class="flex justify-between items-center mb-20">
            <Back>Salas</Back>
            <AdminPanel modelo="sala" necesita="administrar directorio" :contenido="sala"/>
        </div>

        <div class="max-w-[900px] mx-auto flex flex-col md:flex-row gap-10 mt-12">

            <h1>Salas virtuales</h1>

            <div class="bg-base-100 shadow-xl p-5 rounded-lg">

                <h2>{{ sala.nombre }}</h2>
                
                <p>{{ sala.descripcion }}</p>

                <div class="flex w-full justify-end">
                    <a target="_blank" :href="sala.enlace" class=" btn btn-primary after:content-['↗']">Acceder</a>
                </div>
                
            </div>
        </div>


    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    sala: {
        type: Object,
        required: true,
    }
});


</script>
