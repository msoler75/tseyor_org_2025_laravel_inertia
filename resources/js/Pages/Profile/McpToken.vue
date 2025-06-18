<template>
  <div class="max-w-xl mx-auto my-24 p-10 rounded bg-base-100">
    <h1 class="text-2xl font-bold mb-4">Generar Token MCP</h1>
    <p class="mb-6 text-gray-700">
      Esta sección te permite generar un <b>token JWT MCP</b> personal, necesario para autenticarte y utilizar las herramientas avanzadas de Tseyor.org desde aplicaciones externas como Claude Desktop. El token confirma tu identidad como usuario registrado y te da acceso seguro a las funciones MCP sin compartir tu contraseña.
    </p>
    <div v-if="token">
      <div class="mb-4">
        <label class="font-semibold">Tu token JWT MCP:</label>
        <div class="flex items-center gap-2">
          <div class="bg-gray-100 p-2 rounded break-all select-all text-xs mt-2 flex-1">{{ token }}</div>
          <button @click="copiarToken" class="btn btn-sm btn-outline btn-success mt-2 w-20">
            <span v-if="copiado">¡Copiado!</span>
            <span v-else>Copiar</span>
          </button>
        </div>
        <div class="mt-2 text-xs text-red-600 font-semibold">
          ⚠️ No compartas con nadie esta información. El token da acceso a tus herramientas MCP personales.
        </div>
      </div>
      <div class="mb-6">
        <ul class="list-disc pl-5 text-gray-700">
          <li>Si lo pierdes, puedes generar uno nuevo en cualquier momento.</li>
          <li>El token es válido por 30 días.</li>
        </ul>
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded text-blue-900 text-sm">
          <b>¿Cómo usar este token en Claude Desktop?</b>
          <ol class="list-decimal ml-5 mt-2 space-y-1">
            <li>Abre la aplicación Claude Desktop en tu ordenador.</li>
            <li>En el menú superior, haz clic en <b>Archivo</b> &rarr; <b>Configuración</b>.</li>
            <li>En la ventana de configuración, selecciona la pestaña <b>Desarrollador</b>.</li>
            <li>Haz clic en <b>Editar configuración</b> para abrir el archivo <code>claude_config.json</code>.</li>            <li>Añade o actualiza la configuración para este servidor mcp, siguiendo el ejemplo:</li>

          <pre class="bg-gray-100 rounded p-3 mt-3 text-xs overflow-x-auto select-all">{
  "mcpserver": {
    "url": "https://tseyor.org/mcp",
    "token": "{{ token }}"
  },
  // ...otros servidores mcp
}
</pre>
          <li class="mt-2">
            Guarda los cambios y reinicia la aplicación si es necesario.<br>
          </li>
          </ol>
          <div>
            <p class="text-sm">¡Listo! Ahora podrás acceder a las herramientas MCP de Tseyor.org desde Claude Desktop.
                </p>
          </div>
        </div>
      </div>
    </div>
    <form v-else @submit.prevent="solicitarToken" class="space-y-4">
      <p>Haz clic en el botón para generar tu token JWT MCP personal.</p>
      <button type="submit" class="btn btn-primary" :disabled="loading">
        <span v-if="loading">Generando...</span>
        <span v-else>Generar token MCP</span>
      </button>
      <div v-if="error" class="text-red-600 text-sm mt-2">{{ error }}</div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
const props = defineProps({
  token: String
})

const token = ref(props.token || '')
const loading = ref(false)
const error = ref('')
const copiado = ref(false)

function solicitarToken() {
  loading.value = true
  error.value = ''
  axios.post(route('profile.mcp-token.generate'))
    .then(res => {
      token.value = res.data.token
    })
    .catch(() => {
      error.value = 'No se pudo generar el token. Intenta de nuevo.'
    })
    .finally(() => {
      loading.value = false
    })
}

function copiarToken() {
  if (!token.value) return
  navigator.clipboard.writeText(token.value)
    .then(() => {
      copiado.value = true
      setTimeout(() => copiado.value = false, 2000)
    })
}
</script>
