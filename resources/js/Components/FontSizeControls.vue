<template>
  <div class="font-controls flex items-center gap-3">
    <button
      type="button"
      class="btn btn-info w-11 h-11 flex justify-center items-center"
      aria-label="Disminuir tamaño de fuente"
      @click="decrease"
      title="Disminuir fuente"
    >
      <span class="icon-small-a" aria-hidden="true">a</span>
      <span class="icon-sign" aria-hidden="true">−</span>
    </button>
    <button
      type="button"
      class="btn btn-info w-11 h-11 flex justify-center items-center"
      aria-label="Aumentar tamaño de fuente"
      @click="increase"
      title="Aumentar fuente"
    >
      <span class="icon-large-A" aria-hidden="true">A</span>
      <span class="icon-sign plus" aria-hidden="true">+</span>
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
      // set both our app variable and Tailwind-like variables used in the CSS
      document.documentElement.style.setProperty('--app-font-size', size + 'px');
      document.documentElement.style.setProperty('--text-base', size + 'px');
      // fallback for older or specific CSS using body font-size
      document.body.style.fontSize = size + 'px';
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

<!-- styles are provided via utility classes on buttons to avoid @apply in single-file component -->

<style scoped>
.icon-large-A {
  font-size: 1.45rem;
  font-weight: 700;
  line-height: 1;
  margin-right: 0.07rem;
}
.icon-small-a {
  font-size: .95rem;
  font-weight: 600;
  line-height: 1;
  margin-right: 0.07rem;
}
.icon-sign {
  font-size: 1.25rem;
  font-weight: 700;
  line-height: 1;
}
.icon-sign.plus {
  color: #065f46; /* darker green for contrast (optional) */
}
</style>
