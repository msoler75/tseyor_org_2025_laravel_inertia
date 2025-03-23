<template>
    <Page>

        <div class="flex justify-between items-center mb-20">
            <Back>Experiencias</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="experiencia" necesita="administrar experiencias" :contenido="experiencia" />
            </div>
        </div>

        <div class="cloud py-[9ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0 animate-fade-in">

            <div class="px-4 sm:px-14 mx-auto">
                <h1>{{ experiencia.nombre }}</h1>

                <div class="text-sm flex justify-between mb-14">
                    <Link :href="'/experiencias?categoria=' + experiencia.categoria" title="Sue%C3%B1os"
                        class="badge badge-primary text-decoration-none">{{ experiencia.categoria }}</Link>
                    <TimeAgo :date="experiencia.created_at" :includeTime="false" />
                </div>


                <Content :content="experiencia.texto" />

                <p class="py-5">
                <ul>
                    <li><span class="text-primary font-bold">Fecha:</span> {{ experiencia.fecha }}</li>
                    <li><span class="text-primary font-bold">Lugar:</span> {{ experiencia.lugar }}</li>
                </ul>
                </p>

                <div v-if="experiencia.archivo" class="flex justify-end">
                    <a class="btn btn-xs mr-5 flex gap-3 items-center w-fit" download
                        :href="getSrcUrl(experiencia.archivo)" title="nombreArchivo">Descargar archivo adjunto</a>
                </div>
            </div>

        </div>

        <Comentarios :url="route('experiencia', experiencia.id)" />

    </Page>
</template>

<script setup>
import { getSrcUrl } from '@/composables/srcutils.js'


const props = defineProps({
    experiencia: {
        type: Object,
        required: true,
    }
});

const nombreArchivo = computed(() => {
    if (!props.experiencia.archivo) return null
    const idx = props.experiencia.archivo.lastIndexOf("/");
    if (idx == -1) return props.experiencia.archivo
    return props.experiencia.archivo.substring(idx + 1)
})

</script>

<style scoped>
.cloud {
    background-color: transparent;
    background-image: radial-gradient(white 70%, transparent 30%);
    background-size: calc(var(--border-thickness) * 2) calc(var(--border-thickness) * 2);
    background-position: 0 0;
    position: relative;
    z-index: 1;
    --box-size: 100%;
    --border-thickness: 5%;
    box-shadow: none;
}

.dark .cloud {
    background-image: radial-gradient(black 70%, transparent 30%);
}

.cloud::before {
    content: "";
    display: block;
    background-color: hsla(0, 0%, 100%, 1);
    position: absolute;
    top: var(--border-thickness);
    left: var(--border-thickness);
    height: calc(var(--box-size) - var(--border-thickness) * 2);
    width: calc(var(--box-size) - var(--border-thickness) * 2);
    z-index: -1;
}

.dark .cloud::before {
    background-color: rgb(0, 0, 0);
}
</style>
