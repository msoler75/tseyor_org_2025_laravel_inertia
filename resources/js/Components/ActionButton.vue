<template>
    <Link :href="href" class="relative btn btn-primary inline-flex items-center gap-3 transition-all duration-300 group">
    <div class="px-2 absolute transition-all left-0 group-hover:left-full group-hover:-translate-x-full">
        <Icon icon="ph:arrow-right-duotone" class="transform scale-150" />
    </div>
    <div class="transition-all pl-4 group-hover:pl-0 group-hover:pr-4">
        <slot />
    </div>
    </Link>
</template>


<script setup>
const props = defineProps({
    href: { type: String, required: true }
})
</script>
