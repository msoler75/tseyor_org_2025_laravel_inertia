<template>
    <span class="counter">{{ counter }}</span>
  </template>

  <script setup>
  import { ref, watch, onMounted, onUnmounted } from 'vue';

  const props = defineProps({
    from: {
      type: Number,
      required: false,
      default: 0
    },
    to: {
      type: Number,
      required: true,
    },
    duration: {
      type: Number,
      required: false,
      default: 1500
    },
    count: {
      type: Boolean,
      default: true,
    },
    delay: {
      type: Number,
      default: 0,
    },
  });

  const counter = ref(props.from);
  let requestId = null;
  let start = null;

  function animateCounter(timestamp) {
    if (!start) start = timestamp;
    const progress = Math.max(0,timestamp - start - props.delay);

    const diff = props.to - props.from;
    const value = props.from + diff * Math.min(progress / props.duration, 1);
    counter.value = Math.floor(value);

    if (progress < props.duration) {
      requestId = requestAnimationFrame(animateCounter);
    } else {
      requestId = null;
      counter.value = props.to;
    }
  }

  function startCounter() {
    if (requestId !== null) {
      cancelAnimationFrame(requestId);
    }

    requestId = requestAnimationFrame(animateCounter);
  }

  watch(
    () => props.count,
    (value) => {
      if (value) {
        setTimeout(() => {
          startCounter();
        }, props.delay);
      } else {
        cancelAnimationFrame(requestId);
        requestId = null;
      }
    },
  );

  onMounted(() => {
    if (props.count) {
      setTimeout(() => {
        startCounter();
      }, props.delay);
    }
  });

  onUnmounted(() => {
    cancelAnimationFrame(requestId);
    requestId = null;
  });

  // Verificar que los valores de entrada sean numéricos
  if (isNaN(props.from) || isNaN(props.to) || isNaN(props.duration) || isNaN(props.delay)) {
    console.error('Los valores de entrada para el contador no son numéricos');
  }
  </script>
