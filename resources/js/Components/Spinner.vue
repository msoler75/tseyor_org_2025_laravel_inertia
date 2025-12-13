<template>
    <span class="t-spinner" role="status" aria-live="polite" aria-busy="true"></span>
</template>

<style scoped>
.t-spinner {
    display: inline-block;
    width: 1em;
    height: 1em;
    border: 0.15em solid currentColor;
    border-top-color: transparent;
    border-radius: 9999px;
    box-sizing: border-box;
    animation: t-rotate 0.75s linear infinite;
}

@keyframes t-rotate {
    to {
        transform: rotate(360deg);
    }
}
</style>
