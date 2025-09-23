<template>
    <Page>

        <div class="flex justify-between items-center mb-20">
            <Back>Psicografías</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="psicografia" necesita="administrar contenidos" :contenido="psicografia" />
            </div>
        </div>

        <div class="py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0 animate-fade-in">

            <div class="prose mx-auto">
                <h1>{{ psicografia.titulo }}</h1>

                <div class="text-sm mb-20 flex justify-between">
                    <Link :href="route('psicografias')+'?categoria='+psicografia.categoria" class="no-underline badge badge-info hover:badge-secondary">{{ psicografia.categoria }}</Link>
                    <TimeAgo :date="psicografia.updated_at" :includeTime="false" />
                </div>

                <div>{{ psicografia.descripcion }}</div>

                <Content :content="`<img src='${getSrcUrl(psicografia.imagen)}'>`" class="mx-auto" />


                    <div class="mt-7 flex justify-end">
                        <button class="btn btn-primary" @click="abrirEnPuzle(psicografia.slug)">Abrir en puzle <Icon icon="ph:arrow-up-right-duotone" /></button>
                    </div>
                </div>

        </div>

        <div class="mt-12 grid gap-8 mb-12 grid-cols-1 lg:grid-cols-2">
            <CardContent v-if="anterior" :imageLeft="false" :key="anterior.id" :title="'Anterior: ' + anterior.titulo"
                class="rounded-none sm:rounded-lg" :image="anterior.imagen" :href="route('psicografia', anterior.slug)"
                imageClass="h-80" imageWidth="500" />
            <CardContent v-if="siguiente" :imageLeft="false" :key="siguiente.id" class="rounded-none sm:rounded-lg"
                :title="'Siguiente: ' + siguiente.titulo" :image="siguiente.imagen"
                :href="route('psicografia', siguiente.slug)"
                imageClass="h-80" imageWidth="500"/>
        </div>

        <Comentarios :url="route('psicografia', psicografia.id)" />

    </Page>
</template>

<script setup>
import { useGoogleAnalytics } from '@/composables/useGoogleAnalytics.js'
import {getSrcUrl}  from '@/composables/srcutils.js'

const { trackUserEngagement, trackDirectAccess } = useGoogleAnalytics()

const props = defineProps({
    psicografia: {
        type: Object,
        required: true,
    },
    siguiente: {
        type: [Object, null],
        required: true,
    },
    anterior: {
        type: [Object, null],
        required: true,
    },
});

function abrirEnPuzle(slug) {
    // Tracking del evento antes de abrir
    trackUserEngagement('puzle_open', `psicografia: ${props.psicografia.titulo}`)

    window.open(`https://puzle.tseyor.org/?psicografia=${slug}`, "_blank");
}

onMounted(() => {
    // Tracking de acceso directo/externo para psicografías
    trackDirectAccess('psicografia', props.psicografia.titulo)
})
</script>
