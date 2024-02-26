<template>
     <TransitionFade>
            <div v-if="nav.scrollY > 230" class="fixed top-[4rem] left-0 right-0 p-3 bg-base-100 shadow z-10 cursor-pointer"
                @click="scrollToTopPage">
                <div class="container mx-auto flex justify-center gap-5 items-center ">
                    <Icon icon="ph:arrow-circle-up-duotone" class="transform scale-150 opacity-0" />
                    <span class="font-bold"><slot /></span>
                    <Icon icon="ph:arrow-circle-up-duotone" class="transform scale-150" />
                </div>
            </div>
        </TransitionFade>
</template>

<script setup>
import { useNav } from '@/Stores/nav'
import { scrollToTopPage } from '@/composables/contentbar.js'

const nav = useNav()

</script>
