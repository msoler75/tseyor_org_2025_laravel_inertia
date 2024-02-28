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
                    v-html="link.label" preserve-page @click="handleClick" />
            </template>
        </div>
    </div>
</template>

<script setup>


// const nav = useNav()

defineProps({
    links: Array,
})


function handleClick()
{
/*    nav.ignoreScroll = true
    if(!nav.scrollToContent("instant"))
        nav.scrollToTopPage()
    */
}
</script>
