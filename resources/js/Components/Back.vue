<template>
    <Link :href="enlace" class="flex h-fit gap-2 text-sm items-center hover:underline select-none"
    :class="nav.scrollY>90?'fixed bg-base-100 w-screen p-2 shadow top-[60px] md:top-[64px] xl:top-[68px] md:w-fit md:rounded-br-lg left-0 z-50':''"
    >
    <Icon icon="ph:arrow-left" />
    <slot />
    </Link>
</template>

<script setup>
const props = defineProps({
    href: String
})

const currentUrl = window.location.href;
const parentUrl = currentUrl.substring(0, currentUrl.lastIndexOf('/'));
const enlace = ref(props.href || parentUrl)

const nav = useNav()

</script>
