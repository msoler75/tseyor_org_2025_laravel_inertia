<template>
    <span :title="formattedDate" class="timeago" @click="modoAgo = !modoAgo">
        {{ modoAgo ? timeAgo : formattedDate }}
    </span>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    date: [String, Number, Date],
    includeTime: { type: Boolean, default: true },
    short: { type: Boolean, default: false },
});

const timeAgo = ref('');
const modoAgo = ref(true);

const formattedDate = computed(() => {
    if (!props.date) {
        return '';
    }

    let fechaPublicacion;
    const zonaHorariaMadrid = 'Europe/Madrid';
    const zonaHorariaLocal = Intl.DateTimeFormat().resolvedOptions().timeZone;

    if (typeof props.date === 'number') {
        // Si es un timestamp numérico, asumimos que está en segundos y en la zona horaria de Madrid
        fechaPublicacion = new Date(props.date * 1000);
    } else if (typeof props.date === 'string') {
        // Si es una cadena, la parseamos asumiendo que está en la zona horaria de Madrid
        fechaPublicacion = new Date(props.date);
    } else if (props.date instanceof Date) {
        // Si ya es un objeto Date, lo usamos directamente
        fechaPublicacion = new Date(props.date);
    } else {
        // Si no es ninguno de los tipos esperados, retornamos una cadena vacía
        return '';
    }

    // Convertir la fecha de publicación a la zona horaria local
    const offsetMadrid = getTimezoneOffset(zonaHorariaMadrid, fechaPublicacion);
    const offsetLocal = getTimezoneOffset(zonaHorariaLocal, fechaPublicacion);
    const diferenciaOffset = offsetLocal - offsetMadrid;
    const fechaPublicacionLocal = new Date(fechaPublicacion.getTime() - diferenciaOffset * 60000);

    const fechaActual = new Date();

    // Formatear la fecha según la zona horaria del usuario
    const options = {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: props.includeTime ? 'numeric' : undefined,
        minute: props.includeTime ? 'numeric' : undefined,
        timeZone: zonaHorariaLocal,
    };

    const title = fechaPublicacionLocal.toLocaleString('es-ES', options);

    // Calcular la diferencia en segundos
    const segundos = Math.floor((fechaActual - fechaPublicacionLocal) / 1000);

    // Función auxiliar para obtener el offset de una zona horaria
    function getTimezoneOffset(timeZone, date = new Date()) {
        const tz = date.toLocaleString('en', { timeZone, timeStyle: 'long' }).split(' ').slice(-1)[0];
        const dateInTz = new Date(date.toLocaleString('en', { timeZone }));
        const offsetInMinutes = (date.getTime() - dateInTz.getTime()) / 60000;
        return offsetInMinutes;
    }

    const calcularTimeAgo = () => {
        if (segundos < 60) return 'ahora mismo';
        if (segundos < 3600) return `hace ${Math.floor(segundos / 60)} ${pluralize('minuto', Math.floor(segundos / 60))}`;
        if (segundos < 86400) return `hace ${Math.floor(segundos / 3600)} ${pluralize('hora', Math.floor(segundos / 3600))}`;
        if (segundos < 604800) return `hace ${Math.floor(segundos / 86400)} ${pluralize('día', Math.floor(segundos / 86400))}`;
        if (segundos < 2592000) return `hace ${Math.floor(segundos / 604800)} ${pluralize('semana', Math.floor(segundos / 604800))}`;
        if (segundos < 31536000) return `hace ${Math.floor(segundos / 2592000)} ${pluralize('mes', Math.floor(segundos / 2592000))}`;
        return `hace ${Math.floor(segundos / 31536000)} ${pluralize('año', Math.floor(segundos / 31536000))}`;
    };

    const calcularTimeAgoCorto = () => {
        if (segundos < 60) return 'ahora';
        if (segundos < 3600) return `${Math.floor(segundos / 60)}m`;
        if (segundos < 86400) return `${Math.floor(segundos / 3600)}h`;
        if (segundos < 604800) return `${Math.floor(segundos / 86400)}d`;
        if (segundos < 2592000) return `${Math.floor(segundos / 604800)}sem`;
        if (segundos < 31536000) return `${Math.floor(segundos / 2592000)}mes`;
        return `${Math.floor(segundos / 31536000)}a`;
    };

    timeAgo.value = props.short ? calcularTimeAgoCorto() : calcularTimeAgo();

    return title;
});

function pluralize(word, count) {
    return count === 1 ? word : word.endsWith('s') ? `${word}es` : `${word}s`;
}
</script>
