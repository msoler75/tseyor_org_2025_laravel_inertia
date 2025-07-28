<template>
  <div class="max-w-6xl mx-auto p-6 pb-32">
    <div class="mb-6">
      <h1>
        Gestión de inscripciones
      </h1>
      <p>
        Inscripciones de personas interesadas en tomar el Curso Holístico de Tseyor que tienes asignadas.
      </p>
    </div>

    <div v-if="!filtrado && inscripcionesFiltradas.length === 0" class="text-center py-12">
      <div class="text-gray-400 dark:text-gray-500 text-lg">
        <span class="badge text-xl p-6">No tienes inscripciones asignadas actualmente</span>
      </div>
    </div>

    <div v-else >
      <!-- Encabezados informativos -->
      <div class="mb-6 flex flex-wrap justify-between gap-6">
        <!-- Encabezado de inscripciones activas -->
        <div
          v-if="inscripcionesActivas.length > 0"
          class="flex items-center gap-2"
        >
          <div class="bg-base-100 shadow px-2 py-1 rounded-full text-xl font-semibold flex items-center gap-2">
            <Icon icon="ic:round-priority-high" class="text-red-500" />
            Inscripciones Pendientes
            <span class="bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 text-sm font-medium px-2.5 py-0.5 rounded-full">
              {{ inscripcionesActivas.length }}
            </span>
        </div>
        </div>

        <!-- Encabezado de inscripciones cerradas -->
        <div
          v-if="inscripcionesFinalizadas.length > 0"
          class="flex items-center gap-2"
        >
          <div class="bg-base-100 shadow px-2 py-1 rounded-full text-xl font-semibold flex items-center gap-2">
            <Icon icon="ic:round-check-circle" class="text-green-500" />
            Inscripciones Cerradas
            <span class="bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 text-sm font-medium px-2.5 py-0.5 rounded-full">
              {{ inscripcionesFinalizadas.length }}
            </span>
        </div>
        </div>

    <SearchInput
    v-if="filtrado || inscripcionesProcesadas.length > 0"
        v-model="query"
        placeholder="Buscar inscripción..."
        class="ml-auto"
      />
      </div>

     <SearchResultsHeader
          :results="listado"
        />

      <!-- Lista unificada con TransitionGroup -->
      <TransitionGroup name="inscripciones" tag="div" class="space-y-4">
        <!-- Tarjetas unificadas -->
        <Card
          v-for="inscripcion in inscripcionesFiltradas"
          :key="`inscripcion-${inscripcion.id}`"
          :data-inscripcion-id="inscripcion.id"
          class="inscripcion-card border-l-4"
          :class="[
            inscripcion.esCerrada ? 'inscripcion-cerrada border-gray-300' : 'inscripcion-activa',
            inscripcion.esCerrada ? '' : inscripcion.borderClass
          ]"
        >
          <!-- Contenido principal de la fila -->
          <div :class="inscripcion.esCerrada ? 'p-3' : 'p-4'">
            <!-- Layout responsive: una columna en móvil/tablet, fila en desktop -->
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3 lg:gap-4">

              <!-- Primera fila/sección: Información básica -->
              <div class="flex items-start gap-4 flex-grow min-w-0">
                <!-- Icono de estado -->
                <div class="flex-shrink-0">
                  <!-- Ícono para inscripciones pendientes -->
                  <Icon
                    v-if="!inscripcion.esCerrada"
                    icon="ic:round-priority-high"
                    class="w-6 h-6 text-red-500"
                    title="Inscripción pendiente"
                  />
                  <!-- Ícono para inscripciones cerradas -->
                  <Icon
                    v-if="inscripcion.esCerrada"
                    icon="ic:round-check-circle"
                    class="w-5 h-5 text-green-500"
                    title="Inscripción cerrada"
                  />
                </div>

                <!-- Datos principales -->
                <div class="flex-grow min-w-0">
                  <div class="flex items-start gap-3 mb-2">
                    <h3
                      @click="toggleDetalles(inscripcion.id)"
                      :class="[
                        inscripcion.esCerrada
                          ? 'text-base font-medium text-gray-700 dark:text-gray-300 truncate'
                          : 'text-lg font-medium text-gray-900 dark:text-white truncate',
                        'flex items-center gap-2 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400'
                      ]"
                      :title="tarjetasExpandidas.has(inscripcion.id) ? 'Ocultar detalles' : 'Ver detalles'"
                    >
                    <Icon icon="ic:round-person" class="transform scale-120 -translate-y-[1px]"/>
                      {{ inscripcion.nombre }}
                    </h3>

                    <!-- Indicador de inscripción recién reasignada (solo activas) -->
                    <div v-if="!inscripcion.esCerrada && inscripcion.esRecienReasignada"
                         class="flex items-center gap-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded-full text-xs font-bold">
                      <Icon icon="ic:round-refresh" class="w-3 h-3" />
                      <span>REABIERTA</span>
                    </div>

                    <!-- Indicador de cambio de estado -->
                    <div v-if="tarjetasConCambios.has(inscripcion.id) && (!inscripcion.esCerrada ? !inscripcion.esRecienReasignada : true)"
                         class="flex items-center gap-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full text-xs font-bold">
                      <Icon icon="ic:round-update" class="w-3 h-3" />
                      <span class="hidden sm:inline">ACTUALIZADA</span>
                    </div>
                  </div>

                  <!-- Información de contacto y ubicación -->
                  <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 text-sm" :class="inscripcion.esCerrada ? 'text-gray-500 dark:text-gray-400' : 'text-gray-600 dark:text-gray-400'">
                    <div class="flex items-center gap-1 min-w-0">
                      <Icon icon="ic:round-email" class="w-4 h-4 flex-shrink-0" />
                      <span
                        class="truncate"
                        :title="inscripcion.email"
                      >
                        {{ inscripcion.email }}
                      </span>
                    </div>

                    <div v-if="inscripcion.telefono" class="flex items-center gap-1 min-w-0">
                      <Icon icon="ic:round-phone" class="w-4 h-4 text-gray-400" />
                      <span
                        class="truncate"
                        :title="inscripcion.telefono"
                      >
                        {{ inscripcion.telefono }}
                      </span>
                    </div>

                    <div class="flex items-center gap-4 text-xs sm:text-sm">
                      <div v-if="inscripcion.region || inscripcion.ciudad" class="flex items-center gap-1">
                        <Icon icon="ic:round-location-on" class="w-4 h-4 flex-shrink-0" />
                        <span>{{ [inscripcion.ciudad, inscripcion.region].filter(Boolean).join(', ') }}</span>
                      </div>

                      <div v-if="inscripcion.pais" class="flex items-center gap-1">
                        <Icon icon="ic:round-public" class="w-4 h-4 flex-shrink-0" />
                        <span>{{ inscripcion.pais }}</span>
                      </div>

                      <div v-if="inscripcion.esCerrada" class="text-xs">
                        Cerrada: {{ formatearFecha(inscripcion.updated_at) }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Segunda fila/sección: Estado y acciones -->
              <div class="flex flex-wrap items-start justify-between lg:justify-end gap-3 flex-shrink-0">
                <!-- Estado para activas -->
                <div v-if="!inscripcion.esCerrada" class="flex-grow lg:flex-grow-0 lg:min-w-[120px]">
                  <div v-if="inscripcion.estado !== 'rebotada'">
                    <select
                      :value="inscripcion.estado"
                      @change="cambiarEstado(inscripcion, $event.target.value)"
                      class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md px-2 py-1 bg-base-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                      <option
                        v-for="(nombre, codigo) in estadosSeleccionables"
                        :key="codigo"
                        :value="codigo"
                      >
                        {{ nombre }}
                      </option>
                    </select>
                  </div>
                  <div v-else>
                    <span :class="getEstadoClass('rebotada')" class="px-3 py-1 text-sm font-medium rounded-full whitespace-nowrap">
                      Rebotada
                    </span>
                  </div>
                </div>

                <!-- Estado para cerradas -->
                <span v-if="inscripcion.esCerrada" :class="getEstadoClass(inscripcion.estado)" class="px-3 py-1 text-sm font-medium rounded-full whitespace-nowrap">
                  {{ (estadosDisponibles && inscripcion.estado && estadosDisponibles[inscripcion.estado]?.etiqueta) || inscripcion.estado || 'Sin estado' }}
                </span>

                <!-- Botones de acción -->
                <div class="flex items-center gap-2">
                  <button
                    @click="toggleDetalles(inscripcion.id)"
                    class="px-3 py-1 text-sm font-medium text-blue-600 border border-blue-300 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 flex items-center gap-2"
                    :title="tarjetasExpandidas.has(inscripcion.id) ? 'Ocultar detalles' : 'Ver detalles'"
                  >
                    <Icon
                      :icon="tarjetasExpandidas.has(inscripcion.id) ? 'ic:round-expand-less' : 'ic:round-expand-more'"
                      class="w-4 h-4"
                    />
                    <span>Detalles</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Detalles expandibles -->
            <div v-if="tarjetasExpandidas.has(inscripcion.id)" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
              <!-- Primera fila: Contacto y Fechas -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Información de contacto -->
                <div class="space-y-2">
                  <div v-if="inscripcion.fecha_nacimiento" class="flex items-center gap-2 text-sm">
                    <Icon icon="ic:round-cake" class="w-4 h-4 text-purple-500" />
                    <span>{{ calcularEdad(inscripcion.fecha_nacimiento) }} años</span>
                  </div>
                    <div class="flex gap-2 items-center"><Icon icon="ic:round-person-add" class="w-4 h-4 text-primary" />Inscripción: {{ formatearFecha(inscripcion.created_at) }}</div>
                    <div class="flex gap-2 items-center"><Icon icon="ic:round-assignment-ind" class="w-4 h-4 text-warning" />Asignada: {{ formatearFecha(inscripcion.fecha_asignacion) }}</div>
                    <div v-if="inscripcion.ultima_notificacion" class="flex gap-2 items-center" ><Icon icon="ic:round-notifications" class="w-4 h-4 text-blue-500"/>
                      Última notificación: {{ formatearFecha(inscripcion.ultima_notificacion) }}
                    </div>
                    <div v-if="inscripcion.esCerrada" class="flex gap-2 items-center"><Icon icon="ic:round-check-circle" class="w-4 h-4 text-green-600" />Cerrada: {{ formatearFecha(inscripcion.updated_at) }}</div>
                </div>
                <div v-if="inscripcion.comentario" class="text-sm text-gray-600 dark:text-gray-400">
                    <div class="font-medium mb-1">Comentario:</div>
                  <div class="italic">"{{ inscripcion.comentario }}"</div>
                </div>

              </div>

              <!-- Segunda fila: Observaciones (ancho completo) -->
              <div v-if="inscripcion.notas" class="space-y-2">

                <!-- Notas si existen -->
                <div v-if="inscripcion.notas" class="text-sm text-gray-600 dark:text-gray-400">
                  <div class="font-medium mb-1">Notas de seguimiento:</div>
                  <div class="bg-base-100 dark:bg-gray-700 p-2 rounded border max-h-32 overflow-y-auto" v-html="inscripcion.notas.replace(/\n/g, '<br>')"></div>
                </div>
              </div>

              <!-- Acciones adicionales en la vista expandida -->
              <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                <div class="flex justify-center gap-3">
                  <button
                    v-if="inscripcion.estado !== 'rebotada'"
                    @click="abrirModalNotas(inscripcion)"
                    class="px-4 py-2 text-sm font-medium text-green-600 border border-green-600 rounded-md hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors flex items-center gap-2"
                  >
                    <Icon icon="ic:round-edit-note" class="w-4 h-4" />
                    Editar notas
                  </button>

                  <!-- Botón rebotar solo para activas no rebotadas -->
                  <button
                    v-if="!inscripcion.esCerrada && inscripcion.estado !== 'rebotada'"
                    @click="abrirModalRebote(inscripcion)"
                    class="px-4 py-2 text-sm font-medium text-orange-600 border border-orange-600 rounded-md hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors flex items-center gap-2"
                  >
                    <Icon icon="tabler:bounce-left" class="w-4 h-4" />
                    Rebotar
                  </button>

                  <!-- Botón añadir comentario solo para activas no rebotadas -->
                  <button
                    v-if="!inscripcion.esCerrada && inscripcion.estado !== 'rebotada'"
                    @click="abrirModalComentario(inscripcion)"
                    class="px-4 py-2 text-sm font-medium text-blue-600 border border-blue-600 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center gap-2"
                  >
                    <Icon icon="ic:round-add-comment" class="w-4 h-4" />
                    Añadir comentario
                  </button>

                  <!-- Botón reabrir solo para cerradas -->
                  <button
                    v-if="inscripcion.esCerrada"
                    @click="reasignarInscripcion(inscripcion)"
                    class="px-4 py-2 text-sm font-medium text-orange-600 border border-orange-600 rounded-md hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors flex items-center gap-2"
                  >
                    <Icon icon="ic:round-person-add" class="w-4 h-4" />
                    Reabrir inscripción
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Indicador de urgencia solo para activas -->
          <div
            v-if="!inscripcion.esCerrada && inscripcion.esUrgente"
            class="h-1 bg-gradient-to-r from-red-500 to-orange-500"
          ></div>
        </Card>
      </TransitionGroup>
    </div>

    <pagination class="mt-6" :links="listado.links" />

    <!-- Modal de rebote -->
    <Modal :show="mostrarModalRebote" @close="cerrarModalRebote" max-width="md">
      <div class="p-6">
        <h3 class="text-lg font-medium mb-4">
          Rebotar Inscripción
        </h3>

        <div class="mb-4">
          <p class="text-sm">
            Vas a rebotar la inscripción de <strong>{{ inscripcionSeleccionada?.nombre }}</strong> para que se reasigne a otro/a tutor/a.
          </p>
        </div>

        <form @submit.prevent="rebotarInscripcion">
          <div class="mb-4">
            <label class="block text-sm font-medium mb-2">
              Motivo del rebote *
            </label>
            <textarea
              v-model="formRebote.motivo"
              class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-base-100 focus:ring-2 focus:ring-red-500 focus:border-red-500"
              rows="4"
              placeholder="Explica por qué no puedes atender esta inscripción..."
              required
              minlength="10"
            ></textarea>
            <div class="mt-1 text-xs text-gray-500">
              Mínimo 10 caracteres. Caracteres actuales: {{ formRebote.motivo.length }}
            </div>
          </div>

          <div class="flex justify-end space-x-3">
            <button
              type="button"
              @click="cerrarModalRebote"
              class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 rounded-md hover:bg-gray-200 dark:hover:bg-gray-500"
            >
              Cancelar
            </button>
            <button
              type="submit"
              class="px-4 py-2 text-sm font-medium text-white rounded-md transition-colors"
              :class="(formRebote.motivo.trim().length >= 10)
                ? 'bg-red-600 hover:bg-red-700 cursor-pointer'
                : 'bg-gray-400 cursor-not-allowed'"
              :disabled="formRebote.motivo.trim().length < 10"
            >
              Confirmar Rebote
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Modal de notas -->
    <Modal :show="mostrarModalNotas" @close="cerrarModalNotas" max-width="4xl">
      <div class="p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Notas de {{ inscripcionSeleccionada?.nombre }}
          </h3>
          <button
            @click="cerrarModalNotas"
            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div class="mb-4">
          <div class="text-sm mb-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <strong>Email:</strong> {{ inscripcionSeleccionada?.email }}
              </div>
              <div>
                <strong>Estado:</strong> {{ (estadosDisponibles && inscripcionSeleccionada?.estado && estadosDisponibles[inscripcionSeleccionada.estado]?.etiqueta) || inscripcionSeleccionada?.estado || 'Sin estado' }}
              </div>
              <div>
                <strong>Fecha asignación:</strong> {{ formatearFechaCompleta(inscripcionSeleccionada?.fecha_asignacion) }}
              </div>
              <div v-if="inscripcionSeleccionada?.ultima_notificacion">
                <strong>Última notificación:</strong> {{ formatearFechaCompleta(inscripcionSeleccionada?.ultima_notificacion) }}
              </div>
            </div>
          </div>
        </div>

        <form @submit.prevent="guardarNotas">
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Notas de seguimiento
            </label>
            <TipTapEditor
              v-model="formNotas.notas"
              :full="false"
              class="min-h-[200px]"
            />
          </div>

          <div class="flex justify-end space-x-3">
            <button
              type="button"
              @click="cerrarModalNotas"
              class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 rounded-md hover:bg-gray-200 dark:hover:bg-gray-500"
            >
              Cancelar
            </button>
            <button
              type="submit"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
              :disabled="guardandoNotas"
            >
              {{ guardandoNotas ? 'Guardando...' : 'Guardar Notas' }}
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Modal añadir comentario simple -->
    <Modal :show="mostrarModalComentario" @close="cerrarModalComentario" max-width="md">
      <div class="p-6">
        <h3 class="text-lg font-medium mb-4">Añadir comentario a notas</h3>
        <form @submit.prevent="guardarComentario">
          <textarea
            v-model="formComentario.comentario"
            ref="textareaComentario"
            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-base-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            rows="4"
            placeholder="Escribe tu comentario..."
            required
          ></textarea>
          <div class="flex justify-end space-x-3 mt-4">
            <button type="button" @click="cerrarModalComentario" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 rounded-md hover:bg-gray-200 dark:hover:bg-gray-500">Cancelar</button>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700" :disabled="guardandoComentario || !formComentario.comentario.trim()">{{ guardandoComentario ? 'Guardando...' : 'Añadir comentario' }}</button>
          </div>
        </form>
      </div>
    </Modal>
  </div>
</template>

<script setup>
// Estado para la búsqueda, inicializado desde props
const props = defineProps({
  listado: Object,
  estadosDisponibles: Object,
  estadosNoElegibles: Object,
  umbralesdias: {
    type: Object,
    default: () => ({
      asignado_urgente: 3,
      contactado_urgente: 7,
      encurso_seguimiento: 30
    })
  },
  filtrado: {
    type: String,
    default: ''
  }
})


const textareaComentario = ref(null)
const query = ref(props.filtrado || "")


// Copia reactiva local de las inscripciones (evita mutar props directamente)
const inscripcionesLocal = ref(props.listado.data.map(i => ({ ...i })))

// Sincronizar la copia local si cambian las props (paginación, filtro, etc)
watch(() => props.listado.data, (nuevas) => {
  inscripcionesLocal.value = nuevas.map(i => ({ ...i }))
})

// Computed para procesar la copia local
const inscripcionesFiltradas = computed(() => inscripcionesOrdenadas.value)

const mostrarModalRebote = ref(false)
const mostrarModalNotas = ref(false)
const mostrarModalComentario = ref(false)
const inscripcionSeleccionada = ref(null)
const guardandoNotas = ref(false)
const guardandoComentario = ref(false)
const tarjetasExpandidas = ref(new Set())
const inscripcionesRecienReasignadas = ref(new Set())
const tarjetasConCambios = ref(new Set())

// Verificar inscripciones recién reasignadas al cargar la página
onMounted(() => {
  let hayInscripcionesReasignadas = false
  let primeraInscripcionReasignada = null

  props.listado.data.forEach(inscripcion => {
    if (inscripcion.estado === 'asignada') {
      const ahora = new Date()
      const fechaActualizacion = new Date(inscripcion.updated_at)
      const diferenciaMilisegundos = ahora - fechaActualizacion
      const diferenciaSegundos = diferenciaMilisegundos / 1000

      // Si fue actualizada en los últimos 30 segundos, marcarla como recién reasignada
      if (diferenciaSegundos <= 30) {
        inscripcionesRecienReasignadas.value.add(inscripcion.id)

        if (!hayInscripcionesReasignadas) {
          hayInscripcionesReasignadas = true
          primeraInscripcionReasignada = inscripcion.id
        }

        // Programar la eliminación del efecto después de 10 segundos
        setTimeout(() => {
          inscripcionesRecienReasignadas.value.delete(inscripcion.id)
        }, 10000)
      }
    }
  })

  // Si hay inscripciones reasignadas, hacer scroll a la primera
  if (hayInscripcionesReasignadas && primeraInscripcionReasignada) {
    nextTick(() => {
      setTimeout(() => {
        const tarjetaElement = document.querySelector(`[data-inscripcion-id="${primeraInscripcionReasignada}"]`)
        if (tarjetaElement) {
          tarjetaElement.scrollIntoView({
            behavior: 'smooth',
            block: 'center',
            inline: 'nearest'
          })
        }
      }, 500) // Delay un poco más para asegurar que todo esté renderizado
    })
  }
})

// Computed principal que añade campos calculados a cada inscripción (sobre la copia local)
const inscripcionesProcesadas = computed(() => {
  const estadosFinalizados = ['finalizado', 'duplicada', 'nointeresado', 'abandonado', 'nocontesta']
  return inscripcionesLocal.value.map(inscripcion => {
    const esCerrada = estadosFinalizados.includes(inscripcion.estado)
    const esRecienReasignada = inscripcionesRecienReasignadas.value.has(inscripcion.id)
    const diasDesdeActualizacion = Math.floor((new Date() - new Date(inscripcion.updated_at)) / (1000 * 60 * 60 * 24))
    let borderClass = 'border-blue-400'
    if (esRecienReasignada) {
      borderClass = 'border-green-500'
    } else if (!esCerrada) {
      if (inscripcion.estado === 'asignada' && diasDesdeActualizacion >= props.umbralesdias.asignado_urgente) {
        borderClass = 'border-red-500'
      } else if (inscripcion.estado === 'contactado' && diasDesdeActualizacion >= props.umbralesdias.contactado_urgente) {
        borderClass = 'border-orange-500'
      } else if (inscripcion.estado === 'encurso' && diasDesdeActualizacion >= props.umbralesdias.encurso_seguimiento) {
        borderClass = 'border-yellow-500'
      } else if (inscripcion.estado === 'rebotada') {
        borderClass = 'border-gray-400'
      }
    }
    const diasAsignada = Math.floor((new Date() - new Date(inscripcion.fecha_asignacion)) / (1000 * 60 * 60 * 24))
    const diasUltimaNotificacion = inscripcion.ultima_notificacion
      ? Math.floor((new Date() - new Date(inscripcion.ultima_notificacion)) / (1000 * 60 * 60 * 24))
      : diasAsignada
    const esUrgente = diasAsignada > 10 || diasUltimaNotificacion > 5
    return {
      ...inscripcion,
      esCerrada,
      esRecienReasignada,
      diasDesdeActualizacion,
      borderClass,
      esUrgente
    }
  })
})

// Separar inscripciones activas de finalizadas usando los datos procesados
const inscripcionesActivas = computed(() => {
  return inscripcionesProcesadas.value.filter(inscripcion => !inscripcion.esCerrada)
})

const inscripcionesFinalizadas = computed(() => {
  return inscripcionesProcesadas.value.filter(inscripcion => inscripcion.esCerrada)
})

// Lista unificada: primero activas ordenadas por prioridad, luego cerradas ordenadas por fecha
const inscripcionesOrdenadas = computed(() => {
  // Ordenar activas por prioridad (recién reasignadas primero, luego por urgencia/estado)
  const activasOrdenadas = inscripcionesActivas.value.sort((a, b) => {
    // Primero: recién reasignadas
    if (a.esRecienReasignada && !b.esRecienReasignada) return -1
    if (!a.esRecienReasignada && b.esRecienReasignada) return 1

    // Segundo: por urgencia/estado
    const ordenEstados = {
      'rebotada': 0,
      'asignada': 1,
      'contactado': 2,
      'encurso': 3
    }
    const ordenA = ordenEstados[a.estado] ?? 4
    const ordenB = ordenEstados[b.estado] ?? 4
    if (ordenA !== ordenB) return ordenA - ordenB

    // Tercero: por fecha de asignación (más antiguas primero)
    return new Date(a.fecha_asignacion) - new Date(b.fecha_asignacion)
  })

  // Ordenar cerradas por fecha de actualización (más recientes primero)
  const cerradasOrdenadas = inscripcionesFinalizadas.value.sort((a, b) => {
    return new Date(b.updated_at) - new Date(a.updated_at)
  })

  return [
    ...activasOrdenadas,
    ...cerradasOrdenadas
  ]
})

const estadosSeleccionables = computed(() => {
    // primero obtenemos los estados disponibles
    let estados = {}
    Object.entries(props.estadosDisponibles || {}).forEach(([key, estado]) => {
      // Verificar que no esté en los estados no elegibles
      if (!props.estadosNoElegibles.includes(key)) {
        estados[key] = estado.descripcion || estado.etiqueta || estado
      }
    })
  return {
    'asignada': '-- Elegir --',
    ...estados
  }
})

const formRebote = reactive({
  motivo: ''
})

const formNotas = reactive({
  notas: ''
})

const formComentario = reactive({
  comentario: ''
})


function abrirModalRebote(inscripcion) {
  inscripcionSeleccionada.value = inscripcion
  formRebote.motivo = ''
  mostrarModalRebote.value = true
}

function cerrarModalRebote() {
  mostrarModalRebote.value = false
  inscripcionSeleccionada.value = null
}

function abrirModalNotas(inscripcion) {
  inscripcionSeleccionada.value = inscripcion
  formNotas.notas = inscripcion.notas || ''
  mostrarModalNotas.value = true
}

function abrirModalComentario(inscripcion) {
  inscripcionSeleccionada.value = inscripcion
  formComentario.comentario = ''
  mostrarModalComentario.value = true
  nextTick(() => {
    if (textareaComentario.value) {
      textareaComentario.value.focus()
    }
  })
}

function cerrarModalComentario() {
  mostrarModalComentario.value = false
  inscripcionSeleccionada.value = null
}

function guardarComentario() {
  if (!inscripcionSeleccionada.value || !formComentario.comentario.trim()) return
  guardandoComentario.value = true
  const inscripcionId = inscripcionSeleccionada.value.id

  fetch(route('inscripciones.agregar-comentario', inscripcionId), {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': usePage().props.csrf_token || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      'Accept': 'application/json',
    },
    body: JSON.stringify({
      comentario: formComentario.comentario.trim()
    })
  })
    .then(async response => {
      if (!response.ok) {
        let msg = 'Ha ocurrido un error al guardar el comentario.'
        if (response.status === 419) msg = 'La sesión ha expirado. Por favor, recarga la página.'
        if (response.status === 403) msg = 'No tienes permisos para realizar esta acción.'
        if (response.status === 422) msg = 'El comentario no es válido.'
        alert(msg)
        cerrarModalComentario()
        guardandoComentario.value = false
        return
      }
      // Si la respuesta es exitosa, procesar el JSON
      const data = await response.json()
      cerrarModalComentario()
      // Actualizar las notas en la copia local si vienen en la respuesta
      const inscripcion = inscripcionesLocal.value.find(i => i.id === inscripcionId)
      if (inscripcion && (data?.props?.inscripcion || data?.inscripcion)) {
        inscripcion.notas = data.props?.inscripcion?.notas || data.inscripcion?.notas
      }
      guardandoComentario.value = false
    })
    .catch((error) => {
      alert('Error de red o inesperado al guardar el comentario.')
      cerrarModalComentario()
      guardandoComentario.value = false
    })
}

