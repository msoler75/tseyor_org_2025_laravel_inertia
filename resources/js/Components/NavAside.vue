<script setup>
import { Collapse } from 'vue-collapsed'
import { useNav } from '@/Stores/nav'

const nav = useNav()

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    }
});

const emit = defineEmits(['close']);

const close = () => {
    // console.log('close!')
    emit('close');
};




</script>

<template>
    <div>
        <transition @click="close" enter-active-class="ease-in-out transition duration-150"
            leave-active-class="ease-in-out transition duration-150" enter-class="opacity-0" leave-to-class="opacity-0">
            <div v-if="show" class="fixed top-0 left-0 w-full h-full z-40" style="background:rgba(0,0,0,.4)"></div>
        </transition>

        <div class="fixed top-0 left-0 h-full bg-base-100 shadow-lg transition duration-200 z-50 overflow-y-auto"
            :class="!show ? '-translate-x-full' : ''">
            <div class="rounded-r xl:hidden flex justify-between w-full p-6 items-center">
                <!--- more free and premium Tailwind CSS components at https://tailwinduikit.com/ --->
                <div class="flex justify-between  items-center space-x-3">
                    <p class="text-2xl leading-6 ">TSEYOR</p>
                </div>
            </div>
            <div
                class="xl:rounded-r transform  xl:translate-x-0  ease-in-out transition duration-500 flex justify-start items-start h-full  w-full sm:w-64  flex-col">
                <!--- more free and premium Tailwind CSS components at https://tailwinduikit.com/ --->

                <div class="hidden xl:flex justify-start p-6 items-center space-x-3">
                    <Icon icon="ion:chevron-up-outline" />
                    <p class="text-2xl leading-6 ">OvonRueden</p>
                </div>
                <div class="flex flex-col justify-start items-center   px-6  border-gray-600 w-full">
                    <template v-for="tab, index in nav.items" :key="index">
                        <button v-if="tab.submenu" @click="nav.toggleTab(tab)"
                            class="flex justify-between items-center w-full py-5">
                            <div class="text-sm leading-5  uppercase">{{ tab.title }}</div>
                            <Icon v-if="tab.open" icon="ion:chevron-up-outline" />
                            <Icon v-else icon="ion:chevron-down-outline" />
                        </button>
                        <Link v-else
                        class="flex justify-start items-center space-x-6   rounded px-3 py-5  w-full "
                         :href="tab.url">
                            <Icon :icon="tab.icon" />
                            <div class="text-base leading-4 ">{{ tab.title }}</div>
                        </Link>
                        <Collapse as="section" v-if="tab.submenu" :when="!!tab.open"
                            class="flex justify-start  flex-col w-full md:w-auto items-start pb-1 v-collapse">
                            <template v-for="section of tab.submenu.sections" :key="section.title">
                                <button v-for="item of section.items" :key="item.url"
                                    class="flex justify-start items-center space-x-6   rounded px-3 py-5  w-full ">
                                    <Icon :icon="item.icon" />
                                    <Link :href="item.url" class="text-base leading-4">{{ item.title }}</Link>
                                </button>
                            </template>
                        </Collapse>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>




<style scoped>
/* Estilos Tailwind CSS */
.sidebar {
    /* ... */
}

.menu-item {
    transition: height 250ms cubic-bezier(0.3, 0, 0.6, 1);
}

.v-collapse {
    transition: height 300ms cubic-bezier(0.33, 1, 0.68, 1);
}
</style>
