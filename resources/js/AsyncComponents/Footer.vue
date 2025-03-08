<template>
    <footer class="footer-component w-full bg-gray-900 text-white border-t border-gray-300" data-theme="winter">
        <div class="px-4 lg:container mx-auto py-14 lg:py-8">
            <div class="flex flex-wrap gap-8 justify-evenly">
                <div v-for="(section, index) in sections" :key="index" class="space-y-5 lg:space-y-2 mb-7">
                    <h3 class="text-lg font-bold">{{ section.title }}</h3>
                    <ul class="list-none space-y-5 lg:space-y-2 pl-0">
                        <li v-for="(item, i) in section.items" :key="i">
                            <a :href="item.route?route(item.route):encodeUrlAccents(item.url)" class="text-white">{{ item.label }}</a>
                        </li>
                    </ul>
                </div>
                <div v-if="suscription" class="space-y-5 lg:space-y-2 w-56">
                    <h3 class="text-lg font-bold">Formulario de suscripción</h3>
                    <!-- Aquí iría tu formulario de suscripción -->
                    <Suscribe/>
                </div>
                <div class="space-y-5 lg:space-y-2">
                    <h3 class="text-lg font-bold text-left">Redes Sociales</h3>
                    <div class="flex justify-start gap-7 lg:gap-4">
                        <a v-for="(social, i) in socialLinks" :key="i" :href="social.link" target="_blank" class="text-white">
                            <Icon :icon="social.icon" class="text-xl"></Icon>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- frase final de "todos los derechos reservados" -->
        <div class="w-full flex-shrink text-center text-sm pb-8 opacity-80">© {{new Date().getFullYear()}} TSEYOR. Todos los derechos reservados.</div>
    </footer>
</template>

<script setup>
const props = defineProps({
    sections: Array,
    socialLinks: Array,
    suscription: {type: Boolean, default: true}
})

function encodeUrlAccents(str) {
    return encodeURIComponent(str)
    .replace(/%2F/g, '/')
    .replace(/%2D/g, '-')
    .replace(/%23/g, '#');
}
</script>
