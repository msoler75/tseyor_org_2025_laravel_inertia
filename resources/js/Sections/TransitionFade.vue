<template>
    <transition name="fade">
        <slot/>
    </transition>
    </template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 300ms ease-out;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
