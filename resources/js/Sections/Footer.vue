<template>
    <footer class="w-full bg-gray-900 text-white border-t border-gray-300">
        <div class="container mx-auto py-8">
            <GridFill>
                <div v-for="(section, index) in sections" :key="index" class="space-y-2">
                    <h3 class="text-lg font-bold">{{ section.title }}</h3>
                    <ul class="list-none">
                        <li v-for="(item, i) in section.items" :key="i">
                            <a :href="route(item.route)" class="text-white">{{ item.label }}</a>
                        </li>
                    </ul>
                </div>
                <div class="space-y-2">
                    <h3 class="text-lg font-bold">Formulario de suscripción</h3>
                    <!-- Aquí iría tu formulario de suscripción -->
                </div>
                <div class="space-y-2">
                    <h3 class="text-lg font-bold text-center">Redes Sociales</h3>
                    <div class="flex justify-center gap-4">
                        <a v-for="(social, i) in socialLinks" :key="i" :href="social.link" class="text-white">
                            <Icon :icon="social.icon" class="text-xl"></Icon>
                        </a>
                    </div>
                </div>
            </GridFill>
        </div>
    </footer>
</template>

<script setup>

const props = defineProps({
    sections: Array,
    socialLinks: Array,
})
const columns = 3

</script>
