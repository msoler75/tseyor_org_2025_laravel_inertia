<template>
    <FadeOnNavigate class="w-full h-full">
        <footer
            class="footer-component flex flex-col justify-evenly w-full h-full bg-gray-900 text-white border-t border-gray-300 overflow-y-auto"
            data-theme="night"
        >
            <div class="px-4 lg:container mx-auto py-14 lg:py-8">
                <div class="flex flex-wrap gap-8 justify-evenly">
                    <div
                        v-for="(section, index) in sections"
                        :key="index"
                        class="space-y-5 sm:space-y-4 md:space-y-3 lg:space-y-2 mb-7"
                        >
                        <div class="title-footer">{{ section.title }}</div>
                        <ul class="list-none space-y-5 lg:space-y-2 pl-0">
                            <li v-for="(item, i) in section.items" :key="i">
                                <Link
                                    :href="
                                        item.route
                                            ? route(item.route)
                                            : encodeUrlAccents(item.url)
                                    "
                                    class="text-white hover:text-secondary"
                                    >{{ item.label }}</Link
                                >
                            </li>
                        </ul>
                    </div>
                    <div v-if="suscription" class="space-y-5 sm:space-y-4 md:space-y-3 lg:space-y-2 w-56">
                        <div class="title-footer">Formulario de suscripción</div>
                        <!-- Aquí iría tu formulario de suscripción -->
                        <Suscribe />
                    </div>
                    <div class="space-y-5 sm:space-y-4 md:space-y-3 lg:space-y-2 ">
                        <div class="title-footer text-left">Redes Sociales</div>
                        <div class="flex justify-start gap-7 lg:gap-4">
                            <a
                                v-for="(social, i) in socialLinks"
                                :key="i"
                                :href="social.link"
                                target="_blank"
                                class="text-white"
                            >
                                <Icon
                                    :icon="social.icon"
                                    class="text-xl"
                                ></Icon>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- frase final de "todos los derechos reservados" -->
            <div class="w-full shrink text-center text-sm pb-8 opacity-80">
                © {{ new Date().getFullYear() }} TSEYOR. Todos los derechos
                reservados.
            </div>
        </footer>
    </FadeOnNavigate>
</template>

<script setup>
const props = defineProps({
    sections: Array,
    socialLinks: Array,
    suscription: { type: Boolean, default: true },
});

function encodeUrlAccents(str) {
    return encodeURIComponent(str)
        .replace(/%2F/g, "/")
        .replace(/%2D/g, "-")
        .replace(/%23/g, "#");
}

const nav = useNav();
</script>

<style scoped>
@reference "../../css/app.css";

.title-footer {
    @apply mb-5 text-primary text-lg font-bold;
}
</style>
