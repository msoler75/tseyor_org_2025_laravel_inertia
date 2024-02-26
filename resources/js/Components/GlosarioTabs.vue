<template>
    <div class="tabs tabs-boxed mb-12 gap-7 bg-base-300 w-fit uppercase font-bold">
        <component v-for="tab, index of tabs" :key="index" :href="tab.link" :is="IAmHere(tab.link)?'span': Link" class="tab"
            :class="IAmHere(tab.link) ? 'tab-active cursor-default' : ''"
            :title="tab.title" v-html="tab.label"
            @click="selected=tab.link"
            preserve-page :auto-scroll="false"></component>
    </div>
</template>

<script setup>
import Link  from '@/Components/Link.vue';

const tabs = [
    {
        'label': 'Términos',
        'link': route('terminos'),
        'title': 'Conoce los conceptos básicos o palabras clave de la filosofía Tseyor'
    },
    {
        'label': 'Guías&nbsp;<span class="hidden sm:inline">Estelares</span>',
        'link': route('guias'),
        'title': 'Tutores de la Confederación y otros hermanos de las estrellas.'
    },
    {
        'label': 'Lugares&nbsp;<span class="hidden sm:inline">de la galaxia</span>',
        'link': route('lugares'),
        'title': 'Lugares de interés'
    }
]

const selected = ref(null);

function IAmHere(url) {
    return selected.value?url==selected.value: location.pathname.includes(url.replace(/https?:\/\/[^\/]+/, ''))
}

</script>
