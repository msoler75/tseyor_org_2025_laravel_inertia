<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between mb-20">
            <span />
            <div class="flex gap-2">
                <Share />
                <AdminLinks
                    modelo="contacto"
                    necesita="administrar directorio"
                />
            </div>
        </div>

        <h1 class="text-center mb-20">Dónde estamos</h1>

        <div class="flex justify-end mb-5 transform lg:translate-y-[3.5rem]">
            <SearchInput class="py-1" placeholder="Buscar contacto..." :arguments="{ vista: vista }"/>
        </div>

        </PageHeader>

        <PageWide>

        <div class="w-full flex gap-5 flex-wrap lg:flex-nowrap">
            <Categorias
                :categorias="paises"
                :url="route('contactos')"
                columna-breakpoint="lg"
                select-class="w-full"
                valor="codigo"
                div-class="min-w-[200px] sm:justify-between sm:w-full lg:w-auto"
                parametro="pais"
                @click="clickPais"
                @finish="cargando = false"
                :only="['paisActivo', 'filtrado', 'listado']"
                replace
                preserve-state
            />

            <div id="main-content" class="w-full">
                <SearchResultsHeader
                    v-if="!paisActivo"
                    :results="listado"
                    class="mb-2"
                />

                <!-- md -->
                <div role="tablist" class="tabs tabs-lift">
                    <a
                        @click="cambiarVista('mapa')"
                        role="tab"
                        class="tab font-bold uppercase"
                        :class="vista == 'mapa' ? 'tab-active' : ''"
                        >Mapa</a
                    >
                    <a
                        @click="cambiarVista('listado')"
                        role="tab"
                        class="tab font-bold uppercase"
                        :class="vista == 'listado' ? 'tab-active' : ''"
                        >Listado</a
                    >
                    <a role="tab" class="tab pointer-events-none"></a>
                </div>

                <div
                    class="w-full bg-base-100 py-8 px-4 border-l border-r border-b border-base-300 p-6"
                >
                    <div
                        v-show="vista == 'mapa'"
                        ref="mapRef"
                        id="map"
                        class="map-container w-full h-[400px]"
                    ></div>

                    <div v-show="vista == 'listado'">
                        <GridAppear class="gap-8" col-width="16rem">
                            <CardContent
                                v-for="contenido in listado.data"
                                :key="contenido.id"
                                :image="contenido.imagen"
                                :href="route('contacto', contenido.slug)"
                                imageClass="h-48"
                                :tag="paisActivo ? '' : contenido.pais"
                                :skeleton="cargando"
                            >
                                <div
                                    v-if="cargando"
                                    class="m-3 mx-auto skeleton w-[13rem] h-[2.5rem]"
                                ></div>
                                <div
                                    v-else
                                    class="text-center p-2 text-xl font-bold transition duration-300 text-primary group-hover:text-secondary group-hover:drop-shadow-xs"
                                >
                                    {{ contenido.nombre }}
                                </div>
                            </CardContent>
                        </GridAppear>

                        <pagination
                            @click="cargando = true"
                            @finish="cargando = false"
                            scroll-to="#main-content"
                            class="mt-6"
                            :links="listado.links"
                            replace
                            preserve-state
                            :only="['paisActivo', 'filtrado', 'listado']"
                        />
                    </div>
                </div>
            </div>
        </div>

        </PageWide>
    </Page>
</template>

<script setup>
import { getImageUrl } from "@/composables/image.js";
import { loadGoogleMaps } from "@/composables/google";
import { useGoogleAnalytics } from '@/composables/useGoogleAnalytics.js'
import { router } from '@inertiajs/vue3';

const { trackUserEngagement } = useGoogleAnalytics()

const props = defineProps({
    paisActivo: { default: () => "" },
    filtrado: { default: () => "" },
    listado: {
        default: () => {
            data: [];
        },
    },
    paises: {
        default: () => [],
    },
    apiKey: { type: String, required: true },
    vista: { type: String, default: 'mapa' },
});

const vista = ref(props.vista);
const cargando = ref(false);
const paisClick = ref("");
// GOOGLE MAPS

const mapRef = ref(null);
const map = ref(null);
// const markers = [];

const contactos = computed(() => props.listado.data);

const justLoaded = ref(false);