function cerrarModalNotas() {
  mostrarModalNotas.value = false
  inscripcionSeleccionada.value = null
}

function toggleDetalles(inscripcionId) {
  if (tarjetasExpandidas.value.has(inscripcionId)) {
    tarjetasExpandidas.value.delete(inscripcionId)
  } else {
    tarjetasExpandidas.value.add(inscripcionId)
  }
}

function destacarTarjeta(inscripcionId, duracion = 5000) {
  tarjetasConCambios.value.add(inscripcionId)

  // Quitar el destacado después del tiempo especificado
  setTimeout(() => {
    tarjetasConCambios.value.delete(inscripcionId)
  }, duracion)
}

function guardarNotas() {
  if (!inscripcionSeleccionada.value) return

  guardandoNotas.value = true
  const inscripcionId = inscripcionSeleccionada.value.id

  router.put(route('inscripciones.actualizar-notas', inscripcionId), {
    notas: formNotas.notas
  }, {
    onSuccess: () => {
      cerrarModalNotas()
      // Actualizar las notas en la copia local
      const inscripcion = inscripcionesLocal.value.find(i => i.id === inscripcionId)
      if (inscripcion) {
        inscripcion.notas = formNotas.notas
      }
    },
    onFinish: () => {
      guardandoNotas.value = false
    }
  })
}

