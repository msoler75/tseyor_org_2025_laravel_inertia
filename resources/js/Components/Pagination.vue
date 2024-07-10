<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap -mb-1">
            <template v-for="(link, key) in links">
                <div v-if="link.url === null" :key="key"
                    class="border-black/20 dark:border-white/20 mb-1 mr-1 px-4 py-3 text-gray-400 dark:text-gray-600 text-sm leading-4 border rounded"
                    v-html="link.label" />
                <Link v-else :key="`link-${key}`"
                    class="border-black/20 dark:border-white/20 mb-1 mr-1 px-4 py-3 focus:text-primary text-sm leading-4 hover:bg-base-100 border focus:border-primary rounded"
                    :class="{ 'bg-primary': link.active, 'text-primary-content': link.active }" :href="link.url"
                    v-html="link.label" preserve-page @click="handleClick" :preserve-scroll="preserveScroll"
                    :preserve-state="preserveState" :replace="replace" :only="only"
                    @finish="emit('finish')"
                     />
            </template>
        </div>
    </div>
</template>

<script setup>


// const nav = useNav()

const props = defineProps({
    links: Array,
    preserveScroll: {
        type: [Boolean, Function],
        default: true /* ESTA ES LA DIFERENCIA CON EL LINK DE INERTIA */
    },
    preserveState: {
        type: [Boolean, Function, null],
        default: null
    },
    replace: {
        type: Boolean,
        default: false
    },
    only: {
        type: Array,
        default: () => []
    },
    scrollTo: String
})

const emit = defineEmits(['click', 'finish'])


function handleClick() {
    /*    nav.ignoreScroll = true
        if(!nav.scrollToContent("instant"))
            nav.scrollToTopPage()
        */
    emit('click')
    if (props.scrollTo) {
        const elem = document.querySelector(props.scrollTo)
        if (elem) {
            window.scrollTo({
                top: elem.offsetTop,
                behavior: 'instant'
            });
        }
    }
}
</script>
