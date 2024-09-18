<script setup>
import { onBeforeUnmount/*, markRaw*/ } from 'vue';
import { useDark, useToggle } from "@vueuse/core";
import usePermisos from '@/Stores/permisos'
// import usePlayer from '@/Stores/player'
import setTransitionPages from '@/composables/transitionPages.js'

// console.log('app initiating...')

// const player = usePlayer()

const permisos = usePermisos()
const page = usePage()
const nav = useNav()

// MENSAJE FLASH
const mostrarMensaje = ref(page.props?.flash?.message)


const TIME_NAV_INACTIVE = 600
var timerActivateNav = null


nav.announce = page.props.anuncio || ''

const portada = computed(() => page.url == '/')
// DARK MODE
const isDark = useDark();


function updateDarkState() {
    if (isDark.value)
        document.documentElement.setAttribute('data-theme', 'winter')
    else
        document.documentElement.removeAttribute('data-theme')
}



const handleScroll = () => {
    nav.scrollY = window.scrollY || window.pageYOffset
    // console.log('handleScroll', nav.scrollY)
}

// const dynamicAudioPlayer = ref(null);

// cuando el mouse sale de pantalla
function handleMouse() {
    // si el mouse sale de la ventana de la aplicación, cerramos el menú
    document.addEventListener("mouseleave", function (event) {
        // console.log('mouseleave')
        if (screen.width >= 1024) {
            clearTimeout(timerActivateNav)
            nav.hoverDeactivated = true
            // cerramos los submenús
            nav.closeTabs()
        }
    })

    // si el mouse entra en la ventana de la aplicación desde "arriba", pondremos el menú de navegación en no activable durante un tiempo
    document.addEventListener("mouseenter", function (event) {
        // console.log('mouseenter')
        if (screen.width >= 1024) {
            clearTimeout(timerActivateNav)
            timerActivateNav = setTimeout(() => {
                nav.hoverDeactivated = false
                nav.activateHoveredTab()
            }, TIME_NAV_INACTIVE)
        }
    })
}


console.log('APP INITIED')
if (page.props.auth?.user)
    permisos.cargarPermisos()

// aplicamos configuración de transiciones de pagina (fadeout y scroll)
setTransitionPages(router)


// inicializamos la navegación pasando la función "route" del componente, en el cliente
nav.init(route)

onMounted(() => {

    console.log('APP LAYOUT mounted')

    handleMouse()
    handleScroll();
    window.addEventListener('scroll', handleScroll, { passive: true });

    // cargamos el componente AudioPlayer más tarde
    /* setTimeout(() => {
        import('@/Components/AudioPlayer.vue').then(module => {
            dynamicAudioPlayer.value = markRaw(module.default);
        });
    }, 5000)*/



    // modo Dark
    watch(isDark, value => {
        updateDarkState()
    })

    updateDarkState()


    // para globalSearch
    // esto lo hacemos únicamente para el caso muy particular de que globalsearch pueda tambien ponerse en dark mode en la portada
    function updateBodyTheme() {
        const themePortada = portada.value && nav.scrollY < 300 ? 'winter' : ''
        document.querySelector("body").setAttribute('data-theme', themePortada)
    }

    watch(() => `${nav.scrollY}+${portada.value}`, () => {
        updateBodyTheme()
    })

    updateBodyTheme()




    // mover a la posición indicada
    if (window.location.hash) {
        setTimeout(() => {
            nav.scrollToId(window.location.hash.substring(1), 0)
        }, 500)
    }


    // si en la url hay un parámetro ?verified=1
    if (location.search.includes('verified=1'))
        // redirigimos a dashboard
        router.get(route('dashboard'))



});


onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll);
});

// no se utiliza (no es necesario, la página carga muy rápido)
const loader = ref(false)
loader.value = false
/*
axios.get(route('setting', 'navigation'))
    .then(response => {
        console.log('response', response.data.value)
        nav.setItems(response.data.value)
        loader.value = false
    })
*/

// const route = useRoute();


</script>

<template>
    <!-- App layout -->
    <div class="flex flex-col">

        <ScrollToTop class="text-4xl fixed bottom-7 right-7 z-40"/>

        <!-- Loader -->
        <div v-if="loader"
            class="fixed inset-0 flex justify-center items-center z-50 bg-black bg-opacity-50 backdrop-blur-lg">
            <Loader class="w-[7.77rem]" :running="true" />
        </div>


        <Announcement :class="nav.fullPage ? 'w-full fixed top-0 z-40' : 'block'" />

        <NavAside :show="nav.sideBarShow" @close="nav.sideBarShow = false" class="lg:hidden" />


        <Banner />

        <!-- <component :is="dynamicAudioPlayer" v-if="dynamicAudioPlayer" /> -->
        <AudioPlayer />


        <Modal :show="mostrarMensaje" centered max-width="md">
            <div class="p-5 mt-auto mb-auto">
                <p class="text-center">{{ $page.props?.flash?.message }}</p>
                <div class="py-3 flex justify-center">
                    <button @click.prevent="mostrarMensaje = false" type="button" class="btn btn-neutral">
                        Gracias
                    </button>
                </div>
            </div>
        </Modal>

        <div class="bg-base-200 flex-grow flex flex-col">

            <NavBar />

            <!-- Page Content -->
            <div @mouseover="nav.closeTabs()" class="flex-grow relative transition-opacity duration-200"
                :class="nav.fadingOutPage ? 'opacity-0 pointer-events-none' : ''">


                <transition enter-active-class="transition-opacity duration-100"
                    leave-active-class="transition-opacity duration-100" enter-class="opacity-0"
                    leave-to-class="opacity-0">

                    <div v-if="nav.activeTab"
                        class="hidden lg:block z-30 absolute w-full h-full bg-black bg-opacity-10">
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
            <AppFooter v-if="!nav.fullPage && !page.url.match(/^\/archivos/)" />
        </div>
    </div>
</template>

