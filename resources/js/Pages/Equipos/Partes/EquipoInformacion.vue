<template>
    <div class="sm:card sm:bg-base-100 sm:shadow-2xs sm:p-5 flex gap-5 sm:col-span-2">
        <div class="w-full flex flex-wrap sm:flex-nowrap gap-5 justify-center items-center">
            <Image :src="equipo.imagen" alt="Imagen del equipo"
            :fallback="equipo_fallback"
                class="w-[150px] h-[150px] object-cover rounded-lg" />
            <div
                class="flex flex-col gap-5 justify-center items-center sm:justify-start sm:items-start text-center sm:text-left">
                <h3 class="my-0">
                    {{ equipo.nombre }}
                </h3>
                <div class="w-full opacity-80">{{ equipo.descripcion }}</div>
                <div v-if="equipo.categoria" class="badge badge-neutral">{{ equipo.categoria }}</div>
                <div class="sm:hidden flex justify-center text-2xl mt-12">
                    <Icon icon="ph:user-duotone" />
                    <span v-if="equipo.miembros">{{ equipo.miembros.length }}</span>
                </div>

            </div>

            <div v-if="equipo.miembros" class="hidden sm:flex sm:ml-auto gap-3 text-2xl items-center self-end justify-center">
                <Icon icon="ph:user-duotone" />
                {{ equipo.miembros.length }}
            </div>
        </div>
    </div>
</template>


<script setup>
defineProps({ equipo: { type: Object, required: true } })

const equipo_fallback = '/almacen/medios/equipos/equipo1.jpg'
</script>
