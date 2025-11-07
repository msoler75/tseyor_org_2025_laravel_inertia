<template>
    <a v-if="status != 'running'" :href="href" class="worker-controls ml-2" style="min-width: 150px">
        <span class="ml-2">
            <span>{{ status }}</span>
        </span>
        <span v-show="status == 'stopped'" class="ml-2">
            <i class="la la-exclamation-circle text-orange-500" :style="iconStyle"></i>
        </span>
        <span v-show="status == 'error'" class="ml-2">
            <i class="la la-exclamation-circle text-red-500" :style="iconStyle"></i>
        </span>
    </a>
    <div v-else class="worker-controls ml-2" style="min-width: 150px">
        <span class="ml-2">
            <span>{{ status }}</span>
        </span>
        <span class="ml-2">
            <i class="la la-check-circle text-green-500" :style="iconStyle"></i>
        </span>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

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
        console.warn("Error checking worker status:", error);
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
