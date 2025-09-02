<template>
	<!-- Reusa CardContent internamente para conservar apariencia y props comunes -->
	<CardContent :title="title" :image="image" :href="href" :description="description" :date="date" :draft="draft">
		<template #default>
			<div v-if="futuro" class="mt-2 font-bold text-xs text-secondary text-right">PROXIMAMENTE</div>

			<div class="absolute right-2 top-2 rounded-xs shadow-lg bg-base-100 text-xl font-bold overflow-hidden">
				<span class="p-2 inline-block">
					{{ fechaMain }}
				</span>
				<div class="bg-primary text-primary-content text-sm text-center p-1">
					{{ fechaSuffix }}
				</div>
			</div>
		</template>
	</CardContent>
</template>

<script setup>
import { fechaEs } from '@/composables/textutils.js'

const props = defineProps({
	title: String,
	image: String,
	href: String,
	description: String,
	draft: { type: Boolean, default: false },
	// acepta fecha en crudo (por ejemplo evento.fecha_inicio). Mantengo compatibilidad
	// con la prop antigua 'fecha' si alguien la pasa.
	fechaInicio: { type: [String, Date], default: null },
    date: { type: [String, Date], default: null }
})

// formateo interno: preferir fechaInicio sobre fecha (compatibilidad)
const fechaFormateada = computed(() => {
	const raw = props.fechaInicio
	if (!raw) return ''
	try {
		return fechaEs(raw)
	} catch (e) {
		return String(raw)
	}
})

const futuro = computed(() => {
	const raw = props.fechaInicio
	if (!raw) return false
	const d = new Date(raw)
	return !isNaN(d) ? d >= new Date() : false
})

// separación de la fecha en dos partes igual que estaba inline en Index.vue
const fechaMain = computed(() => {
	if (!fechaFormateada.value) return ''
	const idx = fechaFormateada.value.lastIndexOf(' ')
	return idx === -1 ? fechaFormateada.value : fechaFormateada.value.substring(0, idx)
})
const fechaSuffix = computed(() => {
	if (!fechaFormateada.value) return ''
	const idx = fechaFormateada.value.lastIndexOf(' ')
	return idx === -1 ? '' : fechaFormateada.value.substring(idx)
})
</script>

<style scoped>
.rounded-xs {
	border-radius: 0.25rem;
}
</style>

