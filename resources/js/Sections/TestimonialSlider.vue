<template>
    <div class="testimonial-slider">
        <Splide :options="{ rewind: true }" aria-label="My Favorite Images">
            <SplideSlide v-for="(testimonial, index) in testimonials" :key="index">
                <Testimonial :testimonial="testimonial"/>
            </SplideSlide>
        </Splide>
    </div>
</template>

<script setup>
import { Splide, SplideSlide } from '@splidejs/vue-splide';

import '@splidejs/vue-splide/css/skyblue';

defineProps({
    testimonials: {}
})

</script>


<style>

.splide--slide {
    /* @apply flex py-12 h-[90%] my-auto;
    */
}
</style>
