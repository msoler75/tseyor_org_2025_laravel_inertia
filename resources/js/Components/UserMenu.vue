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
            <div class="font-bold px-4 py-2 text-gray-800 dark:text-gray-200">
                {{ $page.props.auth.user.name }}
            </div>

            <div class="px-2 pb-2">
                <Link href="/miembros"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 hover:bg-primary/20 text-primary hover:text-secondary border border-primary/20 transition-all duration-200 text-sm font-semibold">
                    <Icon icon="ph:user-circle-duotone" class="text-xl" />
                    Mi Panel
                    <Icon icon="ph:arrow-right" class="ml-auto text-lg" />
                </Link>
            </div>

            <FontSizeControls class="pl-3 pb-2"/>

            <div class="border-t border-gray-200 dark:border-gray-600" />

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
    router.post(route('logout'), {}, {
        onSuccess: () => userStore.borrarPermisos()
    });
};
</script>
