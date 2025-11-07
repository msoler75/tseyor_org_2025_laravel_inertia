<template>
    <nav
        class="w-full border-gray-300 top-0 z-40 -translate-y-px transition duration-400 select-none pointer-events-none"
        :class="[
            portada ? 'bg-base-100/0' :
                nav.fullPage
                ? 'bg-base-200/20 hover:bg-base-200/100 transition duration-200'
                : 'bg-base-200 border-b',
            nav.fullPage ? 'fixed border-gray-300' : 'sticky',
            nav.fullPage && nav.announce && !nav.announceClosed
                ? 'top-8 '
                : 'top-0 ',
        ]"
    >
        <!-- Primary Navigation Menu -->

        <div class="max-w-7xl mx-auto px-2 xs:px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 relative items-center pointer-events-auto gap-2">
                <!-- Hamburger -->
                <div class="flex items-center lg:hidden">
                    <button
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-hidden focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                        :class="portada ? 'bg-base-300' : ''"
                        @click="nav.sideBarShow = !nav.sideBarShow"
                    >
                        <svg
                            class="h-6 w-6"
                            stroke="currentColor"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                :class="{
                                    hidden: showingNavigationDropdown,
                                    'inline-flex': !showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{
                                    hidden: !showingNavigationDropdown,
                                    'inline-flex': showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Logo -->
                <div
                    class="hidden lg:flex shrink-0 items-center"
                    @mouseover="nav.closeTabs()"
                >
                    <Link :href="route('portada')" >
                        <ApplicationMark class="w-12 h-12 border-2 hover:ring-4 ring-secondary "/>
                    </Link>
                </div>


                <!-- Main Navigation Tabs -->
                <NavTabs
                    class="hidden h-full lg:flex top-navigation grow justify-center"
                />

                <div
                    v-if="false && selectors.developerMode"
                    class="mx-auto flex gap-2"
                >
                    <!-- Area for dev tools -->
                </div>


                <ClientOnly>
                    <transition
                        class="hidden lg:flex"
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                    >
                        <NavSubmenu
                            class="absolute top-[120%] mx-20 z-40 w-[calc(100%-10rem)]"
                        />
                    </transition>
                </ClientOnly>

                <div class="ml-auto flex items-center gap-2"
                    @mouseover="nav.closeTabs()"
                >
                    <GlobalSearch />

                    <button
                        @click="toggleDark()"
                        class="my-auto p-1 w-10 h-10 flex justify-center items-center rounded-full bg-base-300 shadow-2xs text-xl sm:ml-6 hover:text-secondary"
                        :aria-label="
                            'Cambiar a modo ' + (isDark ? 'claro' : 'oscuro')
                        "
                        :aria-pressed="isDark"
                        role="switch"
                    >
                    <ClientOnly>
                        <Icon
                            v-show="!isDark"
                            icon="ph:sun-dim-duotone"
                            aria-hidden="true"
                        />
                        <Icon
                            v-show="isDark"
                            icon="ph:moon-duotone"
                            aria-hidden="true"
                        />
                    </ClientOnly>
                    </button>

                    <div
                        v-if="$page.props.auth?.user"
                        class="flex sm:items-center"
                    >
                        <div class="relative">
                            <UserMenu />
                        </div>
                    </div>
                    <Link
                        v-else
                        :href="route('login')"
                        class="text-2xl bg-base-300 rounded-full p-2 shadow-2xs hover:text-secondary"
                    >
                        <Icon
                            icon="ph:sign-in-duotone"
                            title="Iniciar sesión"
                        />
                    </Link>
                </div>
            </div>
        </div>
        <div id="afterNav" class="absolute top-[calc(100%_+_1px)]"></div>
    </nav>
</template>

<script setup>
const ui = useUi()
const page = usePage();
const nav = ui.nav
const selectors = ui.selectors
const portada = computed(() => page.url == "/");

const showingNavigationDropdown = ref(false);

// Usar el store compartido del tema

const { isDark, toggleDark } = ui.theme


// Forzar sincronización de isDark tras el primer render y reflejar cualquier cambio inicial
onMounted(() => {
    nextTick(() => {
        // Si el valor inicial no coincide con el data-theme, lo corregimos
        const htmlTheme = document.documentElement.getAttribute('data-theme');
        if ((htmlTheme === 'night' && !isDark.value) || (htmlTheme === 'light' && isDark.value)) {
            isDark.value = htmlTheme === 'night';
        }
    });
});

// Si por alguna razón cambia el data-theme externo, sincronizar el botón
watch(
    () => document.documentElement.getAttribute('data-theme'),
    (newTheme) => {
        if ((newTheme === 'night' && !isDark.value) || (newTheme === 'light' && isDark.value)) {
            isDark.value = newTheme === 'night';
        }
    }
);

/*
function updateDarkState() {
    // Evitar ejecución en SSR
    if (typeof window === 'undefined') return;
    if (isDark.value)
        document.documentElement.setAttribute('data-theme', 'night')
    else
        document.documentElement.setAttribute('data-theme', 'summer')
}
*/

// para un caso especial de tema usando globalSearch
// esto lo hacemos únicamente para el caso muy particular de que globalsearch pueda tambien ponerse en dark mode en la portada
function updateSpecialCaseTheme() {
    if (typeof window === "undefined") return; // desactivado en SSR
    const themePortada = portada.value && nav.scrollY < 300 ? "night" : "";
    //document.querySelector("body").setAttribute("data-theme", themePortada);
}

onMounted(() => {

    console.log('navBar mounted')

    // El tema inicial ya se aplica automáticamente en el store

    window.addEventListener("keydown", handleKey);

    window.addEventListener("mousemove", handleMouse);

    watch(
        () => `${nav.scrollY}+${portada.value}`,
        () => {
            updateSpecialCaseTheme();
        }
    );

    updateSpecialCaseTheme();

});


function handleMouse(event) {
    // si el mouse se mueve muy rápido, entenderemos que el usuario no quiere ir al menú
    // console.log("mousemove", event.movementX, event.movementY);
    /*if (Math.abs(event.movementX) + Math.abs(event.movementY)>200) {
        nav.closeTabs();
        nav.movingFast = true
    }
    else {
        nav.movingFast = false
    }*/
}

////////////////////////
// DEV LOGINS

function handleKey(event) {
    if (event.ctrlKey && event.key === "i") {
        // event.preventDefault()
        selectors.developerMode = !selectors.developerMode;
    }
}

////////////////////////////////////////////////////////////////
</script>
