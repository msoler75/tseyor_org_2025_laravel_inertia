<template>
    <!-- Profile Dropdown -->
    <Dropdown align="right" width="48">
        <template #trigger>
            <button v-if="$page.props.jetstream.managesProfilePhotos"
                class="flex text-sm border-2 border-transparent rounded-full focus:outline-hidden focus:border-gray-300 transition">
                <Avatar imageClass="w-10 h-10" :user="$page.props.auth.user" :link="false" class="hover:ring-8 ring-secondary rounded-full" />
            </button>

            <span v-else class="inline-flex rounded-md">
                <button type="button"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-base-100 dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-hidden focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                    {{ $page.props.auth.user.name }}

                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
            </span>
        </template>

        <template #content>
            <!-- Account Management -->
            <div class="font-bold px-4 py-2">
                <Link href="/dashboard">{{ $page.props.auth.user.name }}</Link>
            </div>

            <DropdownLink :href="route('mis_archivos')">
                Mis Archivos
            </DropdownLink>

            <DropdownLink href="/equipos?categoria=Mis equipos">
                Mis Equipos
            </DropdownLink>

            <DropdownLink v-if="$page.props.auth.user?.tiene_inscripciones_asignadas" :href="route('inscripciones.mis-asignaciones')">
                Gestión de inscritos
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

            <DropdownLink v-if="userStore.permisos.length" href="/admin/dashboard" as="a">
                Panel de administrador
            </DropdownLink>

            <FontSizeControls class="pl-4 pb-2"/>

            <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                API Tokens
            </DropdownLink>

            <div v-if="userStore.saldo != ''" class="border-t border-gray-200 dark:border-gray-600" />

            <DropdownLink as="a" href="/muular-electronico" v-if="userStore.saldo != '' && userStore.saldo != 'Error'"
                title="Muular electrónico">
                Saldo: <span class="font-bold">{{ userStore.saldo }}</span> <small>muulares</small>
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
</template>

<script setup>
import useUserStore from '@/Stores/user'
const userStore = useUserStore()

const logout = () => {
    userStore.borrarPermisos()
    router.post(route('logout'));
};
</script>
