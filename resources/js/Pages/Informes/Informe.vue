<template>
    <div>


        <div class="container py-12 mx-auto flex justify-between items-center">
            <Back class="hover:underline" :href="route('equipo.informes', equipo.slug)">Informes del equipo</Back>
            <Link v-if="equipo && equipo.slug && equipo.nombre" :href="route('equipo', equipo.slug)" class="flex h-fit gap-2 text-sm items-center hover:underline">
                {{ equipo.nombre }}
                <Icon icon="ph:arrow-right" />
            </Link>
            <span v-else></span>
            <AdminPanel modelo="informe" necesita="administrar contenidos" :contenido="informe" />
        </div>


        <div class="py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0">

            <div class="prose mx-auto">
                <h1>{{ informe.titulo }}</h1>

                <p v-if="equipo && equipo.nombre" class="text-neutral opacity-50">{{ equipo.nombre }}</p>
                <div class="text-neutral text-sm mb-2 flex justify-between">
                    <Audios :audios="parseAudios(informe.audios, informe.titulo)"/>
                    <TimeAgo :date="informe.updated_at" :includeTime="false" />
                </div>
            </div>

            <Content :content="informe.texto" class="pb-12 mx-auto" />

        </div>

        <Comentarios :url="route('informe', informe.id)" />

    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { parseAudios } from '@/composables/parseAudios'

defineOptions({ layout: AppLayout })

const props = defineProps({
    informe: {
        type: Object,
        required: true,
    },
    equipo: {
        type: Object,
        required: true
    }
});

</script>
