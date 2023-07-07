<template>
    <Sections class="snap-mandatory snap-y overflow-y-scroll h-screen" ref="container" :style="{
        '--sectionHeight': sectionHeight
    }" scroll-region>
        <slot></slot>

        <!-- Footer como una sección más -->
        <Section>
            <AppFooter class="h-full flex items-center" />
        </Section>

        <TransitionFade>
            <div v-show="showScrollDown"
                class="transition duration-300 fixed bottom-3 left-0 w-full flex justify-center z-30 text-white mix-blend-exclusion">
                <Icon v-if="!isLastSection" icon="ph:caret-double-down-duotone" @click="scrollToNextMandatory" class="" />
            </div>
        </TransitionFade>

    </Sections>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
import { useNav } from '@/Stores/nav';

defineProps({
    sectionHeight: {
        type: String,
        required: false,
        default: '100vh'
    }
});

const container = ref(null);
const nav = useNav();
nav.scrollY = 0;

const isLastSection = ref(false)

const handleScroll = () => {
    nav.scrollY = container.value.$el.scrollTop;

    // is Last Section?
    const cont = container.value.$el;
    const mandatoryElems = cont.querySelectorAll('.sections > .section');
    const lastElem = mandatoryElems[mandatoryElems.length - 1];
    const rect = lastElem.getBoundingClientRect();
    isLastSection.value = rect.top <= screen.height * .7;
};

let scrollTimer = null;
const showScrollDown = ref(false);

onMounted(() => {
    handleScroll();
    container.value.$el.addEventListener('scroll', handleScroll, { passive: true });
    nav.fullPage = true;
    scrollTimer = setTimeout(() => {
        showScrollDown.value = true;
    }, 3000);
});


onBeforeUnmount(() => {
    container.value.$el.removeEventListener('scroll', handleScroll);
    nav.fullPage = false;
    clearTimeout(scrollTimer);
});



function scrollToNextMandatory() {
    const cont = container.value.$el;
    const containerHeight = cont.clientHeight;
    const halfContainerHeight = containerHeight / 2;
    const mandatoryElems = document.querySelectorAll('.sections > .section');
    let currentElem = null;

    for (let i = 0; i < mandatoryElems.length; i++) {
        const mandatoryElem = mandatoryElems[i];
        const rect = mandatoryElem.getBoundingClientRect();
        if (rect.top <= halfContainerHeight && rect.bottom >= halfContainerHeight) {
            currentElem = mandatoryElem;
            break;
        }
    }

    if (currentElem) {
        let nextMandatoryElem = null;
        for (let i = 0; i < mandatoryElems.length; i++) {
            const mandatoryElem = mandatoryElems[i];
            const rect = mandatoryElem.getBoundingClientRect();
            if (rect.top >= currentElem.getBoundingClientRect().bottom) {
                nextMandatoryElem = mandatoryElem;
                break;
            }
        }

        if (nextMandatoryElem) {
            nextMandatoryElem.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
}
</script>


<style scoped>
:slotted(.section) {
    height: var(--sectionHeight);
    @apply snap-center flex flex-col justify-center;
}

:deep(.section) {
    height: var(--sectionHeight);
    @apply snap-center flex flex-col justify-center;
}
</style>
