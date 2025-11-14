<script setup>
import Link from '@/Components/Link.vue';

const nav = useNav()

const props = defineProps({
    href: String,
    active: Boolean,
});

const baseClass = 'inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 focus:outline-hidden transition duration-150 ease-in-out';

const classes = computed(() => {
    return [
        baseClass,
        'text-gray-500 dark:text-gray-100',
        props.active ? 'active' : 'border-transparent',
        props.href? 'hover:!text-secondary' : ''
    ].join(' ');
});


</script>

<template>
    <component :is="href ? Link : 'div'" :href="href" :class="classes">
        <slot />
    </component>
</template>
