<template>
    <div
        v-if="(nav.announce || nav.mantenimiento) && !nav.announceClosed"
        class="bg-primary text-primary-content px-4 lg:px-8 py-1 flex justify-between items-center gap-2"
    >
        <div class="text-transparent hidden sm:block">.....</div>
        <div class="text-sm text-center flex-1">
            <template v-if="nav.announce">
                <span v-html="nav.announce" />
            </template>
            <template v-else-if="nav.mantenimiento">
                <span v-if="nav.mantenimiento.esta_vigente" class="font-semibold">En curso: </span>
                <span v-else class="font-semibold">Próximo mantenimiento: </span>
                <span>{{ nav.mantenimiento.titulo }}</span>
                <span class="mx-1">&mdash;</span>
                <span>{{ formatoMantenimiento }}</span>
                <template v-if="nav.mantenimiento.url_info">
                    <a
                        :href="nav.mantenimiento.url_info"
                        class="underline ml-2 hover:text-white/80"
                    >Más información</a>
                </template>
            </template>
        </div>
        <button
            class="text-2xl shrink-0"
            @click="nav.announceClosed = true"
            aria-label="Cerrar anuncio"
            title="Cerrar anuncio"
        >
            <Icon icon="ph:x-square-duotone" aria-hidden="true" />
        </button>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import dayjs from 'dayjs'
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import 'dayjs/locale/es'

dayjs.extend(utc)
dayjs.extend(timezone)
dayjs.locale('es')

const nav = useNav()

const tz = computed(() => dayjs.tz.guess())

const formatoMantenimiento = computed(() => {
    const m = nav.mantenimiento
    if (!m) return ''

    const inicio = dayjs.utc(m.inicio).tz(tz.value)
    const fin = dayjs.utc(m.fin).tz(tz.value)

    const dia = inicio.format('dddd D [de] MMMM [de] YYYY')
    const horaInicio = inicio.format('HH:mm')
    const horaFin = fin.format('HH:mm')

    if (inicio.isSame(fin, 'day')) {
        return `${dia}, de ${horaInicio} a ${horaFin} (hora local)`
    }
    return `${inicio.format('D MMM HH:mm')} – ${fin.format('D MMM HH:mm')} (hora local)`
})
</script>
