<template>
    <nav class="w-full border-gray-300  top-0 z-40 -translate-y-[1px] transition duration-400 select-none"
            :data-theme="portada && nav.scrollY < 300 ? 'winter' : ''" :class="(portada && nav.scrollY < 300 ? 'dark bg-transparent ' : portada ? 'bg-opacity-20 hover:bg-opacity-100 transition duration-200 ' : 'border-b ') +
                    (nav.defaultClass + ' ' + (nav.fullPage ? 'fixed border-gray-300 ' : 'sticky ')) +
                    (nav.fullPage && nav.announce && !nav.announceClosed? 'top-[2rem] ' : 'top-0 ')">
                <!-- Primary Navigation Menu -->

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 relative items-center">

                        <!-- Hamburger -->
                        <div class="flex items-center lg:hidden">
                            <button
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                                :class="portada ? 'bg-base-300' : ''" @click="nav.sideBarShow = !nav.sideBarShow">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path
                                        :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>


                        <!-- Logo -->
                        <div class="hidden lg:flex shrink-0 items-center" @mouseover="nav.closeTabs()">
                            <Link :href="route('portada')">
                            <ApplicationMark />
                            </Link>
                        </div>


                        <!-- Main Navigation Tabs -->
                        <NavTabs class="hidden lg:flex top-navigation space-x-8 flex-grow justify-center" />

                        <div v-if="false && selectors.developerMode" class="mx-auto flex gap-2">
                            <!-- Area for dev tools -->
                        </div>



                        <transition class="hidden lg:flex" enter-active-class="transition ease-out duration-200"
                            enter-from-class="transform opacity-0 scale-95"
                            enter-to-class="transform opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95">
                            <NavSubmenu class="absolute top-[120%]  mx-[5rem] z-40 w-[calc(100%-10rem)]" />
                        </transition>


                        <div class="ml-auto flex items-center gap-3">

                            <GlobalSearch @mouseover="nav.closeTabs()"  />

                            <button @click="toggleDark()" @mouseover="nav.closeTabs()"
                                class="my-auto p-1 w-10 h-10 flex justify-center items-center rounded-full bg-base-300 shadow text-xl sm:ml-6">
                                <Icon v-show="isDark" icon="ph:sun-dim-duotone" />
                                <Icon v-show="!isDark" icon="ph:moon-duotone" />
                            </button>

                            <div v-if="$page.props.auth?.user" class="flex sm:items-center"
                                @mouseover="nav.closeTabs()">

                                <div class="ml-3 relative">
                                    <!-- Profile Dropdown -->
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <button v-if="$page.props.jetstream.managesProfilePhotos"
                                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                                <Avatar imageClass="w-12 h-12" :user="$page.props.auth.user"
                                                    :link="false" />
                                            </button>

                                            <span v-else class="inline-flex rounded-md">
                                                <button type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-base-100 dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                                    {{ $page.props.auth.user.name }}

                                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <!-- Account Management -->
                                            <div class="font-bold px-4 py-2">
                                                {{ $page.props.auth.user.name }}
                                            </div>

                                            <DropdownLink :href="route('mis_archivos')">
                                                Mis Archivos
                                            </DropdownLink>

                                            <DropdownLink href="/equipos?categoria=Mis equipos">
                                                Mis Equipos
                                            </DropdownLink>

                                            <DropdownLink :href="route('usuario', $page.props.auth.user.id)">
                                                Mi perfil público
                                            </DropdownLink>

                                            <DropdownLink v-if="false" href="/muul">
                                                Espacio Muul
                                            </DropdownLink>

                                            <DropdownLink :href="route('profile.show')">
                                                Mi Cuenta
                                            </DropdownLink>

                                            <DropdownLink v-if="permisos.permisos.length" href="/admin/dashboard"
                                                as="a">
                                                Panel de administrador
                                            </DropdownLink>

                                            <DropdownLink v-if="$page.props.jetstream.hasApiFeatures"
                                                :href="route('api-tokens.index')">
                                                API Tokens
                                            </DropdownLink>

                                            <div class="border-t border-gray-200 dark:border-gray-600" />

                                            <!-- Authentication -->
                                            <form @submit.prevent="logout">
                                                <DropdownLink as="button">
                                                    Cerrar Sesión
                                                </DropdownLink>
                                            </form>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>
                            <Link v-else :href="route('login')" class="text-2xl bg-base-300 rounded-full p-2 shadow"
                                @mouseover="nav.closeTabs()">
                            <Icon icon="ph:sign-in-duotone" title="Iniciar sesión" />
                            </Link>


                        </div>
                    </div>
                </div>
                <div id="afterNav" class="absolute top-[calc(100%_+_1px)] "></div>
            </nav>
            </template>



<script setup>
import { usePage } from '@inertiajs/vue3';
import useSelectors from '@/Stores/selectors'
import usePermisos from '@/Stores/permisos'
import { useDark, useToggle } from "@vueuse/core";

const page = usePage()
const nav = useNav()
const selectors = useSelectors()
const permisos = usePermisos()
const portada = computed(() => page.url == '/')
const isDark = useDark();
const toggleDark = useToggle(isDark);


const showingNavigationDropdown = ref(false);


const logout = () => {
    permisos.permisos = []
    router.post(route('logout'));
};





////////////////////////
// DEV LOGINS

onMounted(() => {
    window.addEventListener('keydown', handleKey);
})



function handleKey(event) {
    if (event.ctrlKey && event.key === 'i') {
        // event.preventDefault()
        selectors.developerMode = !selectors.developerMode
    }
}

////////////////////////////////////////////////////////////////


</script>
