<template>
    <!-- App layout -->
    <div class="flex flex-col">
        <ScrollToTop
            class="text-4xl fixed right-7 z-40"
            :class="
                folderExplorer.seleccionando
                    ? 'bottom-20'
                    : player.closed
                    ? 'bottom-7'
                    : player.expanded
                    ? 'bottom-20'
                    : 'bottom-14'
            "
        />

        <!-- Loader -->
        <div
            v-if="loader"
            class="fixed inset-0 flex justify-center items-center z-50 bg-black/50 backdrop-blur-lg"
        >
            <Loader class="w-[7.77rem]" :running="true" />
        </div>

        <Announcement
            :class="nav.fullPage ? 'w-full fixed top-0 z-40' : 'block'"
        />

        <NavAside
            :show="nav.sideBarShow"
            @close="nav.sideBarShow = false"
            class="lg:hidden"
        />

        <Banner />

        <!-- <component :is="dynamicAudioPlayer" v-if="dynamicAudioPlayer" /> -->
        <ClientOnly>
            <AudioVideoPlayer />
        </ClientOnly>

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
            <div class="p-5 mt-auto mb-auto" @click="handleInteraction">
                <p class="text-center">
                    <strong>Pulsa en la pantalla para escuchar el audio</strong>
                </p>
            </div>
        </Modal>

        <div class="bg-base-200 grow flex flex-col">
            <NavBar />

            <!-- Page Content -->
            <div
                @mouseover="nav.closeTabs()"
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
                v-if="!nav.fullPage && !page.url.match(/^\/archivos/)"
                @mouseover="nav.closeTabs()"
                class="select-none"
            />
        </div>
    </div>
</template>

<script setup>
import useUserStore from "@/Stores/user";
import usePlayer from "@/Stores/player";
import setTransitionPages from "@/composables/transitionPages.js";
import useFolderExplorerStore from "@/Stores/folderExplorer";
//import useRoute from "@/composables/useRoute.js";
//useRouteimport { useRoute } from 'ziggy-js';

const folderExplorer = useFolderExplorerStore();

// console.log('app initiating...')

const player = usePlayer();
const userStore = useUserStore();
const page = usePage();
const nav = useNav();
//const route = useRoute();

// MENSAJE FLASH
const mostrarMensaje = ref(page.props?.flash?.message);

const TIME_NAV_INACTIVE = 600;
var timerActivateNav = null;

nav.announce = page.props.anuncio || "";

const handleScroll = () => {
    nav.scrollY = window.scrollY || window.pageYOffset;
    // console.log('handleScroll', nav.scrollY)
};

// const dynamicAudioPlayer = ref(null);

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

console.log("APP INITIED");

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

// const onlyTabs = typeof window === "undefined"
//  nav.init(route, nav.onlyTabs()); // en SSR solo las pestañas principales
//else
// if (typeof window === "undefined")

nav.init(route);

onMounted(() => {
    console.log("APP LAYOUT mounted");

    //console.log("route func:", route);

    // inicializamos la navegación pasando la función "route" del componente, en el cliente

    // aplicamos configuración de transiciones de pagina (fadeout y scroll)
    setTransitionPages(router);

    cargarDatosUsuario();

    handleMouse();
    handleScroll();
    window.addEventListener("scroll", handleScroll, { passive: true });

    // cargamos el componente AudioPlayer más tarde
    /* setTimeout(() => {
        import('@/Components/AudioPlayer.vue').then(module => {
            dynamicAudioPlayer.value = markRaw(module.default);
        });
    }, 5000)*/

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

    // TESTING
    /*
    setTimeout(()=>{
        nav.activateTab(nav.items[5])
    }, 250)
    */
});

onBeforeUnmount(() => {
    window.removeEventListener("scroll", handleScroll);
});

// no se utiliza (no es necesario, la página carga muy rápido)
const loader = ref(false);
loader.value = false;
/*
axios.get(route('setting', 'navigation'))
    .then(response => {
        console.log('response', response.data.value)
        nav.setItems(response.data.value)
        loader.value = false
    })
*/

// INTERACCION AUDIO

function handleInteraction() {
    if (player.requiereInteraccion) player.playPause();
}
</script>
