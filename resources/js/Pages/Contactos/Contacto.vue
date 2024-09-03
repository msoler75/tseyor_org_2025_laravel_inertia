<template>
    <div class="container mx-auto px-4 py-8 pb-20">

        <div class="flex justify-between items-center mb-20">
            <Back href="/donde-estamos">Dónde estamos</Back>
            <AdminLinks modelo="contacto" necesita="administrar directorio" :contenido="contacto" />
        </div>

        <Card class="text-center w-fit space-y-4 sm:!p-14 mx-auto">
            <h1 class="text-3xl font-bold mb-4">{{ contacto.nombre }}</h1>

            <p class="lg:text-right text-gray-600 text-sm mb-2"> Última actualización:
                <TimeAgo :date="contacto.updated_at" />
            </p>
            <Image :src="contacto.imagen" class="inline-block mt-2 pb-6 max-h-[500px]"/>

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
                contacto.redes
            }}</a></p>
        <div v-if="contacto.centro" class="flex justify-center pt-12">
            <Link :href="route('centro', contacto.centro?.slug)" class="btn btn-primary">Ver Información del Centro
            </Link>
        </div>
        </Card>


    </div>
</template>

<script setup>


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
