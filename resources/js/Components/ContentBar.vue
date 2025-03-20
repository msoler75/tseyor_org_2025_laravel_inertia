<template>
     <TransitionFade>
            <div v-if="nav.scrollY > 230" class="fixed top-[4rem] left-0 right-0 p-3 bg-base-100 shadow-2xs z-10 cursor-pointer">
                    <slot />
            </div>
        </TransitionFade>
</template>

<script setup>

const nav = useNav()

</script>