function rebotarInscripcion() {
  if (!inscripcionSeleccionada.value || formRebote.motivo.trim().length < 10) return

  fetch(route('inscripciones.rebotar', inscripcionSeleccionada.value.id), {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': usePage().props.csrf_token || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      'Accept': 'application/json',
    },
    body: JSON.stringify({
      motivo: formRebote.motivo.trim()
    })
  })
  .then(response => {
    if (!response.ok) {
      if (response.status === 419) throw new Error('CSRF_TOKEN_EXPIRED');
      if (response.status === 403) throw new Error('PERMISSION_DENIED');
      if (response.status === 422) throw new Error('VALIDATION_ERROR');
      throw new Error(`HTTP_ERROR_${response.status}`);
    }
    return response.json();
  })
  .then(data => {
    if (data.message) {
      const inscripcionId = inscripcionSeleccionada.value?.id
      cerrarModalRebote()
      // Cerrar detalles de la tarjeta si está abierta
      if (inscripcionId) {
        tarjetasExpandidas.value.delete(inscripcionId)
        const inscripcion = inscripcionesLocal.value.find(i => i.id === inscripcionId)
        if (inscripcion) {
          inscripcion.estado = 'rebotada'
          const fecha = new Date().toLocaleDateString('es-ES')
          const usuario = usePage().props.auth?.user?.name || 'Usuario'
          const nuevaNota = `- ${fecha} - Rebotada por ${usuario}: ${formRebote.motivo.trim()}`
          inscripcion.notas = inscripcion.notas ? inscripcion.notas + '\n' + nuevaNota : nuevaNota
        }
      }
    }
  })
  .catch(error => {
    console.error('Error al rebotar inscripción:', error)
    if (error.message === 'CSRF_TOKEN_EXPIRED') {
      alert('La sesión ha expirado. Por favor, recarga la página e intenta de nuevo.')
    } else if (error.message === 'PERMISSION_DENIED') {
      alert('No tienes permisos para realizar esta acción.')
    } else if (error.message === 'VALIDATION_ERROR') {
      alert('El motivo del rebote debe tener al menos 10 caracteres.')
    } else {
      alert('Ha ocurrido un error. Por favor, intenta de nuevo.')
    }
  })
}

