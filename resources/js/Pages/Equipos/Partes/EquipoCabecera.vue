<template>
    <div class="sticky top-14 py-5 bg-base-100 border-b border-gray-300 z-30" :class="nav.scrollY < 200 ? 'hidden' : ''">
        <div class="container mx-auto flex gap-5 items-center">
            <Image :src="equipo.imagen" alt="Imagen del equipo" class="h-10 object-cover rounded-lg" />
            <h1 class="my-2 text-2xl">
                {{ equipo.nombre }}
            </h1>
            <div class="hidden sm:flex ml-auto gap-3 text-2xl items-center">
                <Icon icon="ph:user-duotone" />
                {{ equipo.usuarios.length }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { useNav } from '@/Stores/nav'

defineProps({ equipo: { type: Object, required: true } })

const nav = useNav()
</script>
