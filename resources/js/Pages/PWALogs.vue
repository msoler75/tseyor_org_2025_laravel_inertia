<template>
    <AppLayout title="PWA Debug Logs">
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">PWA Debug Logs</h1>
                    <div class="space-x-2">
                        <button
                            @click="refreshLogs"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                            :disabled="loading"
                        >
                            {{ loading ? 'Cargando...' : 'Actualizar' }}
                        </button>
                        <button
                            @click="clearLogs"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded"
                            :disabled="loading"
                        >
                            Limpiar Logs
                        </button>
                    </div>
                </div>

                <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ error }}
                </div>

                <div v-if="logs.length === 0" class="text-center py-8 text-gray-500">
                    No hay logs disponibles
                </div>

                <div v-else class="space-y-4">
                    <div class="text-sm text-gray-600 mb-4">
                        Total de logs: {{ logs.length }}
                        <span v-if="fileSize">| Tamaño del archivo: {{ formatFileSize(fileSize) }}</span>
                    </div>

                    <div v-for="(log, index) in logs" :key="index" class="border rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center space-x-2">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded"
                                    :class="getLevelClass(log.level)"
                                >
                                    {{ log.level.toUpperCase() }}
                                </span>
                                <span class="text-sm text-gray-500">{{ log.timestamp }}</span>
                                <span v-if="log.is_pwa" class="bg-purple-100 text-purple-800 px-2 py-1 text-xs rounded">
                                    PWA
                                </span>
                            </div>
                        </div>

                        <div class="mb-2">
                            <strong class="text-gray-800">{{ log.message }}</strong>
                        </div>

                        <div v-if="log.url" class="text-sm text-blue-600 mb-2">
                            URL: {{ log.url }}
                        </div>

                        <div v-if="log.data && Object.keys(log.data).length > 0" class="text-sm">
                            <details class="cursor-pointer">
                                <summary class="text-gray-600 hover:text-gray-800">Datos adicionales</summary>
                                <pre class="mt-2 p-2 bg-gray-100 rounded text-xs overflow-x-auto">{{ JSON.stringify(log.data, null, 2) }}</pre>
                            </details>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const logs = ref([])
const loading = ref(false)
const error = ref('')
const fileSize = ref(0)

const fetchLogs = async () => {
    loading.value = true
    error.value = ''

    try {
        const response = await fetch('/pwa-logs')
        const data = await response.json()

        logs.value = data.logs || []
        fileSize.value = data.file_size || 0
    } catch (err) {
        error.value = 'Error al cargar los logs: ' + err.message
        console.error('Error fetching PWA logs:', err)
    } finally {
        loading.value = false
    }
}

const clearLogs = async () => {
    if (!confirm('¿Estás seguro de que quieres limpiar todos los logs?')) {
        return
    }

    loading.value = true
    error.value = ''

    try {
        const response = await fetch('/pwa-logs', {
            method: 'DELETE'
        })

        if (response.ok) {
            logs.value = []
            fileSize.value = 0
        } else {
            throw new Error('Error al limpiar los logs')
        }
    } catch (err) {
        error.value = 'Error al limpiar los logs: ' + err.message
        console.error('Error clearing PWA logs:', err)
    } finally {
        loading.value = false
    }
}

const refreshLogs = () => {
    fetchLogs()
}

const getLevelClass = (level) => {
    const classes = {
        'info': 'bg-blue-100 text-blue-800',
        'warn': 'bg-yellow-100 text-yellow-800',
        'error': 'bg-red-100 text-red-800',
        'success': 'bg-green-100 text-green-800'
    }
    return classes[level] || 'bg-gray-100 text-gray-800'
}

const formatFileSize = (bytes) => {
    if (!bytes) return '0 B'
    const sizes = ['B', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(1024))
    return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
}

onMounted(() => {
    fetchLogs()
})
</script>