function cambiarEstado(inscripcion, nuevoEstado) {
  if (inscripcion.estado === nuevoEstado) return
  const estadoAnterior = inscripcion.estado
  inscripcion.estado = nuevoEstado
  tarjetasExpandidas.value.delete(inscripcion.id)
  destacarTarjeta(inscripcion.id, 7000)
  router.post(route('inscripciones.actualizar-estado', inscripcion.id), {
    estado: nuevoEstado
  }, {
    onSuccess: () => {},
    onError: (errors) => {
      inscripcion.estado = estadoAnterior
      tarjetasConCambios.value.delete(inscripcion.id)
      console.error('Error al cambiar estado:', errors)
    },
    preserveState: true,
    preserveScroll: true
  })
}

function reasignarInscripcion(inscripcion) {
  const estadoAnterior = inscripcion.estado
  inscripcion.estado = 'asignada'
  tarjetasExpandidas.value.delete(inscripcion.id)
  router.post(route('inscripciones.actualizar-estado', inscripcion.id), {
    estado: 'asignada'
  }, {
    onSuccess: () => {
      inscripcionesRecienReasignadas.value.add(inscripcion.id)
      nextTick(() => {
        setTimeout(() => {
          const tarjetaElement = document.querySelector(`[data-inscripcion-id="${inscripcion.id}"]`)
          if (tarjetaElement) {
            tarjetaElement.scrollIntoView({
              behavior: 'smooth',
              block: 'center',
              inline: 'nearest'
            })
          }
        }, 100)
      })
      setTimeout(() => {
        inscripcionesRecienReasignadas.value.delete(inscripcion.id)
      }, 10000)
    },
    onError: (errors) => {
      inscripcion.estado = estadoAnterior
      console.error('Error al reasignar inscripción:', errors)
      alert('Ha ocurrido un error al reasignar la inscripción. Por favor, intenta de nuevo.')
    },
    preserveState: true,
    preserveScroll: true
  })
}

