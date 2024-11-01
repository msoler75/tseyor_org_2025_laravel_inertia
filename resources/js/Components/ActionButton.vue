<template>
    <component :is="external ? 'a' : Link" :href="href" class="pushable relative inline group btn px-0">
        <div class="w-full front inline-flex items-center gap-3 transition-all duration-300 btn btn-primary">
            <div class="px-2 absolute transition-all left-0 group-hover:left-full group-hover:-translate-x-full">
                <Icon icon="ph:arrow-right-duotone" class="transform scale-150" />
            </div>
            <div class="transition-all pl-4 group-hover:pl-0 group-hover:pr-4">
                <slot />
            </div>
        </div>
    </component>
</template>


<script setup>
import Link from '@/Components/Link.vue'
const props = defineProps({
    href: { type: String, required: true },
    external: { type: Boolean, default: false }
})
</script>


<style scoped>
.pushable {
    @apply rounded-xl bg-primary;
    border: none;
    cursor: pointer;
}

/** sombra */
.pushable:after {
    content: "";
    position: absolute;
    @apply rounded-xl;
    left: 0;
    top: 0;
    z-index: 1;
    width: 100%;
    height: 100%;
    background: rgb(0, 0, 0, .25);
    background: linear-gradient(90deg, rgba(0, 0, 0, .1) 0%, rgba(0, 0, 0, .6) 100%);
}

.pushable .front {
    transform: translateY(-6px);
    z-index: 2;
    position: relative;
}

.pushable:hover .front {
    transform: translateY(-6px);
}

.pushable:active .front {
    transform: translateY(-2px);
}
</style>
