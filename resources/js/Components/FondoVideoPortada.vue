<template>
    <Section class="relative overflow-hidden">
        <video
            ref="video"
            src="/almacen/medios/portada/manos.mp4"
            autoplay
            muted
            class="absolute top-0 left-0 w-full h-full object-cover z-0
            mix-blend-overlay opacity-50"
            @ended="onVideoEnded"
            />
            <slot></slot>
    </Section>
</template>

<script setup>
const video = ref(null);

const onVideoEnded = () => {
    // Mantener el video en el último frame
    if (video.value) {
        video.value.currentTime = video.value.duration;
    }
};
</script>
