<template>
    <div class="sections" style="--sectionHeight:calc(80vh - 4rem)">
        <slot></slot>
    </div>
</template>

<style>
.sections>* {
    @apply w-full mx-auto;
}

.sections>*>* {
    @apply mx-auto;
}

.sections:not([white-first])>.section:nth-child(2n) {
    @apply bg-base-100 border-b border-t border-gray-200;
}

.sections[white-first]>.section:nth-child(2n+1) {
    @apply bg-base-100 border-b border-t border-gray-200;
}
</style>
