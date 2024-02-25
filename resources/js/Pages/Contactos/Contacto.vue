<template>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-20">
            <Back href="/donde-estamos">Dónde estamos</Back>
            <AdminPanel modelo="contacto" necesita="administrar directorio" :contenido="contacto" />
        </div>

        <div class="container mx-auto">
            <h1 class="text-3xl font-bold mb-4">{{ contacto.nombre }}</h1>
            <p class="text-gray-600 text-sm mb-2">
                Última actualización:
                <TimeAgo :date="contacto.updated_at" />
            </p>
            <div class="flex flex-col md:flex-row mb-4">
                <div class="w-full md:w-1/2 md:mr-8 mb-4 md:mb-0">
                    <Image :src="contacto.imagen" :alt="contacto.nombre" class="w-full h-64 object-cover" />
                </div>
                <div class="w-full md:w-1/2 animate-fade-in">
                    <p v-if="contacto.pais" class="text-lg"><strong>País:</strong> {{ contacto.pais }}</p>
                    <p v-if="contacto.poblacion" class="text-lg"><strong>Población:</strong> {{ contacto.poblacion }}</p>
                    <p v-if="contacto.provincia" class="text-lg"><strong>Provincia:</strong> {{ contacto.provincia }}</p>
                    <p v-if="contacto.direccion" class="text-lg"><strong>Dirección:</strong> {{ contacto.direccion }}</p>
                    <template v-if="showGoogleMapsLink">
                        <p class="text-lg">
                            <a :href="getGoogleMapsLink(contacto)" target="_blank" rel="noopener noreferrer">Ver en Google
                                Maps</a>
                        </p>
                    </template>
                    <p v-if="contacto.telefono" class="text-lg"><strong>Teléfono:</strong> {{ contacto.telefono }}</p>
                    <p v-if="contacto.email" class="text-lg"><strong>Email:</strong> {{ contacto.email }}</p>
                    <p v-if="contacto.redes" class="text-lg"><strong>Redes Sociales:</strong> <a :href="contacto.redes">{{
                        contacto.redes }}</a></p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    contacto: {
        type: Object,
        required: true,
    },
});

const showGoogleMapsLink = computed(() => props.contacto.pais && props.contacto.poblacion)

function getGoogleMapsLink(contacto) {
    const encodedAddress = encodeURIComponent(contacto.direccion);
    return `https://www.google.com/maps/search/?api=1&query=${encodedAddress} - ${contacto.codigo} ${contacto.poblacion}, ${contacto.provincia}, ${contacto.pais}`;
}
</script>

<style scoped>/* Agrega estilos personalizados según tus preferencias */</style>
