<template>
    <div v-if="installable && !installed" class="fixed bottom-20 right-4 z-40">
        <button
            @click="install"
            class="btn btn-primary shadow-lg gap-2"
            :class="expanded ? 'btn-lg' : 'btn-square btn-lg'"
            @mouseenter="expanded = true"
            @mouseleave="expanded = false"
            :title="'Instalar Radio Tseyor'"
        >
            <Icon icon="ph:download-duotone" class="text-xl" />
            <span v-show="expanded" class="whitespace-nowrap">Instalar App</span>
        </button>
    </div>

    <div v-if="installed" class="flex items-center gap-1 text-xs opacity-60">
        <Icon icon="ph:check-circle-duotone" class="text-green-500" />
        App instalada
    </div>
</template>

<script setup>
const installable = ref(false)
const installed = ref(false)
const expanded = ref(false)

let deferredPrompt = null

function checkIfInstalled() {
    if (typeof window === 'undefined') return

    if (window.matchMedia?.('(display-mode: standalone)').matches) {
        installed.value = true
    }
    if (window.navigator?.standalone) {
        installed.value = true
    }
    if (document.referrer.includes('android-app://')) {
        installed.value = true
    }
}

async function install() {
    if (!deferredPrompt) return

    deferredPrompt.prompt()
    const { outcome } = await deferredPrompt.userChoice

    if (outcome === 'accepted') {
        installed.value = true
        installable.value = false
    }

    deferredPrompt = null
}

if (typeof window !== 'undefined') {
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault()
        deferredPrompt = e
        installable.value = true
    })

    window.addEventListener('appinstalled', () => {
        installed.value = true
        installable.value = false
    })
}

onMounted(checkIfInstalled)
</script>
