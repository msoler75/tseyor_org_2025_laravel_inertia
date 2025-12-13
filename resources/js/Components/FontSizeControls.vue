<template>
  <div class="font-controls flex items-center gap-3">
    <button
      type="button"
      class="btn  w-10 h-10 flex gap-1 justify-center items-center"
      :class="{ 'btn-disabled': theme.fontSize <= FONT_MIN }"
      :disabled="theme.fontSize <= FONT_MIN"
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
      :class="{ 'btn-disabled': theme.fontSize >= FONT_MAX }"
      :disabled="theme.fontSize >= FONT_MAX"
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

const theme = useTheme();
const FONT_MIN = 12;
const FONT_MAX = 24;
const FONT_STEP = 1;

function increase() {
  const next = Math.min(FONT_MAX, theme.fontSize + FONT_STEP);
  theme.fontSize = next;
}

function decrease() {
  const next = Math.max(FONT_MIN, theme.fontSize - FONT_STEP);
  theme.fontSize = next;
}
</script>
