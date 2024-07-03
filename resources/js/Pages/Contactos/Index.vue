<template>
    <div class="container py-12 mx-auto">

        <AdminPanel modelo="contacto" necesita="administrar directorio" class="mb-3" />

        <h1 class="text-center mb-0">Dónde estamos</h1>

        <div class="flex justify-end mb-5 transform lg:translate-y-[3.5rem]">
            <SearchInput />
        </div>

        <div class="w-full flex gap-5 flex-wrap lg:flex-nowrap">

            <div
                class="card bg-base-100 min-w-48 flex-wrap lg:flex-nowrap flex-row lg:flex-col p-5 lg:p-10 gap-7 self-baseline lg:sticky lg:top-20 overflow-y-auto lg:max-h-[calc(100vh-10rem)] select-none">
                <Link :href="`${route('contactos')}`" :class="!filtrado && !paisActivo ? 'text-primary font-bold' : ''">
                <span class="capitalize">Novedades</span>
                </Link>

                <div v-for="pais of paises" :key="pais.nombre" class="flex gap-2"
                    :class="paisActivo == pais.nombre ? 'text-primary font-bold' : ''">
                    <Link :href="`${route('contactos')}?pais=${pais.codigo}`">
                    <span class="capitalize" :class="pais.codigo == paisActivo ? 'font-bold' : ''">{{ pais.nombre
                        }}</span>
                    <small v-if="pais.total > 0"> ({{ pais.total }})</small>
                    </Link>
                </div>
            </div>

            <div class="w-full">

                <SearchResultsHeader v-if="!paisActivo" :results="listado" />

                <tabs :options="{ disableScrollBehavior: true }">
                    <tab name="Mapa">
                        <div ref="map" id="map" class="map-container w-full h-[400px]"></div>
                    </tab>


                    <tab name="Listado">

                        <GridAppear class="gap-8" col-width="12rem">
                            <CardContent v-for="contenido in listado.data" :key="contenido.id" :image="contenido.imagen"
                                :href="route('contacto', contenido.slug)" imageClass="h-48"
                                :tag="paisActivo ? '' : contenido.pais">
                                <div
                                    class="text-center p-2 text-xl font-bold transition duration-300 group-hover:text-primary  group-hover:drop-shadow">
                                    {{ contenido.nombre }}</div>
                            </CardContent>
                        </GridAppear>

                        <pagination class="mt-6" :links="listado.links" />

                    </tab>
                </tabs>

            </div>
        </div>
    </div>
</template>



<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { getImageUrl } from '@/composables/imageutils.js'
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

const contactos = ref(props.listado.data)





function createMapScript(options) {
                const googleMapScript = document.createElement('SCRIPT')
                if (typeof options !== 'object') {
                    throw new Error('options should  be an object')
                }

                // libraries
                /* eslint-disable no-prototype-builtins */
                if (Array.prototype.isPrototypeOf(options.libraries)) {
                    options.libraries = options.libraries.join(',')
                }
                let baseUrl = 'https://maps.googleapis.com/maps/api/js?'

                let url =
                    baseUrl +
                    Object.keys(options)
                    .map((key) => encodeURIComponent(key) + '=' + encodeURIComponent(options[key])).join('&')

                googleMapScript.setAttribute('src', url)
                googleMapScript.setAttribute('async', '')
                googleMapScript.setAttribute('defer', '')

                return googleMapScript;
            }


            function loadGoogleMaps() {
                const lib = createMapScript({
                    key: props.apiKey,
                    libraries: 'maps,marker',
                    v: 'weekly',
                    callback: 'initMap'
                });
                document.body.appendChild(lib);
            }





onMounted(() => {

    // Carga dinámica de la biblioteca de Google Maps con el parámetro de callback
    if (!window.google || !window.google.maps) {
        loadGoogleMaps()
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
    contactos.value.forEach((contacto) => {
        if(contacto.latitud!=null&&contacto.longitud!=null)
            addMarker(contacto);
    });
}

function addMarker(contacto) {
    console.log({contacto})
    // Crear el marcador en el mapa
    const marker = new google.maps.Marker({
        position: { lat: contacto.latitud, lng: contacto.longitud },
        map: map.value,
        title: contacto.nombre,
    });

    // Crear contenido para la ventana de información
    const content = `
        <div class="info-window flex flex-col gap-2 justify-start items-center text-gray-800">
          <div style='font-weight: bold; font-size: 110%'>${contacto.nombre}</div>
          <div>${contacto.poblacion}, ${contacto.pais}</div>
          <div class="flex justify-center"><a href="${route('contacto', contacto.slug)}" ><img src="${getImageUrl(contacto.imagen)}" alt="${contacto.nombre}" style="height: 60px"></a></div>
          <div class="w-full"><a class="w-full btn btn-primary btn-xs" href="${route('contacto', contacto.slug)}">Ver contacto</a></div>
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



</script>
