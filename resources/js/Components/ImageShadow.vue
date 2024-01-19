<template>
    <div class="vue-image-shadow" :class="shadowClass" :style="shadowStyle">
        <Image class="vue-image-shadow-img" :src="finalSrc" :alt="alt" :width="width" :style="imgStyle" />
        <div class="vue-image-shadow-card" :style="cardStyle">
        </div>
    </div>
</template>

<script setup>
import { getImageUrl } from '@/composables/imageutils.js'

const props = defineProps({
    className: {
        type: String
    },
    shadowBlur: {
        type: Number,
        default: 20
    },
    shadowHover: {
        type: Boolean,
        default: false
    },
    shadowRadius: {
        type: Number,
        default: 8
    },
    src: {
        type: String,
        required: true
    },
    alt: {
        type: String,
    },
    width: {
        type: Number,
        default: 300
    },
});

const shadowClass = ref({});
const shadowStyle = ref({});
const imgStyle = ref({});
const cardStyle = ref({});
const finalSrc = computed(() => getImageUrl(props.src))

onMounted(() => {
    loadStyle();
});

const loadStyle = () => {
    // shadowClass
    shadowClass.value = props.shadowHover ? `vue-image-shadow-hover ${props.className}` : props.className;
    // shadowStyle
    shadowStyle.value = {
        "width": `${props.width}px`
    };
    // imgStyle
    imgStyle.value = {
        "border-radius": `${props.shadowRadius}px`
    };
    // cardStyle
    cardStyle.value = {
        "background-image": `url('${finalSrc.value}')`,
        "border-radius": `${props.shadowRadius}px`,
        "filter": `blur(${props.shadowBlur}px)`
    };

};
</script>

<style scoped>
.vue-image-shadow {
    position: relative;
    z-index: 0;
}

.vue-image-shadow img {
    transition: all 0.4s ease;
    display: block;
}

.vue-image-shadow-card {
    position: absolute;
    background-repeat: no-repeat;
    background-size: 100%;
    bottom: -10%;
    left: 5%;
    width: 90%;
    height: 95%;
    z-index: -1;
    transition: all 0.4s;
}

.vue-image-shadow-hover:hover img {
    transform: translateY(-6px);
}

.vue-image-shadow-hover:hover .vue-image-shadow-card {
    left: 10%;
    bottom: -20%;
    width: 80%;
}
</style>
