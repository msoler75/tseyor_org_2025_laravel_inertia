<template>

    <component :is="link ? User : 'div'" v-if="errorImage || !image || image.match(/^https:\/\/ui-avatars.com\/api\/\?name=/)"
        :user="user" class="font-normal">
        <div class="avatar avatar-placeholder">
            <div class="ring-neutral ring-offset-base-100 ring ring-offset-2 bg-base-200 rounded-full"
            :class="imageClass">
                <span class="text-base-content" :class="textClass">{{ initials(name) }}</span>
            </div>
        </div>
    </component>

    <div v-else class="avatar" :title="name">
        <div class="rounded-full" :class="imageClass">
            <User :user="user" :popupCard="popupCard" v-if="link" class="w-full h-full">
                <Image :src="image" :alt="name" :title="name" fallback-icon="ph:user-circle" :lazy="lazy" @error="errorImage=true" />
            </User>
            <Image v-else :src="image" :alt="name" :title="name" fallback-icon="ph:user-circle" :lazy="lazy" @error="errorImage=true"/>
        </div>
    </div>
</template>

<script setup>
import User from '@/Components/User.vue'

import { initials } from '@/composables/textutils.js'

const props = defineProps({
    user: { type: Object, required: true },
    link: { type: Boolean, default: true },
    imageClass: { type: String, required: false },
    textClass: { type: String, required: false },
    popupCard: { type: Boolean, default: true },
    lazy: {type: Boolean, default: false}
})

// console.log('preparing avatar. user=', props.user)

const name = computed(() => props.user.name || props.user.nombre || props.user.slug?.replace(/-/g, ' '))
const image = computed(() => props.user.avatar || props.user.profile_photo_url || props.user.imagen)

const errorImage = ref(false)

</script>

<style scoped>
@reference "../../css/app.css";

.avatar {
    @apply justify-center shrink-0 grow-0;
}

.avatar>* {
    @apply rounded-full overflow-hidden shrink-0;
}
</style>
