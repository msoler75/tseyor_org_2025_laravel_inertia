<script setup>
import { Link } from '@inertiajs/vue3';


const nav = useNav()

const props = defineProps({
    href: String,
    active: Boolean,
});

const baseClass = 'inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out';

const classes = computed(() => {
    return [
        baseClass,
        'text-gray-500 dark:text-gray-100 focus:text-gray-700 dark:focus:text-gray-300',
        props.active ? 'active' : 'border-transparent'
    ].join(' ');
});

function handleClick() {
    nav.closeTabs()
}
</script>

<template>
    <component :is="href ? Link : 'div'" :href="href" :class="classes" class="text-blue-900 dark:text-blue-50"
        @click="handleClick">
        <slot />
    </component>
</template>
