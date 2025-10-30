<template>
    <div class="loading loading-circle loading-md text-primary"></div>
</template>


<style>
.loading-circle {
    mask-image: url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cg transform-origin='center'%3E%3Ccircle cx='12' cy='12' r='9' fill='none' stroke='black' stroke-width='3' opacity='0.25'%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 12 12' to='360 12 12' dur='1s' repeatCount='indefinite'/%3E%3C/circle%3E%3Ccircle cx='12' cy='12' r='9' fill='none' stroke='black' stroke-width='3' stroke-dasharray='10 30 0'%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 12 12' to='360 12 12' dur='.75s' repeatCount='indefinite'/%3E%3C/circle%3E%3C/g%3E%3C/svg%3E");
}
</style>
