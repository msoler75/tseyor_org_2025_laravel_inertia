<template>
    <div>
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
        <!-- <PWANotifications /> -->
    </div>
</template>

<script setup>
import useUserStore from "@/Stores/user";
import setTransitionPages from "@/composables/transitionPages.js";
// PWANotifications ahora es asíncrono, registrado globalmente
import useUi from "@/Stores/ui";
import ClientOnly from '@duannx/vue-client-only';
import { usePWASession } from "@/composables/usePWASession.js";

const Tools = defineAsyncComponent(() => import('../Components/Tools.vue'))
const ToolTextSearch = defineAsyncComponent(() => import('../Components/ToolTextSearch.vue'))
const AudioVideoPlayer = defineAsyncComponent(() => import('../Components/AudioVideoPlayer.vue'))
// const NavAside = defineAsyncComponent(() => import('../Components/NavAside.vue'))


const ui = useUi();
const player = ui.player
const nav = ui.nav
// Usar el composable para preservar estado en PWA
const { isPWA, initPWA } = usePWASession();

console.log('app initiating...')

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
    // El guardado automático del scroll ahora lo maneja el composable usePWASession
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

// Guardar estado cuando cambia la página (ahora manejado por el composable)
// watch(
//     () => page.url,
//     () => {
//         // Pequeño delay para asegurar que el scroll se actualice
//         if (isPWA()) {
//             setTimeout(() => {
//                 saveState();
//             }, 400);
//         }
//         // Asegurar que el loader esté oculto después de navegación
//         // POR SI TODO FALLA
//         setTimeout(() => {
//             hideLoader()
//         }, 1900);
//     }
// );

// Ocultar loader inicial cuando se complete la restauración
// (Esta lógica ahora está manejada por el composable usePWASession)
// watch(
//     () => isRestoring.value,
//     (newValue) => {
//         console.log('[PWA] isRestoring cambió a:', newValue)
//         if (!newValue) {
//             const initialLoader = document.getElementById('pwa-initial-loader')
//             if (initialLoader) {
//                 console.log('[PWA] Ocultando loader inicial')
//                 initialLoader.style.display = 'none'
//             }
//         }
//     }
// );

nav.init(route);

// Inicializar PWA completamente (restauración, preservación de estado, loader)
initPWA(nav);

onMounted(() => {
    console.log('[PWA] AppLayout onMounted, isPWA:', isPWA())

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
    font-size: var(--text-base, 16px);
}
</style>
