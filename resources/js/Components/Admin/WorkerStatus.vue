<template>
    <component :is="status != 'running' ? Link : 'div'" :href="href" class="worker-controls ml-2"
        style="min-width: 150px">
        <span class="ml-2">
            <span>{{ status }}</span>
        </span>
        <span v-show="status === 'running'" class="ml-2">
            <i class="la la-check-circle text-green-500" :style="iconStyle"></i>
        </span>
        <span v-show="status == 'error' || status == 'stoppped'" class="ml-2">
            <i class="la la-exclamation-circle text-red-500" :style="iconStyle"></i>
        </span>
    </component>
</template>

<script setup>

import Link from '../Link.vue'

const props = defineProps({
    href: {
        type: String,
        default: '/'
    }
})


const status = ref('...');
let checkInterval;

const iconStyle = {
    font: 'bold',
    transform: 'scale(150%)'
};

const checkWorkerStatus = async () => {
    try {
        const response = await axios.get("/admin/worker/check");
        updateWorkerUI(response.data.status);
    } catch (error) {
        updateWorkerUI("error");
    }
};

const updateWorkerUI = (newStatus) => {
    status.value = newStatus;
};

onMounted(() => {
    checkWorkerStatus();
    checkInterval = setInterval(checkWorkerStatus, 25000);
});

onUnmounted(() => {
    clearInterval(checkInterval);
});
</script>
