<template>
    <Footer :sections="sections" :socialLinks="socialLinks" :suscription="usePage().url!='/'" class="z-10" />
</template>


<script setup>
import {sections, socialLinks} from '../footer.js'
import { usePage } from '@inertiajs/vue3';
</script>
