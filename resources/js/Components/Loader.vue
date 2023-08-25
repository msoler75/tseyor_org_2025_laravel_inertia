<template>
    <svg :width="width" :height="width" :viewBox="`0 0 ${w} ${w}`" version="1.1">
        <defs>
    <radialGradient id="gradiente" cx="50%" cy="50%" r="50%" fx="50%" fy="50%">
      <stop offset="0%" style="stop-color: #ffffff; stop-opacity: 1" />
      <stop offset="60%" style="stop-color: #5087d8; stop-opacity: 1" />
      <stop offset="100%" style="stop-color: #5087d8; stop-opacity: 1" />
    </radialGradient>
  </defs>
        <circle style="display:inline;fill:#ffffff;fill-opacity:1;stroke:none;stroke-width:0.16666667" :cx="cx" :cy="cx" :r="cx" />
                <circle style="fill:#18419d;fill-opacity:1;stroke:none;stroke-width:0.16666667" :cx="cx" :cy="cx" :r="cx*.98" />
                <circle style="fill:#aec6ec;fill-opacity:1;stroke:none;stroke-width:0.16666667" :cx="cx" :cy="cx" :r="cx*.7" />
                <path
                style="fill:#ffffff;fill-opacity:1;stroke:none;stroke-width:0.16666667;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
                d="M 10.909884,33.092427 39.242175,33.055237 25.052897,8.5426185 Z" sodipodi:nodetypes="cccc" />
                <circle  :cx="cx" :cy="cx" style="fill: url(#gradiente); fill-opacity: 1; stroke: none; stroke-width: 0.04471808" :r="cx*0.27" />
        <circle v-for="circulo of circulos" style="fill:#e5eafe;fill-opacity:1;stroke:none;stroke-width:0.04393909"
        :cx="circulo.x" :cy="circulo.y" :r="cx*.073" :style="{
            animation: running ? 'spinAnim 3s linear infinite' : '',
            animationDelay: circulo.delay + 's',
        }" />

    </svg>
</template>


<script setup>
const props = defineProps(['running', 'width'])
const circulos = ref([])
var t = 3; //tiempo de animación
const w = 50
const cx = w/2
const wr = w * 0.425
for (var i = 1; i <= 12; i++) {
    var z = 360 / 12 * (Math.PI / 180);
    var x = wr * Math.sin(i * z) + w / 2;
    var y = -wr * Math.cos(i * z) + w / 2;
    circulos.value.push({ x, y, delay: ((i-1) * .5 * t / 12) })
}
</script>

<style>
@keyframes spinAnim {
    0% {
        opacity: 1
    }

    10% {
        opacity: 0
    }

    50% {
        opacity: 0
    }

    60% {
        opacity: 1
    }
}
</style>
