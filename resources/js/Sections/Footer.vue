<template>
    <footer class="w-full bg-gray-900 text-white border-t border-gray-300" data-theme="winter">
        <div class="container mx-auto py-14 lg:py-8">
            <GridFill>
                <div v-for="(section, index) in sections" :key="index" class="space-y-5 lg:space-y-2 mb-7">
                    <h3 class="text-lg font-bold">{{ section.title }}</h3>
                    <ul class="list-none space-y-5 lg:space-y-2">
                        <li v-for="(item, i) in section.items" :key="i">
                            <a :href="item.route?route(item.route):item.url" class="text-white">{{ item.label }}</a>
                        </li>
                    </ul>
                </div>
                <div class="space-y-5 lg:space-y-2 w-56">
                    <h3 class="text-lg font-bold">Formulario de suscripción</h3>
                    <!-- Aquí iría tu formulario de suscripción -->
                    <Suscribe/>
                </div>
                <div class="space-y-5 lg:space-y-2">
                    <h3 class="text-lg font-bold text-center">Redes Sociales</h3>
                    <div class="flex justify-center gap-7 lg:gap-4">
                        <a v-for="(social, i) in socialLinks" :key="i" :href="social.link" target="_blank" class="text-white">
                            <Icon :icon="social.icon" class="text-xl"></Icon>
                        </a>
                    </div>
                </div>
            </GridFill>
        </div>
    </footer>
</template>

<script setup>
import usePermisos from '@/Stores/permisos'

const permisos = usePermisos()

const props = defineProps({
    sections: Array,
    socialLinks: Array,
})
const columns = 3

</script>
