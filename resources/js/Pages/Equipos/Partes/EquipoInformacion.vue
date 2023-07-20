<template>
    <div class="sm:card sm:bg-base-100 sm:shadow sm:p-5 flex gap-5 sm:col-span-2">
        <div class="w-full flex flex-wrap sm:flex-nowrap gap-5 justify-center items-center">
            <Image :src="equipo.imagen" alt="Imagen del equipo"
                class="w-[200px] sm:h-full sm:w-40 object-cover rounded-lg" />
            <div
                class="flex flex-col gap-5 justify-center items-center sm:justify-start sm:items-start text-center sm:text-left">
                <h3 class="my-0">
                    {{ equipo.nombre }}
                </h3>
                <div class="w-full opacity-80">{{ equipo.descripcion }}</div>
                <div class="badge badge-neutral">{{ equipo.categoria }}</div>
                <div class="sm:hidden flex justify-center text-2xl mt-12">
                    <Icon icon="ph:user-duotone" />
                    {{ equipo.usuarios.length }}
                </div>

            </div>

            <div class="hidden sm:flex sm:ml-auto gap-3 text-2xl items-center self-end justify-center">
                <Icon icon="ph:user-duotone" />
                {{ equipo.usuarios.length }}
            </div>
        </div>
    </div>
</template>


<script setup>
defineProps({ equipo: { type: Object, required: true } })
</script>
