<template>
    <span :title="formattedDate" class="timeago" @click="modoAgo = !modoAgo">{{
        modoAgo ? timeAgo : formattedDate }}</span>
</template>

<script setup>

const props = defineProps({
    date: String | Number,
    includeTime: { type: Boolean, default: true },
})

const timeAgo = ref('');
const modoAgo = ref(true)

const formattedDate = computed(() => {
    if (!props.date) {
        return '';
    } else {
        const fechaActual = new Date(); // Fecha actual

        const fechaPublicacion =
            props.date instanceof Date
                ? props.date
                : typeof props.date === 'number'
                    ? new Date(props.date * 1000)
                    : new Date(props.date);

        const title = props.includeTime ? fechaPublicacion.toLocaleString() :
            fechaPublicacion.toLocaleDateString([], {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });

        // Verificar si la date es hoy sin tener en cuenta la hora
        if (
            fechaPublicacion.getFullYear() === fechaActual.getFullYear() &&
            fechaPublicacion.getMonth() === fechaActual.getMonth() &&
            fechaPublicacion.getDate() === fechaActual.getDate()
        ) {
            timeAgo.value = 'hoy';
        } else {
            const diferencia = Math.floor((fechaActual.getTime() - fechaPublicacion.getTime()) / 1000);

            if (diferencia < 60) {
                timeAgo.value = `hace ${diferencia} ${pluralize('segundo', diferencia)}`;
            } else if (diferencia < 3600) {
                timeAgo.value = `hace ${Math.floor(diferencia / 60)} ${pluralize('minuto', Math.floor(diferencia / 60))}`;
            } else if (diferencia < 86400) {
                timeAgo.value = `hace ${Math.floor(diferencia / 3600)} ${pluralize('hora', Math.floor(diferencia / 3600))}`;
            } else if (diferencia < 604800) {
                timeAgo.value = `hace ${Math.floor(diferencia / 86400)} ${pluralize('día', Math.floor(diferencia / 86400))}`;
            } else if (diferencia < 2592000) {
                timeAgo.value = `hace ${Math.floor(diferencia / 604800)} ${pluralize('semana', Math.floor(diferencia / 604800))}`;
            } else if (diferencia < 31536000) {
                timeAgo.value = `hace ${Math.floor(diferencia / 2592000)} ${pluralize('mes', Math.floor(diferencia / 2592000))}`;
            } else {
                timeAgo.value = `hace ${Math.floor(diferencia / 31536000)} ${pluralize('año', Math.floor(diferencia / 31536000))}`;
            }
        }

        return title;
    }
});

function pluralize(word, count) {
    return count === 1 ? word : word.endsWith('s') ? `${word}es` : `${word}s`;
}
</script>
