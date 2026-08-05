<template>
    <Footer :sections="sections" :socialLinks="socialLinks" :suscription="!esPortada" class="z-10" />
</template>


<script setup>
import {sections, socialLinks} from '../footer.js'

const esPortada = usePage().url == '/';
</script>
