<template>
    <span :title="formattedDate" class="timeago" @click="modoAgo = !modoAgo">
        {{ modoAgo ? timeAgo : formattedDate }}
    </span>
</template>

<script setup>
const props = defineProps({
    date: [String, Number, Date],
    includeTime: { type: Boolean, default: true },
    short: { type: Boolean, default: false },
});

const page = usePage()

// Obtener el timestamp del servidor o usar la hora actual como fallback
const serverTimestamp = computed(() =>
    page?.props?.timestamp_server || Math.floor(Date.now() / 1000)
)

// Calcular la fecha del servidor basándote en la zona horaria de Madrid si no tienes el timestamp
const fechaServidor = computed(() => {
    if (page?.props?.timestamp_server) {
        return new Date(serverTimestamp.value * 1000)
    } else {
        const fechaLocal = new Date()
        const offsetMadrid = getTimezoneOffset('Europe/Madrid', fechaLocal)
        const offsetLocal = getTimezoneOffset(Intl.DateTimeFormat().resolvedOptions().timeZone, fechaLocal)
        const diferenciaOffset = offsetMadrid - offsetLocal

        // Cálculo de diferencia
        return new Date(fechaLocal.getTime() + (offsetMadrid - offsetLocal) * 60000)
    }
})



 // Función auxiliar para obtener el offset de una zona horaria
 function getTimezoneOffset(timeZone, date = new Date()) {
    // Corrección del cálculo del offset
    const formato = new Intl.DateTimeFormat('en', {
        timeZone,
        hour12: false,
        hour: 'numeric',
        minute: 'numeric',
        second: 'numeric'
    })

    const partes = formato.formatToParts(date)
    const horas = parseInt(partes.find(p => p.type === 'hour').value)
    const minutos = parseInt(partes.find(p => p.type === 'minute').value)

    return (date.getHours() - horas) * 60 + (date.getMinutes() - minutos)
}



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

    // Formatear la fecha según la zona horaria del usuario
    const options = {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: props.includeTime ? 'numeric' : undefined,
        minute: props.includeTime ?     'numeric' : undefined,
        timeZone: zonaHorariaLocal,
    };



    // Calcular la diferencia en segundos transcurridos
    const segundos = Math.floor((fechaServidor.value - fechaPublicacionLocal) / 1000);


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

    return fechaPublicacionLocal.toLocaleString('es-ES', options);
});

function pluralize(word, count) {
    return count === 1 ? word : word.endsWith('s') ? `${word}es` : `${word}s`;
}



// Actualizar cada minuto si no hay timestamp del servidor
/*
let intervalo
onMounted(() => {
    if (!page?.props?.timestamp_server) {
        intervalo = setInterval(() => {
            timeAgo.value = calcularTimeAgo()
        }, 60000)
    }
})

onBeforeUnmount(() => clearInterval(intervalo))
*/

</script>
