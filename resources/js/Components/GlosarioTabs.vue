<template>
    <div class="tabs tabs-boxed mb-12 gap-7 bg-base-300 w-fit max-w-full uppercase font-bold">
        <component v-for="tab, index of tabs" :key="index" :href="route(tab.route)" :is="IAmHere(route(tab.route))?'span': Link" class="tab"
            :class="IAmHere(route(tab.route)) ? 'tab-active cursor-default' : ''"
            :title="tab.title" v-html="tab.label"
            @click="selected=route(tab.route)"
            preserve-page :auto-scroll="false"></component>
    </div>
</template>

<script setup>
import Link  from '@/Components/Link.vue';

const tabs = [
    {
        'label': 'Términos',
        'route': 'terminos',
        'title': 'Conoce los conceptos básicos o palabras clave de la filosofía Tseyor'
    },
    {
        'label': 'Guías&nbsp;<span class="hidden sm:inline">Estelares</span>',
        'route': 'guias',
        'title': 'Tutores de la Confederación y otros hermanos de las estrellas.'
    },
    {
        'label': 'Lugares&nbsp;<span class="hidden sm:inline">de la galaxia</span>',
        'route': 'lugares',
        'title': 'Lugares de interés'
    }
]

const selected = ref(null);

function IAmHere(url) {
    if(typeof window === 'undefined') return false
    return selected.value?url==selected.value: location.pathname.startsWith(url.replace(/https?:\/\/[^\/]+/, ''))
}

</script>
