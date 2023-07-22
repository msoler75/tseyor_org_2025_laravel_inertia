<template>
    <div class="avatar" :title="user.name || user.nombre || user.slug">
        <div class="rounded-full" :class="imageClass">
            <User :user="user" :popupCard="popupCard" v-if="link" class="w-full h-full">
                <Image :src="image" :alt="name" :title="name" :fallback="fallbackImage"   />
            </User>
            <Image v-else :src="image" :alt="name" :title="name" :fallback="fallbackImage" />
        </div>
    </div>
</template>

<script setup>
const fallbackImage = ref('/storage/profile-photos/user.png')

const props = defineProps({
    user: { type: Object, required: true },
    link: { type: Boolean, default: true },
    big: { type: Boolean, default: false },
    popupCard: { type: Boolean, default: true }
})

const name = computed(() => props.user.name || props.user.nombre)
const image = computed(() => props.user.avatar || props.user.profile_photo_path || props.user.imagen)

const imageClass=computed(()=>{
    if(props.big) return 'w-32 h-32'
    return 'w-16 h-16'
})
</script>

<style scoped>
.avatar {
    @apply justify-center flex-shrink-0 flex-grow-0;
}
.avatar > * {
    @apply rounded-full overflow-hidden flex-shrink-0 ;
}
</style>
