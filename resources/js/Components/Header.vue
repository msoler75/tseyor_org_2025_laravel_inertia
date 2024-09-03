<template>
    <div class="bg-base-100 dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <transition name="slide-fade">
                <slot />
            </transition>
        </div>
    </div>
</template>



<style scoped>
/* durations and timing functions.*/
.slide-fade-enter-active {
    transition: all .9s ease;
}

.slide-fade-leave-active {
    transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}

.slide-fade-enter,
.slide-fade-leave-to

/* .slide-fade-leave-active below version 2.1.8 */
    {
    transform: translateX(50px);
    opacity: 0;
}
</style>
