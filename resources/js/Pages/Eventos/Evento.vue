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
                    <template v-if="fechasEvento.length>1">
                        <span class="mb-2 flex items-start gap-3">
                            <Icon
                                icon="ph:calendar-check-duotone"
                                class="text-xl"
                            />
                            Fechas:
                        </span>
                        <div class="flex flex-col gap-1">
                            <span v-for="fecha of fechasEvento" :key="fecha">{{ fecha }}</span>
                        </div>
                    </template>
                    <template v-else>
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

        <Modal :show="showModal" @close="showModal=false" maxWidth="lg" class="rounded-xl" >
            <div class="p-5 space-y-5">

                <h2>Cómo importar en tu Google Calendar</h2>
                    <div>Se ha descargado un archivo .ics en tu dispositivo.</div>
                    <ol class="space-y-3 list-decimal pl-5">
                        <li>Abre la web de Google Calendar en tu navegador.</li>
                        <li>En el menú lateral izquierdo, busca "Otros calendarios" y haz clic en el símbolo "+"
                            <img :src="tutorialImage" alt="Menú de Google Calendar" class="inline border rounded-lg max-w-full">
                        </li>
                        <li>Selecciona "Importar" en el menú desplegable.</li>
                        <li>En la ventana de importación, haz clic en "Seleccionar archivo de tu ordenador" y elige el archivo .ics que se ha descargado.</li>
                        <li>Elige el calendario al que deseas agregar el evento y haz clic en "Importar".</li>
                    </ol>

                <div>Abre ahora <a href="https://calendar.google.com" target="_blank" rel="noopener" class="text-primary underline">Google Calendar</a></div>

                <div class="flex justify-center">
                    <button class="btn btn-primary" @click="showModal=false">Entendido</button>
                </div>
                </div>
        </Modal>
    </Page>
</template>

<script setup>
import {
    fechaFormatoEsp,
    buildGoogleCalendarDates,
    parsearFechasEvento
} from "@/composables/fechas.js";
import {removeAccents} from "@/composables/textutils.js";
import { useGoogleAnalytics } from "@/composables/useGoogleAnalytics.js";

const tutorialImage = 'data:image/jpeg;base64,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'

const { trackUserEngagement, trackDirectAccess } = useGoogleAnalytics();

const props = defineProps({
    evento: {
        type: Object,
        required: true,
    },
});

const fechasEvento = computed(() => parsearFechasEvento(props.evento.fechas_evento));

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


const generateICSFile = () => {
    if(!fechasEvento.value.length) return;

    const e = props.evento;

    const fechas = fechasEvento.value;

    let icsContent = [
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//Tseyor//ES',
        'CALSCALE:GREGORIAN',
        'METHOD:PUBLISH'
    ];

    const now = new Date();
    const dtstamp = now.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';

    fechas.forEach((fecha, index) => {
        const eventoTemporal = {
            ...e,
            fecha_inicio: fecha,
            fecha_fin: fecha
        };

        const { start, end } = buildGoogleCalendarDates(eventoTemporal);

        if (!start || !end) {
            console.warn(`Fecha inválida saltada: ${fecha}`);
            return;
        }

        // UID: usar la fecha ISO original (2026-03-01) sin barras
        // Formato: 20260301-5-0@tseyor.org
        const uid = `${fecha.replace(/-/g, '')}-${e.id || 'evento'}-${index}@tseyor.org`;

        const escaparTexto = (texto) => {
            if (!texto) return '';
            return texto
                .replace(/\\/g, '\\\\')
                .replace(/;/g, '\\;')
                .replace(/,/g, '\\,')
                .replace(/\n/g, '\\n');
        };

        icsContent.push(
            'BEGIN:VEVENT',
            `UID:${uid}`,
            `DTSTAMP:${dtstamp}`,
            `DTSTART:${start}`,
            `DTEND:${end}`,
            `SUMMARY:${escaparTexto(e.titulo || 'Evento')}`,
            `DESCRIPTION:${escaparTexto(e.descripcion || '')}`,
            `LOCATION:${escaparTexto(e.lugar || (e.centro ? e.centro.nombre : ''))}`,
            'STATUS:CONFIRMED',
            'SEQUENCE:0',
            'END:VEVENT'
        );
    });

    icsContent.push('END:VCALENDAR');

    const icsText = icsContent.join('\r\n');
    const blob = new Blob([icsText], {
        type: 'text/calendar;charset=utf-8'
    });

    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `${(removeAccents(e.titulo) || 'evento').replace(/[^a-z0-9]/gi, '_').toLowerCase()}.ics`;
    link.click();

    setTimeout(() => URL.revokeObjectURL(link.href), 100);
};

const thisUrl = ref(null);
const showModal = ref(false);

const trackCalendarAdd = (e) => {
    trackUserEngagement("calendar_add", `evento: ${props.evento.titulo}`);
    if(fechasEvento.value.length>1) {
        e.preventDefault()
        generateICSFile()
        showModal.value = true
    }
};

onMounted(() => {
    thisUrl.value = window.location.href;

    // Tracking de acceso directo/externo
    trackDirectAccess("evento", props.evento.titulo);
});
</script>
