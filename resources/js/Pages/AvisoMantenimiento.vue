<template>
    <Head :title="pageTitle" />

    <div class="container mx-auto px-4 py-24 md:py-32 max-w-3xl">
        <div v-if="aviso && aviso.inicio" class="space-y-8">
            <div class="text-center">
                <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-warning/80 mb-4 font-display">
                    Aviso de mantenimiento
                </span>

                <h1 class="text-4xl sm:text-5xl font-display font-black leading-tight text-balance">
                    {{ aviso.titulo }}
                </h1>
            </div>

            <div class="card bg-base-100 shadow-lg">
                <div class="card-body gap-4">
                    <div class="flex items-start gap-3">
                        <Icon icon="ph:calendar-check-duotone" class="w-6 h-6 text-primary shrink-0 mt-0.5" />
                        <div>
                            <p class="font-semibold mt-0">Fecha</p>
                            <p class="text-base-content/75">{{ fechaFormato }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <Icon icon="ph:clock-duotone" class="w-6 h-6 text-primary shrink-0 mt-0.5" />
                        <div>
                            <p class="font-semibold mt-0">Ventana de mantenimiento</p>
                            <p class="text-base-content/75">
                                Entre las {{ horaInicio }} y las {{ horaFin }} (hora local).
                                <template v-if="aviso.duracion_estimada">
                                    La interrupcion real del servicio sera de <strong>{{ aviso.duracion_estimada }}</strong> dentro de esa franja.
                                </template>
                            </p>
                        </div>
                    </div>

                    <div v-if="aviso.descripcion" class="flex items-start gap-3">
                        <Icon icon="ph:info-duotone" class="w-6 h-6 text-info shrink-0 mt-0.5" />
                        <div>
                            <p class="font-semibold mt-0">Que significa esto</p>
                            <p class="text-base-content/75">{{ aviso.descripcion }}</p>
                        </div>
                    </div>

                    <div v-if="estado === 'proximo'" class="flex items-start gap-3">
                        <Icon icon="ph:timer-duotone" class="w-6 h-6 text-accent shrink-0 mt-0.5" />
                        <div>
                            <p class="font-semibold mt-0">Tiempo restante</p>
                            <p class="text-base-content/75">{{ tiempoRestante }}</p>
                        </div>
                    </div>

                    <div v-if="estado === 'en_curso'" class="alert alert-warning mt-2">
                        <Icon icon="ph:warning-duotone" class="w-5 h-5" />
                        <span>El mantenimiento está en curso. El sitio puede estar inaccesible en estos momentos.</span>
                    </div>

                    <div v-if="estado === 'finalizado'" class="alert alert-success mt-2">
                        <Icon icon="ph:check-circle-duotone" class="w-5 h-5" />
                        <span>El mantenimiento ha finalizado. El sitio debería funcionar con normalidad.</span>
                    </div>
                </div>
            </div>

            <div v-if="aviso.raw_email_text" class="mt-8">
                <details class="group">
                    <summary class="cursor-pointer text-sm text-base-content/50 hover:text-base-content/70 transition-colors">
                        Ver texto original del aviso de DreamHost
                    </summary>
                    <pre class="mt-3 p-4 bg-base-300 rounded-lg text-xs text-base-content/60 whitespace-pre-wrap">{{ aviso.raw_email_text }}</pre>
                </details>
            </div>

            <div class="text-center mt-8">
                <a href="/" class="btn btn-ghost gap-2">
                    <Icon icon="ph:arrow-left-duotone" class="w-4 h-4" />
                    Volver al inicio
                </a>
            </div>
        </div>

        <div v-else class="text-center py-16">
            <Icon icon="ph:smiley-duotone" class="w-16 h-16 text-base-content/30 mx-auto mb-4" />
            <h2 class="text-2xl font-semibold mb-2">No hay mantenimiento programado</h2>
            <p class="text-base-content/60">No hay ningún aviso de mantenimiento activo en este momento.</p>
            <a href="/" class="btn btn-ghost mt-6 gap-2">
                <Icon icon="ph:arrow-left-duotone" class="w-4 h-4" />
                Volver al inicio
            </a>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import dayjs from 'dayjs'
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import relativeTime from 'dayjs/plugin/relativeTime'
import 'dayjs/locale/es'

dayjs.extend(utc)
dayjs.extend(timezone)
dayjs.extend(relativeTime)
dayjs.locale('es')

const props = defineProps({
    aviso: { type: Object, default: null },
})

const tz = computed(() => {
    try { return dayjs.tz.guess() } catch { return 'Europe/Madrid' }
})

const pageTitle = computed(() => {
    if (!props.aviso?.titulo) return 'Mantenimiento - TSEYOR.org'
    return props.aviso.titulo + ' - TSEYOR.org'
})

const inicio = computed(() => props.aviso ? dayjs.utc(props.aviso.inicio).tz(tz.value) : null)
const fin = computed(() => props.aviso?.fin ? dayjs.utc(props.aviso.fin).tz(tz.value) : null)
const ahora = computed(() => dayjs().tz(tz.value))

const estado = computed(() => {
    if (!inicio.value || !fin.value) return null
    if (ahora.value.isBefore(inicio.value)) return 'proximo'
    if (ahora.value.isAfter(fin.value)) return 'finalizado'
    return 'en_curso'
})

const fechaFormato = computed(() => {
    if (!inicio.value) return ''
    return inicio.value.format('dddd, D [de] MMMM [de] YYYY')
})

const horaInicio = computed(() => inicio.value?.format('HH:mm') ?? '')
const horaFin = computed(() => fin.value?.format('HH:mm') ?? '')

const tiempoRestante = computed(() => {
    if (!inicio.value) return ''
    return 'Comienza ' + inicio.value.fromNow()
})
</script>
