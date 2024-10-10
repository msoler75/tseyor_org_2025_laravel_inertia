<template>
    <component :is="status!='running'?Link:'div'" :href="href" class="worker-controls ml-2" style="min-width: 150px">
      <span class="ml-2">
        <span>{{ workerState }}</span>
      </span>
      <span v-show="isRunning" class="ml-2">
        <i  class="la la-check-circle text-green-500" :style="iconStyle"></i>
      </span>
      <span v-show="isError" class="ml-2">
        <i  class="la la-exclamation-circle text-red-500" :style="iconStyle"></i>
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

  const workerState = ref('...');
  const isRunning = ref(false);
  const isError = ref(false);
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

  const updateWorkerUI = (status) => {
    workerState.value = status;
    isRunning.value = status === "running";
    isError.value = status === "error";
  };

  onMounted(() => {
    checkWorkerStatus();
    checkInterval = setInterval(checkWorkerStatus, 25000);
  });

  onUnmounted(() => {
    clearInterval(checkInterval);
  });
  </script>
