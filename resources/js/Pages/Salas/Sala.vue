<template>
    <Page>

        <PageHeader>
        <div class="flex justify-between items-center mb-20">
            <Back>Todas las Salas</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="sala" necesita="administrar directorio" :contenido="sala"/>
            </div>
        </div>

              <h1>Salas virtuales</h1>

        </PageHeader>

        <PageContent class="max-w-[60ch] mx-auto mb-20 py-12 rounded-xl">


                <h2>{{ sala.nombre }}</h2>

                <p>{{ sala.descripcion }}</p>

                <div class="flex w-full justify-end">
                    <a target="_blank" :href="sala.enlace"
                       @click="trackSalaAcceso"
                       class=" btn btn-primary after:content-['↗']">Acceder</a>
                </div>

        </PageContent>


    </Page>
</template>

<script setup>
import { useGoogleAnalytics } from '@/composables/useGoogleAnalytics.js'

const { trackUserEngagement } = useGoogleAnalytics()

const props = defineProps({
    sala: {
        type: Object,
        required: true,
    }
});

const trackSalaAcceso = () => {
    trackUserEngagement('sala_access', `sala: ${props.sala.nombre}`)
}

</script>
