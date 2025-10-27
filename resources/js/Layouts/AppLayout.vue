<template>
    <div>
        <Banner />

        <ClientOnly>
            <Tools/>
        </ClientOnly>

        <ClientOnly>
            <ToolTextSearch/>
        </ClientOnly>

        <ClientOnly>
            <AudioVideoPlayer />
        </ClientOnly>

        <Announcement
            :class="nav.fullPage ? 'w-full fixed top-0 z-40' : 'block'"
        />

        <NavAside
            :show="nav.sideBarShow"
            @close="nav.sideBarShow = false"
            class="lg:hidden"
        />

        <Modal :show="mostrarMensaje" centered max-width="md">
            <div class="p-5 mt-auto mb-auto">
                <p class="text-center">{{ $page.props?.flash?.message }}</p>
                <div class="py-3 flex justify-center">
                    <button
                        @click.prevent="mostrarMensaje = false"
                        type="button"
                        class="btn btn-neutral"
                    >
                        Gracias
                    </button>
                </div>
            </div>
        </Modal>

        <Modal :show="player.requiereInteraccion" centered max-width="md">
            <div class="p-5 mt-auto mb-auto">
                <p class="text-center">
                    <strong>Pulsa en la pantalla para escuchar el audio</strong>
                </p>
            </div>
        </Modal>

        <div class="bg-base-200 grow flex flex-col">
            <NavBar />

            <!-- Page Content -->
            <div id="page-content"
                @mouseover="nav.closeTabs()"
                @click="ui.tools.toggleTools($event)"
                class="grow relative transition-opacity duration-200"
                :class="
                    nav.fadingOutPage ? 'opacity-0 pointer-events-none' : ''
                "
            >
                <transition
                    enter-active-class="transition-opacity duration-100"
                    leave-active-class="transition-opacity duration-100"
                    enter-class="opacity-0"
                    leave-to-class="opacity-0"
                >
                    <div
                        v-if="nav.activeTab"
                        class="hidden lg:block z-30 absolute w-full h-full bg-black/10"
                    >
                        <!-- Contenido del elemento -->
                    </div>
                </transition>

                <!-- <transition name="slide-fade">
                    <slot  />
                </transition>
                -->

                <slot />
            </div>

            <!--  queremos que si la ruta actual es /archivos, no se muestre el footer: -->
            <AppFooter
                v-if="!nav.fullPage && !page.url.match(/^\/(archivos|emails)/)"
                @mouseover="nav.closeTabs()"
                class="select-none"
            />
        </div>

        <!-- Notificaciones PWA -->
        <PWANotifications />
    </div>
</template>

<script setup>
import useUserStore from "@/Stores/user";
import setTransitionPages from "@/composables/transitionPages.js";
import PWANotifications from "@/Components/PWANotifications.vue";
import useUi from "@/Stores/ui";
import ClientOnly from '@duannx/vue-client-only';
import { usePWASession } from "@/composables/usePWASession.js";


const ui = useUi();
const player = ui.player
const nav = ui.nav
//import useRoute from "@/composables/useRoute.js";
//useRouteimport { useRoute } from 'ziggy-js';

// Usar el composable para preservar estado en PWA
const { isPWA, saveState, restoreState, initStatePreservation, isRestoring, hasCheckedRestoration } = usePWASession();

// Variable para cleanup de PWA
let pwaCleanup = null;

// const folderExplorer = useFolderExplorerStore();

// ...existing code... (font controls moved to FontSizeControls component)

// console.log('app initiating...')

const userStore = useUserStore();
const page = usePage();

//const route = useRoute();

// MENSAJE FLASH
const mostrarMensaje = ref(page.props?.flash?.message);

const TIME_NAV_INACTIVE = 600;
var timerActivateNav = null;

nav.announce = page.props.anuncio || "";

