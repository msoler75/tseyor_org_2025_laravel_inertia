<template>
    <Page class="py-0! md:py-0!">
        <div
            class="sticky -top-2 pt-10 bg-base-100 pb-4 border-b border-base-300 z-30"
        >
            <div class="container flex justify-between items-center mb-3">
                <Back inline>Eventos</Back>
                <div class="flex gap-2">
                    <Share />
                    <AdminLinks
                        modelo="evento"
                        necesita="administrar social"
                        :contenido="evento"
                    />
                </div>
            </div>

            <h1 class="container mt-8 mb-2 md:mb-8">
                {{ evento.titulo }}
            </h1>
            <small class="container text-right block md:mt-5"
                ><span class="badge badge-info badge-sm">{{
                    evento.categoria
                }}</span></small
            >
        </div>

        <div class="container py-7 mx-auto space-y-12">
            <div class="mx-auto grid gap-12 md:grid-cols-2 items-start">
                <div class="w-full">
                    <div class="lg:max-w-[500px]">
                        <Image
                            :src="evento.imagen"
                            alt="Imagen del evento"
                            class="w-full mb-4"
                            @click="showImage"
                        />
                    </div>
                </div>
                <div class="w-full order-first md:order-last">
                    <div
                        class="card bg-base-100 md:max-w-[400px] shadow-2xs p-4 grid grid-cols-2 py-7 gap-y-3"
                    >
                        <span class="mb-2 flex gap-3 items-center">
                            <Icon
                                icon="ph:calendar-check-duotone"
                                class="text-xl"
                            />
                            Inicia:
                        </span>
                        <span>{{
                            fechaFormatoEsp(evento.fecha_inicio, {
                                month: "long",
                            })
                        }}</span>
                        <template v-if="evento.fecha_fin">
                            <span class="mb-2 flex gap-3 items-center">
                                <Icon
                                    icon="ph:calendar-x-duotone"
                                    class="text-xl"
                                />
                                Finaliza:
                            </span>
                            <span>{{
                                fechaFormatoEsp(evento.fecha_fin, {
                                    month: "long",
                                })
                            }}</span>
                        </template>
                        <template v-if="evento.hora_inicio">
                            <span class="mb-2 flex gap-3">
                                <Icon icon="ph:alarm-duotone" class="text-xl" />
                                Hora de inicio:
                            </span>
                            <span> {{ evento.hora_inicio.substr(0, 5) }}</span>
                        </template>
                        <template v-if="evento.hora_fin">
                            <span class="mb-2 flex gap-3">
                                <Icon
                                    icon="ph:clock-countdown-duotone"
                                    class="text-xl"
                                />
                                Hora de fin:
                            </span>
                            <span>{{ evento.hora_fin }}</span>
                        </template>
                        <template v-if="evento.lugar" class="mb-2 flex gap-3">
                            <span class="mb-2 flex gap-3">
                                <Icon
                                    icon="ph:map-pin-duotone"
                                    class="text-xl"
                                />
                                Lugar:
                            </span>
                            <span>{{ evento.lugar }}</span>
                        </template>
                        <template v-if="evento.centro" class="mb-2 flex gap-3">
                            <span class="mb-2 flex gap-3">
                                <Icon
                                    icon="ph:map-pin-duotone"
                                    class="text-xl"
                                />
                                Lugar:
                            </span>
                            <Link
                                :href="route('centro', evento.centro.slug)"
                                class="text-primary"
                                >{{ evento.centro.nombre }}</Link
                            >
                        </template>
                        <template v-if="evento.sala" class="mb-2 flex gap-3">
                            <span class="mb-2 flex gap-3">
                                <Icon
                                    icon="ph:map-pin-duotone"
                                    class="text-xl"
                                />
                                Sala virtual:
                            </span>
                            <Link
                                :href="route('sala', evento.sala.slug)"
                                class="text-primary"
                                >{{ evento.sala.nombre }}
                            </Link>
                        </template>
                        <template v-if="evento.equipo" class="mb-2 flex gap-3">
                            <span class="mb-2 flex gap-3">
                                <Icon icon="ph:hand-duotone" class="text-xl" />
                                Organiza:
                            </span>
                            <Link
                                :href="route('equipo', evento.equipo.slug)"
                                class="text-primary"
                                >{{ evento.equipo.nombre }}</Link
                            >
                        </template>

                        <div class="mt-6 col-span-2 flex justify-center">
                            <a
                                :href="googleCalendarUrl"
                                target="_blank"
                                rel="noopener"
                                @click="trackCalendarAdd"
                                class="btn btn-sm btn-primary flex items-center gap-2"
                                title="Agregar a Google Calendar"
                            >
                                <Icon
                                    icon="material-symbols:calendar-add-on-outline"
                                    class="text-lg"
                                />
                                Añadir a Google Calendar
                            </a>
                        </div>
                    </div>
                    <p class="mt-12 text-xl italic">{{ evento.descripcion }}</p>

                    <hr class="my-12" />

                    <PageContent class="sm:max-w-[80ch]">
                        <div class="py-[10ch] mb-12 relative">
                            <FontSizeControls
                                class="hidden lg:flex absolute right-4 top-4"
                            />

                            <div class="prose mx-auto">
                                <!-- Un solo componente Content, reposicionado por el grid responsivo -->
                                <Content
                                    :content="textoImagenInsertada"
                                    ref="content"
                                    :optimizeImages="false"
                                />
                            </div>
                        </div>
                    </PageContent>
                </div>
            </div>
        </div>
    </Page>
