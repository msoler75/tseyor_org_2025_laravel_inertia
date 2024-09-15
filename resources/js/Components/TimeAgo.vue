<template>
    <span :title="formattedDate" class="timeago" @click="modoAgo = !modoAgo">{{
        modoAgo ? timeAgo : formattedDate }}</span>
</template>

<script setup>

const props = defineProps({
    date: String | Number,
    includeTime: { type: Boolean, default: true },
    short: { type: Boolean, default: false },
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

            var textual = ''
            if (diferencia < 60) {
                textual = `hace ${diferencia} ${pluralize('segundo', diferencia)}`;
            } else if (diferencia < 3600) {
                textual = `hace ${Math.floor(diferencia / 60)} ${pluralize('minuto', Math.floor(diferencia / 60))}`;
            } else if (diferencia < 86400) {
                textual = `hace ${Math.floor(diferencia / 3600)} ${pluralize('hora', Math.floor(diferencia / 3600))}`;
            } else if (diferencia < 604800) {
                textual= `hace ${Math.floor(diferencia / 86400)} ${pluralize('día', Math.floor(diferencia / 86400))}`;
            } else if (diferencia < 2592000) {
                textual = `hace ${Math.floor(diferencia / 604800)} ${pluralize('semana', Math.floor(diferencia / 604800))}`;
            } else if (diferencia < 31536000) {
                textual= `hace ${Math.floor(diferencia / 2592000)} ${pluralize('mes', Math.floor(diferencia / 2592000))}`;
            } else {
                textual= `hace ${Math.floor(diferencia / 31536000)} ${pluralize('año', Math.floor(diferencia / 31536000))}`;
            }

            if (props.short) {

                if (diferencia < 60) {
                    timeAgo.value = `${diferencia}s`;
                } else if (diferencia < 3600) {
                    timeAgo.value = `${Math.floor(diferencia / 60)} m`;
                } else if (diferencia < 86400) {
                    timeAgo.value = `${Math.floor(diferencia / 3600)} h`;
                } else if (diferencia < 604800) {
                    timeAgo.value = `${Math.floor(diferencia / 86400)} d`;
                } else if (diferencia < 2592000) {
                    timeAgo.value = `${Math.floor(diferencia / 604800)}sem`;
                } else if (diferencia < 31536000) {
                    timeAgo.value = `${Math.floor(diferencia / 2592000)}mes`;
                } else {
                    timeAgo.value = `${Math.floor(diferencia / 31536000)} a`;
                }

                return textual + ' \n' + title

            }

            timeAgo.value = textual

        }

        return title;
    }
});

function pluralize(word, count) {
    return count === 1 ? word : word.endsWith('s') ? `${word}es` : `${word}s`;
}
</script>
