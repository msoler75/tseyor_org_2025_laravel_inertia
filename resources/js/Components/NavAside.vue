<script setup>
import { Collapse } from 'vue-collapsed'


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
            <div v-if="show" class="fixed top-0 left-0 w-full h-full z-40" style="background:rgba(0,0,0,.5)"></div>
        </transition>

        <div class="fixed top-0 left-0 h-full bg-base-100 shadow-lg transition duration-200 z-50 overflow-y-auto"
            :class="!show ? '-translate-x-full' : ''">
            <div class="rounded-r xl:hidden flex justify-between w-full p-6 items-center">

                <div class="flex justify-between  items-center space-x-3">
                    <Link :href="route('portada')"
                    @click="close">
                                <ApplicationMark />
                                </Link>
                    <div class="text-2xl leading-6 font-bold font-serif">TSEYOR</div>
                </div>
            </div>
            <div class="xl:rounded-r transform xl:translate-x-0 ease-in-out transition duration-500
                    flex justify-start items-start h-full w-full sm:w-64 flex-col">
                <div class="flex flex-col justify-start items-center  w-full
                divide-y divide-gray-300 dark:divide-gray-600 border-b border-gray-300
                bg-base-200
                ">
                    <template v-for="tab, index in nav.items" :key="index">
                        <!--

                            <button v-if="tab.hasItems" @click="nav.toggleTab(tab)"
                            class="px-5 font-bold flex justify-between items-center w-full py-5
                            transition duration-300"
                            :class="tab.open?'shadow-lg bg-base-300':'shadow'">
                            <div class="text-sm leading-5  uppercase">{{ tab.title }}</div>
                            <Icon v-if="tab.open" icon="ion:chevron-up-outline" />
                            <Icon v-else icon="ion:chevron-down-outline" />
                        </button>
                        <Link v-else-if="tab.url||tab.route"
                        class="px-5 font-bold flex justify-start items-center space-x-6   rounded py-5  w-full "
                         :href="tab.url  || route(tab.route)" @click="close">
                         <Icon :icon="tab.icon" />
                         <div class="text-sm  uppercase leading-4 ">{{ tab.title }}</div>
                        </Link>
                    -->
                        <label
                            class="w-full rounded-none mb-0"
                            :class="tab.hasItems?'collapse collapse-arrow':''">
                            <input v-if="tab.hasItems" type="checkbox" :v-model="tab.open" />
                            <div class="collapse-title">{{ tab.title }}</div>
                            <div class="collapse-content bg-base-100 bg-opacity-50" v-if="tab.hasItems">
                                <template v-for="section of tab.submenu.sections" :key="section.title">
                                    <div v-if="section.title!=tab.title" class="px-5 mt-4 font-bold text-xs text-neutral opacity-40 uppercase">{{section.title}}</div>
                                    <div v-else class="mt-2"/>
                                    <button v-for="item of section.items" :key="item.url"
                                        class="px-3 flex justify-start items-center space-x-6   rounded  py-5  w-full ">
                                        <Icon :icon="item.icon" />
                                        <Link :href="item.url" class="text-left text-base leading-4"
                                        @click="close">{{ item.title }}</Link>
                                    </button>
                                </template>
                            </div>
                        </label>
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