function getEstadoClass(estado) {
  if (!estado) {
    return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
  }

  const clases = {
    'nueva': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    'asignada': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    'contactado': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'encurso': 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
    'finalizado': 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
    'nocontesta': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    'rebotada': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
    'duplicada': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
    'nointeresado': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    'abandonado': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
  }
  return clases[estado] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
}

function formatearFecha(fecha) {
  return new Date(fecha).toLocaleDateString('es-ES', {
    month: 'short',
    day: 'numeric'
  })
}

function formatearFechaCompleta(fecha) {
  return new Date(fecha).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function calcularEdad(fechaNacimiento) {
  const nacimiento = new Date(fechaNacimiento)
  const hoy = new Date()
  let edad = hoy.getFullYear() - nacimiento.getFullYear()
  const mes = hoy.getMonth() - nacimiento.getMonth()

  if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
    edad--
}
    return edad
}

</script>

<style scoped>
/* ANIMACIONES AVANZADAS PARA TRANSITIONGROUP */

/* Animación de movimiento suave para reordenamiento */
.inscripciones-move {
  transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

/* Animaciones de entrada */
.inscripciones-enter-active {
  transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.inscripciones-enter-from {
  opacity: 0;
  transform: translateY(-20px) scale(0.95);
}

.inscripciones-enter-to {
  opacity: 1;
  transform: translateY(0) scale(1);
}

/* Animaciones de salida */
.inscripciones-leave-active {
  transition: all 0.5s cubic-bezier(0.55, 0.085, 0.68, 0.53);
  position: relative;
  z-index: 0;
}

.inscripciones-leave-from {
  opacity: 1;
  transform: translateY(0) scale(1);
}

.inscripciones-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}

/* Clases básicas para tarjetas */
.inscripcion-card {
  position: relative;
  transform-origin: center;
}

/* Transición suave para cambios de estado visual */
.inscripcion-card {
  transition: border-color 0.3s ease, background-color 0.3s ease;
}

/* Separación entre tarjetas */
.space-y-4 > * + * {
  margin-top: 1rem;
}

/* Mejorar la transición cuando una tarjeta cambia de activa a cerrada */
.inscripcion-cerrada {
  transition: all 0.4s ease;
}

.inscripcion-activa {
  transition: all 0.4s ease;
}
</style>
