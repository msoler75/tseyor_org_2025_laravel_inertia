<script setup>
import { Head, usePage, router } from '@inertiajs/vue3';
import { useNav } from '@/Stores/nav'
import { useDark, useToggle } from "@vueuse/core";

const page = usePage()
const nav = useNav()
const sideBarShow = ref(false)
const anuncio = computed(() => page.props.anuncio || '');

nav.announce = !!anuncio

defineProps({
    title: String,
});

const portada = computed(() => page.url == '/')

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};

// DARK MODE
const isDark = useDark();
const toggleDark = useToggle(isDark);

function updateDarkState() {
    if (isDark.value)
        document.documentElement.setAttribute('data-theme', 'winter')
    else
        document.documentElement.removeAttribute('data-theme')
}

watch(isDark, value => {
    updateDarkState()
})

updateDarkState()
/*
// On page load or when changing themes, best to add inline in `head` to avoid FOUC
if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
  document.documentElement.classList.add('dark')
} else {
  document.documentElement.classList.remove('dark')
}

// Whenever the user explicitly chooses light mode
localStorage.theme = 'light'

// Whenever the user explicitly chooses dark mode
localStorage.theme = 'dark'

// Whenever the user explicitly chooses to respect the OS preference
localStorage.removeItem('theme')
*/


/*

tri-estado:


<template>
  <div :class="theme">
    <!-- Contenido de la aplicación aquí -->
  </div>
</template>

<script setup>
import { useDark, useColorScheme } from 'vue-use';

    const isDark = useDark();
    const colorScheme = useColorScheme();

    // Variable de estado que controla el tema actual
    const currentTheme = Vue.ref('system');

    // Función para actualizar el tema actual
    const updateTheme = () => {
      if (currentTheme.value === 'system') {
        currentTheme.value = colorScheme.value;
      }
    };

    // Actualiza el tema cuando cambia el color esquema
    Vue.watch(colorScheme, updateTheme);

    // Función para cambiar el tema manualmente
    const setTheme = (theme) => {
      currentTheme.value = theme;
    };

    // Vigila los cambios en la variable de estado currentTheme
    Vue.watch(currentTheme, (theme) => {
      if (theme === 'light') {
        document.documentElement.classList.remove('dark');
        document.documentElement.classList.add('light');
      } else if (theme === 'dark') {
        document.documentElement.classList.remove('light');
        document.documentElement.classList.add('dark');
      } else if (theme === 'system') {
        updateTheme();

        if (currentTheme.value === 'light') {
          document.documentElement.classList.remove('dark');
          document.documentElement.classList.add('light');
        } else if (currentTheme.value === 'dark') {
          document.documentElement.classList.remove('light');
          document.documentElement.classList.add('dark');
        }
      }
    });

    // Retorna la variable de estado currentTheme y la función setTheme
    return {
      currentTheme,
      setTheme,
    };
  },
};

*/

</script>

