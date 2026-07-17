<template>
    <Dropdown align="right" width="56">
        <template #trigger>
            <button v-if="$page.props.jetstream.managesProfilePhotos"
                class="flex text-sm border-2 border-transparent rounded-full focus:outline-hidden focus:border-base-300 transition">
                <Avatar imageClass="w-10 h-10" :user="$page.props.auth.user" :link="false" class="hover:ring-4 ring-secondary/30 rounded-full" />
            </button>

            <span v-else class="inline-flex rounded-md">
                <button type="button"
                    class="inline-flex items-center px-3 py-2 border border-base-300 rounded-lg text-sm font-medium text-base-content/70 hover:text-base-content hover:border-base-400 focus:outline-hidden transition duration-150">
                    {{ $page.props.auth.user.name }}
                    <svg class="ml-2 h-4 w-4 text-base-content/40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
            </span>
        </template>

        <template #content>
            <div class="px-3 py-3">
                <div class="text-base font-semibold text-base-content leading-tight">{{ $page.props.auth.user.name }}</div>
            </div>

            <div class="border-t border-base-300" />

            <div class="px-1 py-1">
                <Link href="/miembros"
                    class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg hover:bg-base-200/50 transition-colors duration-150 whitespace-nowrap">
                    <Icon icon="ph:user-circle-duotone" class="text-lg text-base-content/50" />
                    Mi Panel
                </Link>
            </div>

            <div class="border-t border-base-300" />

            <div class="px-1 py-1">
                <form @submit.prevent="logout" class="whitespace-nowrap">
                    <DropdownLink as="button">
                        Cerrar Sesión
                    </DropdownLink>
                </form>
            </div>
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
