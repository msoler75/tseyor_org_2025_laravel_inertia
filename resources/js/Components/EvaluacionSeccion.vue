<template>
    <div class="evaluacion-seccion text-xs space-y-1 p-2 rounded-lg bg-base-200/50 border border-base-300 mb-4">
        <div class="flex items-center gap-1 mb-1">
            <span class="font-semibold uppercase tracking-wider text-[0.6rem] text-base-content/60">Evaluación</span>
            <span class="text-base-content/40">·</span>
            <span class="text-[0.6rem] text-base-content/40">{{ seccion }}</span>
        </div>
        <div class="flex flex-wrap gap-x-4 gap-y-1">
            <div v-for="criterio in criterios" :key="criterio.nombre" class="flex items-center gap-1.5">
                <span class="text-base-content/60 min-w-[4rem]">{{ criterio.etiqueta }}</span>
                <div class="w-16 h-1.5 bg-base-300 rounded-full overflow-hidden">
                    <div
                        class="h-full rounded-full transition-all duration-500"
                        :class="colorBarra(criterio.valor)"
                        :style="{ width: criterio.valor + '%' }"
                    ></div>
                </div>
                <span class="tabular-nums text-base-content/50 w-6">{{ criterio.valor }}%</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue"

const props = defineProps({
    seccion: {
        type: String,
        default: "",
    },
    idoneidad: {
        type: Number,
        default: 80,
    },
    claridad: {
        type: Number,
        default: 80,
    },
    composicion: {
        type: Number,
        default: 80,
    },
    posicion: {
        type: Number,
        default: 80,
    },
})

const criterios = computed(() => [
    { nombre: "idoneidad", etiqueta: "Idoneidad", valor: Math.min(100, Math.max(0, props.idoneidad)) },
    { nombre: "claridad", etiqueta: "Claridad", valor: Math.min(100, Math.max(0, props.claridad)) },
    { nombre: "composicion", etiqueta: "Composición", valor: Math.min(100, Math.max(0, props.composicion)) },
    { nombre: "posicion", etiqueta: "Posición", valor: Math.min(100, Math.max(0, props.posicion)) },
])

function colorBarra(valor) {
    if (valor >= 80) return "bg-emerald-500"
    if (valor >= 60) return "bg-amber-500"
    return "bg-red-500"
}
</script>
