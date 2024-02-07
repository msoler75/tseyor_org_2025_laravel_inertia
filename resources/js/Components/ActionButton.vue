<template>
    <Link :href="href" class="pushable relative inline  group btn px-0">
    <div class="w-full front inline-flex items-center gap-3 transition-all duration-300 btn btn-primary">
        <div class="px-2 absolute transition-all left-0 group-hover:left-full group-hover:-translate-x-full">
            <Icon icon="ph:arrow-right-duotone" class="transform scale-150" />
        </div>
        <div class="transition-all pl-4 group-hover:pl-0 group-hover:pr-4">
            <slot />
        </div>
    </div>
    </Link>
</template>


<script setup>
const props = defineProps({
    href: { type: String, required: true }
})
</script>


<style scoped>
.pushable {
    @apply rounded-xl;
    border: none;
    cursor: pointer;
    background: #189dd8;
}

.pushable .front {
    transform: translateY(-6px);
}

.pushable:hover .front {
    transform: translateY(-6px);
}

.pushable:active .front {
    transform: translateY(-2px);
}
</style>
