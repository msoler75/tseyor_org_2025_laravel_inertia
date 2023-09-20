<template>
    <div class="tabs tabs-boxed mb-12 gap-7 bg-base-300 w-fit">
        <component v-for="tab, index of tabs" :key="index" :href="tab.link" :is="IAmHere(tab.link)?'span': Link" class="tab"
            :class="IAmHere(tab.link) ? 'tab-active cursor-default' : ''">
            {{ tab.label }}</component>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const tabs = [
    {
        'label': 'Índice de términos',
        'link': route('terminos')
    },
    {
        'label': 'Guías Estelares',
        'link': route('guias')
    },
    {
        'label': 'Lugares de la Galaxia',
        'link': route('lugares')
    },
    {
        'label': 'Descargar glosario en pdf',
        'link': '/libros/glosario-terminologico'
    },
]

function IAmHere(url) {
    return url.replace(/https?:\/\/[^\/]+/, '') === location.pathname
}

</script>