onMounted(() => {
    console.log("onMounted - Inicio");
    console.log("window.google exists:", !!window.google);
    console.log("window.google.maps exists:", !!(window.google && window.google.maps));
    console.log("justLoaded:", justLoaded.value);

    window.initMap = () => {
        console.log("initMap - Inicio");
        console.log("contactos.value:", contactos.value);
        console.log("map.value:", map.value);
        console.log("mapRef.value:", mapRef.value);
        if (map.value) {
            console.log("Map already initialized");
            return;
        }
        if (!mapRef.value) {
            console.log("mapRef not ready, skipping initMap");
            return;
        }
        console.log("Inicializando mapa");
        // Inicializar el mapa
        map.value = new google.maps.Map(mapRef.value, {
            center: { lat: 0, lng: 0 },
            zoom: 2,
        });

        colocarMarcadores();
        encuadrarMarcadores();

        console.log("Mapa inicializado completamente");

        // si cambian los contactos, recolocamos los marcadores
        watch(contactos, () => {
            console.log("watch contactos - Inicio");
            console.log("contactos cambiaron:", contactos.value);
            borrarMarcadores();
            colocarMarcadores();
            encuadrarMarcadores();
        });
    };

    // Carga dinámica de la biblioteca de Google Maps con el parámetro de callback
    if (!justLoaded.value) {
        console.log("Cargando Google Maps");
        loadGoogleMaps(props.apiKey, "initMap");
    } else {
        console.log("Google Maps ya cargado previamente");
        // Si ya está cargado, llamar initMap directamente
        if (window.google && window.google.maps) {
            window.initMap();
        }
    }
    justLoaded.value = true;
});

const markers = [];

function colocarMarcadores() {
    console.log("colocarMarcadores - Inicio");
    console.log("contactos.value:", contactos.value);
    console.log("map.value:", map.value);
    // Agregar marcadores para cada contacto
    contactos.value.forEach((contacto) => {
        if (contacto.latitud != null && contacto.longitud != null)
            addMarker(contacto);
    });
}

function encuadrarMarcadores() {
    console.log("encuadrarMarcadores - Inicio");
    console.log("markers.length:", markers.length);
    if (!markers.length) {
        console.log("No hay marcadores, saliendo");
        return;
    }

    // comprobar límites del mapa usando los marcadores
    const bounds = new google.maps.LatLngBounds();
    for (let i = 0; i < markers.length; i++) {
        bounds.extend(markers[i].getPosition());
    }

    //if too close...
    var NE = bounds.getNorthEast();
    if (NE && NE.equals(bounds.getSouthWest())) {
        var extendPoint1 = new google.maps.LatLng(
            NE.lat() + 0.3,
            NE.lng() + 0.3
        );
        var extendPoint2 = new google.maps.LatLng(
            NE.lat() - 0.3,
            NE.lng() - 0.3
        );
        bounds.extend(extendPoint1);
        bounds.extend(extendPoint2);
    }

    //map.setCenter(bounds.getCenter());
    map.value.fitBounds(bounds);
}

function borrarMarcadores() {
    console.log("borrarMarcadores - Inicio");
    console.log("markers antes:", markers);
    // removemos los markers
    for (let i = 0; i < markers.length; i++) {
        markers[i].setMap(null);
    }
    // delete all items of array markers
    markers.splice(0, markers.length);
    console.log("markers después:", markers);
}

function addMarker(contacto) {
    console.log("addMarker - Inicio para contacto:", contacto.nombre);
    // Crear el marcador en el mapa
    const marker = new google.maps.Marker({
        position: { lat: contacto.latitud, lng: contacto.longitud },
        map: map.value,
        title: contacto.nombre,
    });

    // guardamos el marker en el array
    markers.push(marker);
    console.log("Marcador agregado, total markers:", markers.length);

    // Crear contenido para la ventana de información
    const content = `
        <div class="info-window flex flex-col gap-2 justify-start items-center text-gray-800">
          <div style='font-weight: bold; font-size: 110%'>${
              contacto.nombre
          }</div>
          <div>${contacto.poblacion}, ${contacto.pais}</div>
          <div class="flex justify-center"><a href="${route(
              "contacto",
              contacto.slug
          )}" ><img src="${getImageUrl(contacto.imagen)}" alt="${
        contacto.nombre
    }" style="height: 60px"></a></div>
          <div class="w-full"><a class="w-full btn btn-primary btn-xs" href="${route(
              "contacto",
              contacto.slug
          )}">Ver contacto</a></div>
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
    // markers.push(marker);
}

function clickPais(pais) {
    console.log("clickPais", pais);
    paisClick.value = pais;
    cargando.value = true;
}

const cambiarVista = (nuevaVista) => {
    const vistaAnterior = vista.value;
    vista.value = nuevaVista;

    // Tracking del cambio de vista
    trackUserEngagement('contactos_view_change', `${vistaAnterior} -> ${nuevaVista}`)
    console.log('🗺️ Cambio de vista contactos:', vistaAnterior, '->', nuevaVista)
}
</script>