const handleScroll = () => {
    nav.scrollY = window.scrollY || window.pageYOffset;
    // console.log('handleScroll', nav.scrollY)

    // Guardar estado en PWA con throttling (máximo cada 2 segundos)
    if (isPWA()) {
        const now = Date.now();
        if (!handleScroll.lastSave || now - handleScroll.lastSave > 2000) {
            saveState();
            handleScroll.lastSave = now;
        }
    }
};

// cuando el mouse sale de pantalla
function handleMouse() {
    // si el mouse sale de la ventana de la aplicación, cerramos el menú
    document.addEventListener("mouseleave", function (event) {
        // console.log('mouseleave')
        if (screen.width >= 1024) {
            clearTimeout(timerActivateNav);
            nav.deactivateMenu();
        }
    });

    // si el mouse entra en la ventana de la aplicación desde "arriba", pondremos el menú de navegación en no activable durante un tiempo
    document.addEventListener("mouseenter", function (event) {
        // console.log('mouseenter')
        if (screen.width >= 1024) {
            clearTimeout(timerActivateNav);
            timerActivateNav = setTimeout(() => {
                nav.reactivateMenu();
            }, TIME_NAV_INACTIVE);
        }
    });

    document.addEventListener("click", handleInteraction);
}

function cargarDatosUsuario() {
    if (!page.props.auth?.user) {
        userStore.saldo = "";
        userStore.borrarPermisos();
        return;
    }
    userStore.cargarPermisos();
    userStore.cargarSaldo();
}

// si cambia el usuario
watch(
    () => page.props.auth?.user,
    () => {
        cargarDatosUsuario();
    }
);

// Guardar estado cuando cambia la página
watch(
    () => page.url,
    () => {
        // Pequeño delay para asegurar que el scroll se actualice
        if (isPWA()) {
            setTimeout(() => {
                saveState();
            }, 400);
        }
    }
);

// Ocultar loader inicial cuando se complete la restauración
watch(
    () => isRestoring.value,
    (newValue) => {
        if (!newValue) {
            const initialLoader = document.getElementById('pwa-initial-loader')
            if (initialLoader) {
                initialLoader.style.display = 'none'
            }
        }
    }
);

nav.init(route);

restoreState();
pwaCleanup = initStatePreservation();

onMounted(() => {

    // Ocultar el loader inicial de PWA cuando se complete la restauración
    const hideLoader = () => {
        const initialLoader = document.getElementById('pwa-initial-loader')
        if (initialLoader) {
            initialLoader.style.display = 'none'
        }
    }

    // Si no es PWA, ocultar inmediatamente
    if (!isPWA()) {
        hideLoader()
    } else {
        // Para PWA, esperar a que termine la restauración
        const unwatch = watch(
            () => isRestoring.value,
            (newValue) => {
                if (!newValue) {
                    hideLoader()
                    unwatch()
                }
            }
        )
    }

    // aplicamos configuración de transiciones de pagina (fadeout y scroll)
    setTransitionPages(router);

    cargarDatosUsuario();

    handleMouse();
    handleScroll();
    window.addEventListener("scroll", handleScroll, { passive: true });



    // mover a la posición indicada
    if (window.location.hash) {
        setTimeout(() => {
            console.log("scrollto_app");
            nav.scrollToId(window.location.hash.substring(1), 0);
        }, 500);
    }

    // si en la url hay un parámetro ?verified=1
    if (location.search.includes("verified=1"))
        // redirigimos a dashboard
        router.get(route("dashboard"));
});

onBeforeUnmount(() => {
    window.removeEventListener("scroll", handleScroll);

    // Limpiar event listeners de PWA y guardar estado final
    if (pwaCleanup) pwaCleanup();
    if (isPWA()) {
        saveState();
    }
});


// INTERACCION AUDIO

function handleInteraction() {
    // console.log("handleInteraction", player.requiereInteraccion);
    if (player.requiereInteraccion) player.playPause();
}


</script>

<style>


/* font button styles moved to component (use utility classes) */

/* Use the CSS variable as root font-size fallback for components that use rem/em */
:root {
    font-size: var(--app-font-size, 16px);
}
</style>
