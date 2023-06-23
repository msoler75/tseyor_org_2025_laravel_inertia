
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1 class="text-center mb-0">Dónde estamos</h1>

        <div class="flex justify-end mb-5 transform lg:translate-y-[3.5rem]">
            <SearchInput />
        </div>

        <div class="w-full flex gap-5 flex-wrap lg:flex-nowrap">

            <div class="card bg-base-100 shadow flex-wrap flex-row lg:flex-col p-5 lg:p-10 gap-4 self-baseline">
                <Link :href="`${route('contactos')}`" :class="!filtrado && !paisActivo ? 'text-blue-700 font-bold' : ''">
                <span class="capitalize">Novedades</span>
                </Link>

                <div v-for="pais of paises" :key="pais.nombre" class="flex gap-2"
                    :class="paisActivo == pais.nombre ? 'text-blue-700 font-bold' : ''">
                    <Link :href="`${route('contactos')}?pais=${pais.codigo}`">
                    <span class="capitalize">{{ pais.nombre }}</span>
                    <small v-if="pais.total > 0">({{ pais.total }})</small>
                    </Link>
                </div>
            </div>

            <div class="w-full flex-grow">

                <SearchResultsHeader :results="listado" />

                <tabs>
                    <tab name="Mapa">

                        <div ref="map" class="map-container"></div>
                    </tab>


                    <tab name="Contactos">
                        <div v-if="listado.data.length > 0" class="grid gap-4"
                            :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(16rem, 1fr))` }">
                            <div v-for="contacto in listado.data" :key="contacto.id" class="card bg-base-100 shadow">
                                <img :src="contacto.imagen_url" :alt="contacto.nombre" class="h-48 object-cover w-full" />
                                <div class="p-5 flex flex-col flex-grow">
                                    <h2 class="text-lg font-bold mb-2">{{ contacto.nombre }}</h2>
                                    <div class="my-4 badge badge-primary badge-outline">{{
                                        contacto.pais }}</div>
                                    <p class="text-xs">{{
                                        contacto.poblacion }}</p>
                                    <Link :href="`/contacto/${contacto.slug}`" class="btn btn-primary mt-auto">
                                    Ver contacto
                                    </Link>

                                    <p v-if="false" class="text-gray-600 mb-2 w-full text-xs text-right">
                                        <TimeAgo :date="contacto.updated_at" />
                                    </p>
                                </div>
                            </div>
                        </div>

                        <pagination class="mt-6" :links="listado.links" />

                    </tab>
                </tabs>

            </div>
        </div>
    </div>
</template>



<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'
import { Tabs, Tab } from 'vue3-tabs-component';

defineOptions({ layout: AppLayout })

const props = defineProps({
    paisActivo: { default: () => '' },
    filtrado: { default: () => '' },
    listado: {
        default: () => { data: [] }
    },
    paises: {
        default: () => []
    },
    apiKey: { default: () => '' }
});

const listado = ref(props.listado);
const paises = ref(props.paises)




// GOOGLE MAPS



const map = ref(null);
const markers = [];

const contactos = [
    // Tu lista de contactos aquí
];

onMounted(() => {

    // Carga dinámica de la biblioteca de Google Maps con el parámetro de callback
    if (!window.google || !window.google.maps) {
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${props.apiKey}&libraries=places&callback=initMap`;
        script.defer = true;
        document.head.appendChild(script);
    }
    else
        initMap()

});

window.initMap = () => {
    // Inicializar el mapa
    map.value = new google.maps.Map(map.value, {
        center: { lat: 0, lng: 0 },
        zoom: 2,
    });

    // Agregar marcadores para cada contacto
    contactos.forEach((contacto) => {
        addMarker(contacto);
    });
}

function addMarker(contacto) {
    // Crear el marcador en el mapa
    const marker = new google.maps.Marker({
        position: { lat: contacto.latitud, lng: contacto.longitud },
        map: map.value,
        title: contacto.nombre,
    });

    // Crear contenido para la ventana de información
    const content = `
        <div class="info-window">
          <h3>${contacto.nombre}</h3>
          <p>${contacto.direccion}</p>
          <p>${contacto.poblacion}, ${contacto.pais}</p>
          <a href="${getContactoURL(contacto)}">Ver contacto</a>
        </div>
      `;

    // Crear ventana de información para el marcador
    const infoWindow = new google.maps.InfoWindow({
        content: content,
    });

    // Mostrar ventana de información al hacer clic en el marcador
    marker.addListener("click", () => {
        infoWindow.open(map.value, marker);
    });

    // Agregar marcador a la lista
    markers.push(marker);
}

function getContactoURL(contacto) {
    // Devolver la URL del contacto
    return `/contactos/${contacto.slug}`;
}


</script>


