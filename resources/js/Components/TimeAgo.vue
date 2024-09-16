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

    if (typeof props.date === 'number') {
      // Si es un timestamp numérico, asumimos que está en segundos
      fechaPublicacion = new Date(props.date * 1000);
    } else if (typeof props.date === 'string') {
      // Si es una cadena, la parseamos directamente
      fechaPublicacion = new Date(props.date);
    } else if (props.date instanceof Date) {
      // Si ya es un objeto Date, lo usamos directamente
      fechaPublicacion = props.date;
    } else {
      // Si no es ninguno de los tipos esperados, retornamos una cadena vacía
      return '';
    }

    const fechaActual = new Date();

    // Formatear la fecha según la zona horaria del usuario
    const options = {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: props.includeTime ? 'numeric' : undefined,
      minute: props.includeTime ? 'numeric' : undefined,
      timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
    };

    const title = fechaPublicacion.toLocaleString('es-ES', options);

    const diferencia = Math.floor((fechaActual - fechaPublicacion) / 1000);

    const calcularTimeAgo = () => {
      if (diferencia < 60) return 'ahora mismo';
      if (diferencia < 3600) return `hace ${Math.floor(diferencia / 60)} ${pluralize('minuto', Math.floor(diferencia / 60))}`;
      if (diferencia < 86400) return `hace ${Math.floor(diferencia / 3600)} ${pluralize('hora', Math.floor(diferencia / 3600))}`;
      if (diferencia < 604800) return `hace ${Math.floor(diferencia / 86400)} ${pluralize('día', Math.floor(diferencia / 86400))}`;
      if (diferencia < 2592000) return `hace ${Math.floor(diferencia / 604800)} ${pluralize('semana', Math.floor(diferencia / 604800))}`;
      if (diferencia < 31536000) return `hace ${Math.floor(diferencia / 2592000)} ${pluralize('mes', Math.floor(diferencia / 2592000))}`;
      return `hace ${Math.floor(diferencia / 31536000)} ${pluralize('año', Math.floor(diferencia / 31536000))}`;
    };

    const calcularTimeAgoCorto = () => {
      if (diferencia < 60) return 'ahora';
      if (diferencia < 3600) return `${Math.floor(diferencia / 60)}m`;
      if (diferencia < 86400) return `${Math.floor(diferencia / 3600)}h`;
      if (diferencia < 604800) return `${Math.floor(diferencia / 86400)}d`;
      if (diferencia < 2592000) return `${Math.floor(diferencia / 604800)}sem`;
      if (diferencia < 31536000) return `${Math.floor(diferencia / 2592000)}mes`;
      return `${Math.floor(diferencia / 31536000)}a`;
    };

    timeAgo.value = props.short ? calcularTimeAgoCorto() : calcularTimeAgo();

    return title;
  });

  function pluralize(word, count) {
    return count === 1 ? word : word.endsWith('s') ? `${word}es` : `${word}s`;
  }
  </script>
