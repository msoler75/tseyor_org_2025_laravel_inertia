<template>

    <div @click="toggleSocialShow">
        <slot>
            <button class="btn btn-xs btn-primary">
                <Icon icon="ph:share-network-duotone" />
                <span class="hidden xs:inline">Comparte</span>
            </button>
        </slot>
    </div>


    <Modal max-width="lg" :show="socialShow">

        <div class="p-5">

            <p class="font-bold text-base-content">Compartir
                &ldquo;<i>{{ sharing.title }}</i>&ldquo;
            </p>
            <div class="flex flex-wrap justify-start items-center gap-3"
                :style="{ color: '#eee', textShadow: '1px 1px black' }">
                <ShareNetwork v-for="network in networks" :network="network.network" :key="network.network"
                    :style="{ backgroundColor: network.color }" :url="currentUrl"
                    class="flex items-center gap-2 p-2 text-base hover:outline-double outline-black"
                    :class="network.cls" :title="sharing.title"
                    :quote="sharing.quote" :hashtags="sharing.hashtags" :twitterUser="sharing.twitterUser">
                    <Icon :icon="network.icon" class="text-lg" /> <span>{{ network.name }}</span>
                </ShareNetwork>

                <div v-if="isSupported" @click="copyCurrentUrl" :style="{ backgroundColor: clipboard.color }"
                    :url="currentUrl"
                    class="cursor-pointer flex items-center gap-2 p-2 text-base hover:outline-double outline-black"
                    :class="clipboard.cls" :title="sharing.title"
                    :quote="sharing.quote" :hashtags="sharing.hashtags" :twitterUser="sharing.twitterUser">
                    <Icon :icon="clipboard.icon" /> <span>{{ clipboard.name }}</span>
                </div>
            </div>

            <div class="py-3 flex justify-between sm:justify-end gap-5 mt-5">
                <button class="btn btn-primary" @click="socialShow = false">Cancelar</button>
            </div>

        </div>

    </Modal>
</template>

<script setup>
import { useClipboard } from '@vueuse/core'

const sharing = reactive({
    title: '',
    description: '',
    quote: '',
    hashtags: '',
    twitterUser: 'TSEYOR'
})

const currentUrl = ref()

const { copy, copied, isSupported } = useClipboard()

const copyCurrentUrl = () => {
    copy(currentUrl.value)
    alert('Se ha copiado el enlace al portapapeles')
}

// mostrar Modal
const socialShow = ref(false)

const toggleSocialShow = () => {
    socialShow.value = true;
}


function updateMeta() {
    sharing.title = document.title
    const metaDescription = document.querySelector('meta[name="description"]')
    sharing.description = metaDescription ? metaDescription.getAttribute('content') : ''
}

onMounted(() => {
    currentUrl.value = window.location.href.replace(/\?.*/, '')
    updateMeta()
    setTimeout(updateMeta, 250)

    watch(() => document.title, () => {
        console.warn('watch doc.title')
        setTimeout(updateMeta, 250)
    })
})


const networks = ref([
    //{ network: 'baidu', name: 'Baidu', icon: 'fas fah fa-lg fa-paw', color: '#2529d8' },
    //{ network: 'buffer', name: 'Buffer', icon: 'fab fah fa-lg fa-buffer', color: '#323b43' },
    { network: 'email', name: 'Correo', icon: 'material-symbols:mail', color: '#333333' },
    //{ network: 'evernote', name: 'Evernote', icon: 'fab fah fa-lg fa-evernote', color: '#2dbe60' },
    { network: 'facebook', name: 'Facebook', icon: 'famicons:logo-facebook', color: '#1877f2' },
    //{ network: 'flipboard', name: 'Flipboard', icon: 'fab fah fa-lg fa-flipboard', color: '#e12828' },
    //{ network: 'hackernews', name: 'HackerNews', icon: 'fab fah fa-lg fa-hacker-news', color: '#ff4000' },
    //{ network: 'instapaper', name: 'Instapaper', icon: 'fas fah fa-lg fa-italic', color: '#428bca' },
    //{ network: 'line', name: 'Line', icon: 'fab fah fa-lg fa-line', color: '#00c300' },
    //{ network: 'linkedin', name: 'LinkedIn', icon: 'fab fah fa-lg fa-linkedin', color: '#007bb5' },
    { network: 'messenger', name: 'Messenger', icon: 'uim:facebook-messenger', color: '#0084ff' },
    //{ network: 'odnoklassniki', name: 'Odnoklassniki', icon: 'fab fah fa-lg fa-odnoklassniki', color: '#ed812b' },
    //{ network: 'pinterest', name: 'Pinterest', icon: 'fab fah fa-lg fa-pinterest', color: '#bd081c' },
    //{ network: 'pocket', name: 'Pocket', icon: 'fab fah fa-lg fa-get-pocket', color: '#ef4056' },
    //{ network: 'quora', name: 'Quora', icon: 'fab fah fa-lg fa-quora', color: '#a82400' },
    { network: 'reddit', name: 'Reddit', icon: 'famicons:logo-reddit', color: '#ff4500' },
    //{ network: 'skype', name: 'Skype', icon: 'fab fah fa-lg fa-skype', color: '#00aff0' },
    //{ network: 'sms', name: 'SMS', icon: 'far fah fa-lg fa-comment-dots', color: '#333333' },
    //{ network: 'stumbleupon', name: 'StumbleUpon', icon: 'fab fah fa-lg fa-stumbleupon', color: '#eb4924' },
    { network: 'telegram', name: 'Telegram', icon: 'line-md:telegram', color: '#0088cc' },
    //{ network: 'tumblr', name: 'Tumblr', icon: 'fab fah fa-lg fa-tumblr', color: '#35465c' },
    { network: 'twitter', name: 'Twitter/X', icon: 'fa6-brands:x-twitter', color: '#111' },
    //{ network: 'viber', name: 'Viber', icon: 'fab fah fa-lg fa-viber', color: '#59267c' },
    //{ network: 'vk', name: 'Vk', icon: 'fab fah fa-lg fa-vk', color: '#4a76a8' },
    //{ network: 'weibo', name: 'Weibo', icon: 'fab fah fa-lg fa-weibo', color: '#e9152d' },
    { network: 'whatsapp', name: 'Whatsapp', icon: 'mdi:whatsapp', color: '#25d366' },
    //{ network: 'wordpress', name: 'Wordpress', icon: 'fab fah fa-lg fa-wordpress', color: '#21759b' },
    //{ network: 'xing', name: 'Xing', icon: 'fab fah fa-lg fa-xing', color: '#026466' },
    //{ network: 'yammer', name: 'Yammer', icon: 'fab fah fa-lg fa-yammer', color: '#0072c6' },
    //{ network: 'fakeblock', name: 'Custom Network', icon: 'fab fah fa-lg fa-vuejs', color: '#41b883' },
    // { network: 'clipboard', name: 'Copiar', icon: 'material-symbols:content-copy-sharp', color: '#aaa' },
])

const clipboard = {
    name: 'Copiar enlace', icon: 'material-symbols:content-copy-sharp', color: '#888'
}

</script>
