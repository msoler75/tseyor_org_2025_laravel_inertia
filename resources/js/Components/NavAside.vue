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
                    <Link :href="route('portada')" @click="close">
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
                text-left text-base leading-4
                ">
                    <template v-for="tab, index in nav.items" :key="index">
                        <label class="w-full rounded-none mb-0" :class="tab.hasItems ? 'collapse collapse-arrow' : ''">
                            <Link v-if="!tab.hasItems && ('url' in tab)" :href="tab.url"
                                class="px-4 py-5 block transition duration-200 hover:bg-base-300" @click="close">{{
                                    tab.title }}</Link>
                            <template v-else>
                                <input v-if="tab.hasItems" type="checkbox" :v-model="tab.open"
                                    @change="toggle(tab, $event)" />
                                <div class="collapse-title">{{ tab.title }}</div>
                                <div class="collapse-content px-0   bg-base-100 bg-opacity-50" v-if="tab.hasItems">
                                    <template v-for="section, index of tab.submenu.sections" :key="index">
                                        <template v-for="group, index of section.groups" :key="index">
                                        <div @click.prevent.stop="null" v-if="group.title != tab.title"
                                            class="px-5 mt-4 font-bold text-xs text-neutral opacity-40 uppercase">
                                            {{ group.title }}</div>
                                        <div v-else class="mt-2" />

                                        <component :is="item.disabled ? 'div' : (item.external ? 'a' : Link)"
                                            :target="item.target" :href="item.url"
                                            v-for="item of group.items" :key="item.url" @click="close"
                                            class="nav-item p-5 flex justify-start items-center space-x-6 w-full transition duration-200 hover:bg-base-300"
                                            :class="item.disabled ? 'opacity-50 pointer-events-none' : ''">
                                            <Icon v-if="item.icon":icon="item.icon" />
                                            <component v-else-if="item.component" :is="item.component" class="w-4 h-4 flex-shrink-0"/>
                                            <span>{{ item.title }}</span>
                                        </component>
                                    </template>
                                    </template>
                                </div>
                            </template>
                        </label>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>




<style scoped>
.menu-item {
    transition: height 250ms cubic-bezier(0.3, 0, 0.6, 1);
}
</style>


<script setup>
import Link from './Link.vue';

const nav = useNav()

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    }
});

const emit = defineEmits(['close']);

const close = () => {
    console.log('sidenav close!')
    emit('close');
};

const toggle = (tab, event) => {
    // nav.toggleTab(tab)
    const elem = event.target
    console.log('toggle', tab, elem)
    if (!tab.open) {
        setTimeout(() => {
            elem.scrollIntoView({ behavior: 'smooth' })
        }, 300)
    }
}
</script>

