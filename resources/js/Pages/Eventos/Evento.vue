<template>
    <div>
        <div class="sticky top-8 pt-10 bg-base-100 pb-4 border-b border-base-300 z-30">
            <div class="container mx-auto px-4 flex justify-between items-center mb-3">
                <Back>Eventos</Back>
                <AdminPanel modelo="evento" necesita="administrar social" :contenido="evento" />
            </div>

            <h1 class="container mx-auto my-2">
                {{ evento.titulo }}
            </h1>
            <small class="container mx-auto text-right block">{{ evento.categoria }}</small>
        </div>
        <div class="container py-7 mx-auto space-y-12">
            <div class="mx-auto flex flex-wrap md:flex-nowrap gap-7">
                <div class="w-full md:w-1/2 md:order-last">
                    <div class="card bg-base-100 md:max-w-[300px] shadow p-4">
                        <p class="mb-2 flex gap-3 items-center">
                            <Icon icon="ph:calendar-check-duotone" class="text-xl" /> Inicio: {{ evento.fecha_inicio }}
                        </p>
                        <p v-if="evento.fecha_fin" class="mb-2 flex gap-3 items-center">
                            <Icon icon="ph:calendar-x-duotone" class="text-xl" /> Fin: {{ evento.fecha_fin }}
                        </p>
                        <p v-if="evento.hora_inicio" class="mb-2 flex gap-3">
                            <Icon icon="ph:alarm-duotone" class="text-xl" /> Hora de inicio: {{ evento.hora_inicio }}
                        </p>
                        <p v-if="evento.hora_fin" class="mb-2">
                            <Icon icon="ph:clock-countdown-duotone" class="text-xl" /> Hora de fin: {{ evento.hora_fin }}
                        </p>
                        <p v-if="evento.lugar" class="mb-2 flex gap-3">
                            <Icon icon="ph:map-pin-duotone" class="text-xl" /> Lugar: {{ evento.lugar }}
                        </p>
                    </div>
                    <hr class="my-7" />
                    <p class="mt-4">{{ evento.descripcion }}</p>
                </div>
                <div class="w-full md:w-1/2">
                    <div class="lg:max-w-[500px]">
                        <Image :src="evento.imagen" alt="Imagen del evento" class="w-full mb-4" />
                        <Content :content="evento.texto" class="mb-4"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    evento: {
        type: Object,
        required: true,
    },
})

</script>
