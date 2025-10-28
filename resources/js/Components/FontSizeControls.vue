<template>
  <div class="font-controls flex items-center gap-3">
    <button
      type="button"
      class="btn  w-10 h-10 flex gap-1 justify-center items-center"
      aria-label="Disminuir tamaño de fuente"
      @click="decrease"
      title="Disminuir fuente"
    >
      <span class="text-[0.75rem] font-semibold leading-none" aria-hidden="true">a</span>
      <span class="text-xl font-bold leading-none" aria-hidden="true">−</span>
    </button>
    <button
      type="button"
      class="btn w-10 h-10 flex gap-1 justify-center items-center"
      aria-label="Aumentar tamaño de fuente"
      @click="increase"
      title="Aumentar fuente"
    >
      <span class="text-[1.25rem] font-bold leading-none" aria-hidden="true">A</span>
      <span class="text-xl font-bold leading-none" aria-hidden="true">+</span>
    </button>
  </div>
</template>

<script setup>
import { onMounted, watch } from 'vue';
import useSelectors from '@/Stores/selectors';

const selectors = useSelectors();
const FONT_MIN = 12;
const FONT_MAX = 24;
const FONT_STEP = 1;

function applyFontSize(size) {
  if (typeof document !== 'undefined') {
    try {
      // set the Tailwind variable used in the CSS
      document.documentElement.style.setProperty('--text-base', size + 'px');
    } catch (e) {
      // noop
    }
  }
}

function increase() {
  const next = Math.min(FONT_MAX, selectors.tamanyoFuente + FONT_STEP);
  selectors.tamanyoFuente = next;
}

function decrease() {
  const next = Math.max(FONT_MIN, selectors.tamanyoFuente - FONT_STEP);
  selectors.tamanyoFuente = next;
}

// keep applied value in sync
watch(() => selectors.tamanyoFuente, (val) => applyFontSize(val), { immediate: true });

onMounted(() => applyFontSize(selectors.tamanyoFuente));
</script>