</template>

<script setup>
import {
    fechaFormatoEsp,
    buildGoogleCalendarDates,
} from "@/composables/fechas.js";
import { useGoogleAnalytics } from "@/composables/useGoogleAnalytics.js";

const { trackUserEngagement, trackDirectAccess } = useGoogleAnalytics();

const props = defineProps({
    evento: {
        type: Object,
        required: true,
    },
});

const textoImagenInsertada = computed(() => {
    return (
        `<img src='${props.evento.imagen}' class='hidden'>\n\n` +
        props.evento.texto
    );
});

const content = ref(null);

async function showImage() {
    // Intentar abrir la imagen desde el único componente Content
    if (
        content &&
        content.value &&
        typeof content.value.showImage === "function"
    ) {
        content.value.showImage(0);
        return;
    }
    console.warn(
        "Content component no expone showImage o ref no inicializada",
        content && content.value
    );
}

// Construir URL para Google Calendar
const googleCalendarUrl = computed(() => {
    const e = props.evento;
    if (!e) return "#";

    // usamos formatDateOnly y formatDateTimeLocalToUTC importados desde composable

    const text = encodeURIComponent(e.titulo || "Evento");
    const rawDetails = `${e.descripcion || ""}\n\nEvento: ${
        thisUrl.value || ""
    }`.trim();
    // Añadir la URL pública del evento (page.url) al final de la descripción
    const details = encodeURIComponent(rawDetails);
    const location = encodeURIComponent(
        e.lugar || (e.centro ? e.centro.nombre : "")
    );

    const { start, end } = buildGoogleCalendarDates(e);

    let url = `https://www.google.com/calendar/render?action=TEMPLATE&text=${text}`;
    if (start && end) url += `&dates=${start}/${end}`;
    if (details) url += `&details=${details}`;
    if (location) url += `&location=${location}`;
    url += "&sf=true&output=xml";
    return url;
});

const thisUrl = ref(null);

const trackCalendarAdd = () => {
    trackUserEngagement("calendar_add", `evento: ${props.evento.titulo}`);
};

onMounted(() => {
    thisUrl.value = window.location.href;

    // Tracking de acceso directo/externo
    trackDirectAccess("evento", props.evento.titulo);
});
</script>