<template>
    <div class="">
        <Announcement :text="anuncio" :class="nav.fullPage ? 'w-full fixed top-0 z-40' : 'block'" />

        <NavAside :show="sideBarShow" @close="sideBarShow = false" class="lg:hidden" />

        <Head :title="title" />

        <Banner />

        <div class="bg-base-200">
            <nav class="w-full border-gray-300 dark:border-gray-700 bg-base-100 top-0 z-40 -translate-y-[1px] transition duration-400 "
                :data-theme="portada && nav.scrollY < 300 ? 'winter' : ''" :class="(portada && nav.scrollY < 300 ? 'dark bg-transparent ' : portada ? 'bg-opacity-20 hover:bg-opacity-100 transition duration-200 ' : 'border-b ') +
                    (nav.defaultClass + ' ' + (nav.fullPage ? 'fixed border-gray-300 ' : 'sticky ')) +
                    (nav.fullPage && nav.announce ? 'top-[2rem] ' : 'top-0 ')">
                <!-- Primary Navigation Menu -->

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 relative">

                        <div class="hidden lg:flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center" @mouseover="nav.closeTabs()">
                                <Link :href="route('portada')">
                                <ApplicationMark/>
                                </Link>
                            </div>

                            <button @click="toggleDark()" class="px-4 py-2 text-white bg-gray-600 dark:bg-purple-700">
                                Dark Toggle
                            </button>

                            <!-- Navigation Links -->
                            <div class="hidden top-navigation space-x-8 sm:-my-px sm:ml-10 sm:flex">

                                <template v-for="tab of nav.items" :key="tab.url">
                                    <NavLink v-if="tab.url" :href="tab.url" @mouseover="nav.closeTabs()"
                                        :active="!nav.activeTab && route().current(tab.route)">
                                        {{ tab.title }}
                                    </NavLink>
                                    <NavLink v-else @click="nav.toggleTab(tab)" @mouseover="nav.activateTab(tab)"
                                        :active="tab.open || (!nav.activeTab && nav.in(tab, route().current()))"
                                        class="relative navigation-tab">
                                        {{ tab.title }}
                                        <div v-show="tab.open"
                                            class="hover-helper absolute z-40  -left-[7rem] -right-[7rem] top-[88%]  h-6" />
                                    </NavLink>
                                </template>
                            </div>


                        </div>

                        <transition class="hidden lg:flex" enter-active-class="transition ease-out duration-200"
                            enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95">
                            <div v-show="nav.activeTab" class="absolute top-[120%]  mx-[5rem] z-40"
                                style="width:calc(100% - 10rem)">
                                <div v-if="nav.ghostTab && nav.ghostTab.submenu"
                                    class="w-full h-30 flex flex-col z-40 top-8 bg-base-100 shadow-lg rounded-md border-gray-100 border">
                                    <div class="flex justify-between gap-10 p-12">
                                        <div v-for="section, index of nav.ghostTab.submenu.sections" :key="index"
                                            class="flex-1">
                                            <div class="text-gray-500 my-5 uppercase tracking-widest text-xs">{{
                                                section.title }}
                                            </div>
                                            <div class="flex flex-col gap-7 mb-7">
                                                <Link :href="item.url" v-for="item of section.items" :key="item.url"
                                                    @click="nav.closeTabs"
                                                    class="flex gap-3 p-3 rounded-lg hover:bg-base-200 transition duration-100 cursor-pointer">
                                                <div class="flex justify-start" style="min-width:2.2rem">
                                                    <Icon :icon="item.icon" class="text-3xl text-blue-400 flex-shrink-0" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <strong class="item-lg">{{ item.title }}</strong>
                                                    <span class="text-gray-500 text-sm">{{ item.description }}</span>
                                                </div>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="nav.ghostTab.submenu.footer" v-html="nav.ghostTab.submenu.footer"
                                        class="p-5 bg-base-100" />
                                </div>
                            </div>
                        </transition>



                        <div v-if="$page.props.auth.user" class="hidden sm:flex sm:items-center sm:ml-6"
                            @mouseover="nav.closeTabs()">
                            <div class="ml-3 relative">
                                <!-- Teams Dropdown -->
                                <Dropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-base-100 dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.current_team.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Manage Team
                                                </div>

                                                <!-- Team Settings -->
                                                <DropdownLink
                                                    :href="route('teams.show', $page.props.auth.user.current_team)">
                                                    Team Settings
                                                </DropdownLink>

                                                <DropdownLink v-if="$page.props.jetstream.canCreateTeams"
                                                    :href="route('teams.create')">
                                                    Create New Team
                                                </DropdownLink>

                                                <!-- Team Switcher -->
                                                <template v-if="$page.props.auth.user.all_teams.length > 1">
                                                    <div class="border-t border-gray-200 dark:border-gray-600" />

                                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                                        Switch Teams
                                                    </div>

                                                    <template v-for="team in $page.props.auth.user.all_teams"
                                                        :key="team.id">
                                                        <form @submit.prevent="switchToTeam(team)">
                                                            <DropdownLink as="button">
                                                                <div class="flex items-center">
                                                                    <svg v-if="team.id == $page.props.auth.user.current_team_id"
                                                                        class="mr-2 h-5 w-5 text-green-400"
                                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                        viewBox="0 0 24 24" stroke-width="1.5"
                                                                        stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>

                                                                    <div>{{ team.name }}</div>
                                                                </div>
                                                            </DropdownLink>
                                                        </form>
                                                    </template>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos"
                                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <Avatar :user="$page.props.auth.user" :link="false"/>
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
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Perfil
                                        </div>

                                        <DropdownLink :href="route('profile.show')">
                                            Mi Cuenta
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

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center lg:hidden">
                            <button
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                                @click="sideBarShow = !sideBarShow">
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
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
                    class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div v-if="$page.props.auth.user" class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
                                <img class="h-10 w-10 rounded-full object-cover"
                                    :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </div>

                            <div>
                                <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                        </div>

                        <div v-if="$page.props.auth.user" class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                Profile
                            </ResponsiveNavLink>

                            <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')"
                                :active="route().current('api-tokens.index')">
                                API Tokens
                            </ResponsiveNavLink>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <ResponsiveNavLink as="button">
                                    Log Out
                                </ResponsiveNavLink>
                            </form>

                            <!-- Team Management -->
                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                <div class="border-t border-gray-200 dark:border-gray-600" />

                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Manage Team
                                </div>

                                <!-- Team Settings -->
                                <ResponsiveNavLink :href="route('teams.show', $page.props.auth.user.current_team)"
                                    :active="route().current('teams.show')">
                                    Team Settings
                                </ResponsiveNavLink>

                                <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')"
                                    :active="route().current('teams.create')">
                                    Create New Team
                                </ResponsiveNavLink>

                                <!-- Team Switcher -->
                                <template v-if="$page.props.auth.user.all_teams.length > 1">
                                    <div class="border-t border-gray-200 dark:border-gray-600" />

                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        Switch Teams
                                    </div>

                                    <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                        <form @submit.prevent="switchToTeam(team)">
                                            <ResponsiveNavLink as="button">
                                                <div v-if="$page.props.auth.user" class="flex items-center">
                                                    <svg v-if="team.id == $page.props.auth.user.current_team_id"
                                                        class="mr-2 h-5 w-5 text-green-400"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <div>{{ team.name }}</div>
                                                </div>
                                            </ResponsiveNavLink>
                                        </form>
                                    </template>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>



            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-base-100 dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main @mouseover="nav.closeTabs()" class="relative">
                <transition enter-active-class="transition-opacity duration-100"
                    leave-active-class="transition-opacity duration-100" enter-class="opacity-0" leave-to-class="opacity-0">
                    <div v-if="nav.activeTab" class="hidden lg:block z-30 absolute w-full h-full bg-black bg-opacity-10">
                        <!-- Contenido del elemento -->
                    </div>
                </transition>


                <slot />
            </main>

            <AppFooter v-if="!nav.fullPage"/>
        </div>
    </div>
</template>



<style scoped>
.top-navigation>.navigation-tab:nth-child(2)>.hover-helper {
    transform: translateX(4rem);
}

.top-navigation>.navigation-tab:nth-child(3)>.hover-helper {
    transform: translateX(1rem);
}

.top-navigation>.navigation-tab:nth-child(5)>.hover-helper {
    transform: translateX(-1rem);
}

.top-navigation>.navigation-tab:nth-child(6)>.hover-helper {
    transform: translateX(-4rem);
}
</style>

<style>
.navigation-tab {
    cursor: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='24' height='24'><path fill='black' d='M12 20.5l-9-9v-2l9 9 9-9v2z'></path></svg>") 16 16, auto;
}

.dark .navigation-tab {
    cursor: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='24' height='24'><path fill='white' d='M12 20.5l-9-9v-2l9 9 9-9v2z'></path></svg>") 16 16, auto;
}
</style>
