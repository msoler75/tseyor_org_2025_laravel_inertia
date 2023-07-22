<template>
    <div class="avatar" :title="user.name || user.nombre || user.slug">
        <div class="w-32 rounded-full">
            <component :is="openTab?'a':Link" v-if="link" target="_blank" :href="route('usuario', { id: user.slug || user.id })">
            <Image :src="image" :alt="name" :title="name" fallback="/storage/profile-photos/user.png" />
            </component>
            <Image v-else :src="image" :alt="name" :title="name" fallback="/storage/profile-photos/user.png" />
        </div>
    </div>
</template>

<script setup>
import {Link} from '@inertiajs/vue3'
const props = defineProps({
    user: { type: Object, required: true },
    link: { type: Boolean, default: true },
    openTab: {type: Boolean, default: false}
})

const name = computed(() => props.user.name || props.user.nombre)
const image = computed(() => props.user.avatar || props.user.profile_photo_path || props.user.imagen)
</script>
