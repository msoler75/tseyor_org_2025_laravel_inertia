<template>
    <PageHeader title="Trabajos de Arte">
        <template #description>
            Espacio para compartir y descubrir las creaciones artísticas de la comunidad TSEYOR
        </template>
    </PageHeader>

    <PageWide>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="i in 6" :key="i"
                class="rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden group hover:shadow-md transition-all duration-200">
                <div class="aspect-[4/3] bg-gradient-to-br from-primary/5 to-secondary/5 flex items-center justify-center">
                    <Icon icon="ph:image-duotone" class="text-6xl text-gray-300 dark:text-gray-600" />
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Próximamente</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Los trabajos de arte de la comunidad se mostrarán aquí</p>
                </div>
            </div>
        </div>
    </PageWide>
</template>

<script setup>
</script>
