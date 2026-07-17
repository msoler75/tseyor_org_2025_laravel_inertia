<template>
    <div>
        <transition @click="close" enter-active-class="ease-in-out transition duration-150"
            leave-active-class="ease-in-out transition duration-150" enter-class="opacity-0" leave-to-class="opacity-0">
            <div v-if="show" class="fixed top-0 left-0 w-full h-full z-40" style="background:rgba(0,0,0,.5)"></div>
        </transition>

        <div class="fixed top-0 left-0 h-full bg-base-100 shadow-lg transition duration-200 z-50 overflow-y-auto"
            :class="!show ? '-translate-x-full' : ''">
            <span class="absolute text-2xl right-2 top-2 cursor-pointer" @click="close"><Icon icon="ph:x" /></span>
            <div class="rounded-r xl:hidden flex justify-between w-full p-6 items-center">

                <div class="flex justify-between  items-center space-x-3">
                    <Link :href="route('portada')" @click="close">
                    <ApplicationMark />
                    </Link>
                    <div class="text-2xl leading-6 font-bold font-serif">TSEYOR</div>
                </div>
            </div>

            <FontSizeControls class="px-6 py-4 border-b border-gray-300 dark:border-gray-600" />


            <div class="xl:rounded-r transform xl:translate-x-0 ease-in-out transition duration-500
                    flex justify-start items-start h-full w-full sm:w-64 flex-col">
                <div class="flex flex-col justify-start items-center  w-full
                divide-y divide-gray-300 dark:divide-gray-600 border-b border-gray-300
                bg-base-200
                text-left text-base leading-4
                ">
                    <template v-for="tab, index in visibleTabs" :key="index">
                        <label class="w-full rounded-none mb-0" :class="tab.hasItems ? 'collapse collapse-arrow' : ''">
                            <Link v-if="!tab.hasItems && ('url' in tab)" :href="tab.url"
                                class="px-4 py-5 block transition duration-200 hover:bg-base-300" @click="close">{{
                                    tab.title }}</Link>
                            <template v-else>
                                <input v-if="tab.hasItems" type="checkbox" v-model="tab.open"
                                    @change="toggle(tab, $event)" />
                                <div class="collapse-title !px-4 !py-5 !min-h-0">{{ tab.title }}</div>
                                <div class="collapse-content px-0 bg-base-100/50" v-if="tab.hasItems">
                                    <template v-for="section, index of tab.submenu.sections" :key="index">
                                        <template v-for="group, index of section.groups" :key="index">
                                        <component :is="item.disabled ? 'div' : (item.external ? 'a' : Link)"
                                            :target="item.target" :href="item.url"
                                            v-for="item of group.items" :key="item.url" @click="close"
                                            class="nav-item p-5 flex justify-start items-center space-x-6 w-full transition duration-200 hover:bg-base-300"
                                            :class="item.disabled ? 'opacity-50 pointer-events-none' : 'text-primary'">
                                            <Icon v-if="item.icon":icon="item.icon" />
                                            <component v-else-if="item.component" :is="item.component" class="w-4 h-4 shrink-0"/>
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
import FontSizeControls from '@/Components/FontSizeControls.vue';
import Link from '@/Components/Link.vue';

const nav = useNav()

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    }
});

const relativeUrl = (url) => url ? url.replace(/^https?:\/\/[^\/]+/, '') : ''

const visibleTabs = computed(() => {
    const tabs = []

    const EVENTOS_URL = relativeUrl(route('eventos'))

    for (const tab of nav.items) {
        if (!tab.hasItems) {
            tabs.push(tab)
            continue
        }

        const mobileItems = tab.submenu.sections.flatMap(s =>
            s.groups.flatMap(g =>
                g.items.filter(item =>
                    item.mobileShow && item.url !== EVENTOS_URL
                )
            )
        )

        const tabUrl = tab.url || ''
        const hasSubItems = mobileItems.some(i => i.url !== tabUrl)

        if (!hasSubItems && tabUrl) {
            tabs.push({ ...tab, hasItems: false, submenu: null })
            continue
        }

        if (!hasSubItems) continue

        tabs.push({
            ...tab,
            submenu: {
                ...tab.submenu,
                sections: tab.url && !mobileItems.some(i => i.url === tab.url)
                    ? [{
                        title: tab.title,
                        groups: [{
                            items: [
                                ...mobileItems,
                                { title: 'Más...', url: tab.url, icon: tab.icon },
                            ]
                        }]
                    }]
                    : [{ title: tab.title, groups: [{ items: mobileItems }] }],
            }
        })
    }

    const inicioIdx = tabs.findIndex(t => t.title === 'Inicio')
    tabs.splice(inicioIdx + 1, 0, {
        title: 'Eventos',
        icon: 'ph:calendar-duotone',
        url: EVENTOS_URL,
        hasItems: false,
    })

    return tabs
})

const emit = defineEmits(['close']);

const close = () => {
    console.log('sidenav close!')
    emit('close');
};

const toggle = (tab, event) => {
    // Mutex: si se abre un tab, cerrar los demás
    if (tab.open) {
        nav.items.forEach(t => {
            if (t !== tab && t.hasItems) t.open = false
        })
        setTimeout(() => {
            event.target.closest('label').scrollIntoView({ behavior: 'smooth' })
        }, 300)
    }
}
</script>

