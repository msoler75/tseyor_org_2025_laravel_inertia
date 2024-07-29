<template>

<component :is="link?User:'div'" v-if="!image || image.match(/^https:\/\/ui-avatars.com\/api\/\?name=/)" :user="user" class="font-normal">
    <div class="avatar placeholder">
        <div class="bg-neutral text-neutral-content rounded-full" :class="big?'w-32':'w-12'">
            <span :class="big?'text-4xl':''">{{ initials(name) }}</span>
        </div>
    </div>
</component>

    <div v-else class="avatar" :title="name">
        <div class="rounded-full" :class="imageClass">
            <User :user="user" :popupCard="popupCard" v-if="link" class="w-full h-full">
                <Image :src="image" :alt="name" :title="name" :fallback="fallbackImage" />
            </User>
            <Image v-else :src="image" :alt="name" :title="name" :fallback="fallbackImage" />
        </div>
    </div>
</template>

<script setup>
import User from '@/Components/User.vue'

import { initials } from '@/composables/textutils.js'

const fallbackImage = ref('/almacen/profile-photos/user.png')

const props = defineProps({
    user: { type: Object, required: true },
    link: { type: Boolean, default: true },
    big: { type: Boolean, default: false },
    imageClass: { type: String, required: false },
    popupCard: { type: Boolean, default: true }
})

console.log('preparing avatar. user=', props.user)

const name = computed(() => props.user.name || props.user.nombre || props.user.slug?.replace(/-/g, ' '))
const image = computed(() => props.user.avatar || props.user.profile_photo_url || props.user.imagen)

const imageClass = computed(() => {
    if (props.imageClass) return props.imageClass
    if (props.big) return 'w-32 h-32'
    return 'w-12 h-12'
})
</script>

<style scoped>
.avatar {
    @apply justify-center flex-shrink-0 flex-grow-0;
}

.avatar>* {
    @apply rounded-full overflow-hidden flex-shrink-0;
}
</style>
