<template>
    <div class="sticky -top-2 pt-10 bg-base-100 pb-4 border-b border-base-300 z-30">

        <div class="container flex justify-between items-center mb-3">
            <Back inline>Eventos</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="evento" necesita="administrar social" :contenido="evento" />
            </div>
        </div>

        <h1 class="container mt-8 mb-2 md:mb-8">
            {{ evento.titulo }}
        </h1>
        <small class="container text-right block md:mt-5"><span class="badge badge-info badge-sm">{{
            evento.categoria }}</span></small>
    </div>
    <div class="container py-7 mx-auto space-y-12">
        <div class="mx-auto flex flex-wrap md:flex-nowrap gap-12">
            <div class="w-full md:w-1/2 md:order-last">
                <div class="card bg-base-100 md:max-w-[400px] shadow-2xs p-4 grid grid-cols-2 py-7 gap-y-3">
                    <span class="mb-2 flex gap-3 items-center">
                        <Icon icon="ph:calendar-check-duotone" class="text-xl" /> Inicia:
                    </span>
                    <span>{{ fechaEs(evento.fecha_inicio, { month: 'long' }) }}</span>
                    <template v-if="evento.fecha_fin">
                        <span class="mb-2 flex gap-3 items-center">
                            <Icon icon="ph:calendar-x-duotone" class="text-xl" /> Finaliza:
                        </span>
                        <span>{{ fechaEs(evento.fecha_fin, { month: 'long' }) }}</span>
                    </template>
                    <template v-if="evento.hora_inicio">
                        <span class="mb-2 flex gap-3">
                            <Icon icon="ph:alarm-duotone" class="text-xl" /> Hora de inicio:
                        </span>
                        <span> {{ evento.hora_inicio.substr(0, 5) }}</span>
                    </template>
                    <template v-if="evento.hora_fin">
                        <span class="mb-2 flex gap-3">
                            <Icon icon="ph:clock-countdown-duotone" class="text-xl" /> Hora de fin:
                        </span>
                        <span>{{ evento.hora_fin }}</span>
                    </template>
                    <template v-if="evento.lugar" class="mb-2 flex gap-3">
                        <span class="mb-2 flex gap-3">
                            <Icon icon="ph:map-pin-duotone" class="text-xl" /> Lugar:
                        </span>
                        <span>{{ evento.lugar }}</span>
                    </template>
                    <template v-if="evento.centro" class="mb-2 flex gap-3">
                        <span class="mb-2 flex gap-3">
                            <Icon icon="ph:map-pin-duotone" class="text-xl" /> Lugar:
                        </span>
                        <Link :href="route('centro', evento.centro.slug)" class="text-primary">{{ evento.centro.nombre
                        }}</Link>
                    </template>
                    <template v-if="evento.sala" class="mb-2 flex gap-3">
                        <span class="mb-2 flex gap-3">
                            <Icon icon="ph:map-pin-duotone" class="text-xl" /> Sala virtual:
                        </span>
                        <Link :href="route('sala', evento.sala.slug)" class="text-primary">{{ evento.sala.nombre }}
                        </Link>
                    </template>
                    <template v-if="evento.equipo" class="mb-2 flex gap-3">
                        <span class="mb-2 flex gap-3">
                            <Icon icon="ph:hand-duotone" class="text-xl" /> Organiza:
                        </span>
                        <Link :href="route('equipo', evento.equipo.slug)" class="text-primary">{{ evento.equipo.nombre
                        }}</Link>
                    </template>

                    <div class="mt-6 col-span-2 flex justify-center" >
                        <a :href="googleCalendarUrl" target="_blank" rel="noopener" class="btn btn-sm btn-primary flex items-center gap-2" title="Agregar a Google Calendar">
                            <Icon icon="material-symbols:calendar-add-on-outline" class="text-lg" />
                            Añadir a Google Calendar
                        </a>
                    </div>
                </div>

                <p class="mt-12">{{ evento.descripcion }}</p>
                <hr class="my-12 hidden md:block" />
                <Content :content="evento.texto" class="mt-12 hidden md:block" />
            </div>
            <div class="w-full md:w-1/2 ">
                <div class="lg:max-w-[500px]">
                    <Image :src="evento.imagen" alt="Imagen del evento" class="w-full mb-4" />
                    <Content :content="evento.texto" class="my-12 md:hidden" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { fechaEs } from '@/composables/textutils.js'

const props = defineProps({
    evento: {
        type: Object,
        required: true,
    },
})

// Construir URL para Google Calendar
const googleCalendarUrl = computed(() => {
    const e = props.evento
    if (!e) return '#'
    // Formato: YYYYMMDDTHHmmssZ
    function formatDate(date, time) {
        if (!date) return ''
        let d = new Date(date + (time ? 'T' + time : ''))
        // Si la hora no está, poner 00:00
        if (!time) d.setHours(0,0,0,0)
        // Convertir a UTC
        return d.toISOString().replace(/[-:]/g, '').replace(/\.\d{3}Z$/, 'Z')
    }
    const start = formatDate(e.fecha_inicio, e.hora_inicio)
    const end = formatDate(e.fecha_fin || e.fecha_inicio, e.hora_fin)
    const text = encodeURIComponent(e.titulo || 'Evento')
    const details = encodeURIComponent(e.descripcion || '')
    const location = encodeURIComponent(e.lugar || (e.centro ? e.centro.nombre : ''))
    let url = `https://www.google.com/calendar/render?action=TEMPLATE&text=${text}`
    if (start) url += `&dates=${start}/${end}`
    if (details) url += `&details=${details}`
    if (location) url += `&location=${location}`
    url += '&sf=true&output=xml'
    return url
})


</script>
