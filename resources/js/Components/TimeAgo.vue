<template>
    <span :title="formattedDate">{{ tiempo }}</span>
</template>

<script setup>
import { ref, computed } from 'vue';


const props = defineProps({
    date: String | Number
})

const tiempo = ref('');

const formattedDate = computed(() => {
    if (!props.date) {
        return '';
    } else {
        const fechaActual = new Date(); // Fecha actual
        const fechaPublicacion = props.date instanceof Date ? props.date : new Date(props.date * 1000);

        const title = fechaPublicacion.toLocaleString();

        // Verificar si la date es hoy sin tener en cuenta la hora
        if (
            fechaPublicacion.getFullYear() === fechaActual.getFullYear() &&
            fechaPublicacion.getMonth() === fechaActual.getMonth() &&
            fechaPublicacion.getDate() === fechaActual.getDate()
        ) {
            tiempo.value = 'hoy';
        } else {
            const diferencia = Math.floor((fechaActual.getTime() - fechaPublicacion.getTime()) / 1000);

            if (diferencia < 60) {
                tiempo.value = `hace ${diferencia} ${pluralize('segundo', diferencia)}`;
            } else if (diferencia < 3600) {
                tiempo.value = `hace ${Math.floor(diferencia / 60)} ${pluralize('minuto', Math.floor(diferencia / 60))}`;
            } else if (diferencia < 86400) {
                tiempo.value = `hace ${Math.floor(diferencia / 3600)} ${pluralize('hora', Math.floor(diferencia / 3600))}`;
            } else if (diferencia < 604800) {
                tiempo.value = `hace ${Math.floor(diferencia / 86400)} ${pluralize('día', Math.floor(diferencia / 86400))}`;
            } else if (diferencia < 2592000) {
                tiempo.value = `hace ${Math.floor(diferencia / 604800)} ${pluralize('semana', Math.floor(diferencia / 604800))}`;
            } else if (diferencia < 31536000) {
                tiempo.value = `hace ${Math.floor(diferencia / 2592000)} ${pluralize('mes', Math.floor(diferencia / 2592000))}`;
            } else {
                tiempo.value = `hace ${Math.floor(diferencia / 31536000)} ${pluralize('año', Math.floor(diferencia / 31536000))}`;
            }
        }

        return title;
    }
});

function pluralize(word, count) {
    return count === 1 ? word : `${word}s`;
}
</script>
