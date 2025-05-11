<template>
    <div class="flex h-full">
        <h1 class="hidden">Correos</h1>
        <!-- Sidebar -->
        <aside class="w-1/4 bg-gray-100 dark:bg-gray-900 p-4">
            <ul>
                <li
                    v-for="categoria in categorias"
                    :key="categoria.id"
                    class="mb-2"
                >
                    <div
                        class="px-3 rounded-lg cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600"
                        :class="{
                            'bg-gray-300 dark:bg-gray-700':
                                categoriaSeleccionada === categoria.id,
                        }"
                        @click="categoriaSeleccionada = categoria.id"
                    >
                        <p class="font-semibold">{{ categoria.nombre }}</p>
                    </div>
                </li>
            </ul>
        </aside>

        <div class="w-full h-full">
            <div class="bg-base-100 px-5 pt-3 flex gap-2 items-center justify-between">
                <button v-if="viendoCorreo" @click="viendoCorreo = null" class="btn btn-xs font-bold">
                    <Icon icon="ph:arrow-left"/>
                </button>
                <button
                    @click="navegarCorreo(-1)"
                    :disabled="!correoAnterior"
                    class="btn btn-xs ml-auto"
                >
                    Anterior
                </button>
                <button
                    @click="navegarCorreo(1)"
                    :disabled="!correoSiguiente"
                    class="btn btn-xs"
                >
                    Siguiente
                </button>
            </div>

            <!-- Main Content -->
            <main
                class="flex-1 h-full bg-white dark:bg-gray-800 p-6 overflow-y-auto"
                @scroll="handleScroll"
            >
                <div v-if="viendoCorreo" class="h-full overflow-y-auto">
                    <h1 class="text-2xl font-bold mb-4">
                        {{ correoSeleccionado?.subject }}
                    </h1>
                    <p class="text-sm text-gray-500 mb-6 flex flex-wrap justify-between">
                        <span>
                        De: {{ correoSeleccionado?.from }} a
                        {{ correoSeleccionado?.to }}
                        </span>
                        <TimeAgo :date="correoSeleccionado?.created_at"/>
                    </p>
                    <Content
                        :content="correoSeleccionado?.body"
                        :optimize-images="false"
                        class="flex-grow h-full"
                    />
                </div>

                <div v-else>
                    <ul>
                        <li
                            v-for="grupo in correosAgrupados"
                            :key="grupo.subject"
                            class="mb-1"
                        >
                            <div class="font-semibold mb-2">
                                {{ grupo.subject }}
                            </div>
                            <ul>
                                <li
                                    v-for="correo in grupo.correos"
                                    :key="correo.id"
                                >
                                    <div
                                        class="grid grid-cols-3 gap-2 rounded-lg cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-800"
                                        :class="{
                                            'bg-gray-300 dark:bg-gray-700':
                                                viendoCorreo === correo.id,
                                        }"
                                        @click="clickHandle(correo.id)"
                                    >
                                        <p
                                            class="text-sm text-gray-500 truncate"
                                        >
                                            De: {{ correo.from }}
                                        </p>
                                        <p class="font-semibold truncate">
                                            {{ correo.subject }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            <TimeAgo :date="correo.created_at"/>
                                        </p>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>

                    <pagination
                        class="mt-6"
                        :links="listado.links"
                        @click="loadMore"
                    />
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    email: { required: false },
    listado: {
        default: () => ({ data: [], links: [] }),
    },
});

const viendoCorreo = ref(props.email?.id || null);
const listado = ref(props.listado);
const categoriaSeleccionada = ref(null);
const loading = ref(false);

const categorias = ref([
    { id: "todos", nombre: "Todos" },
    { id: "hoy", nombre: "Hoy" },
    { id: "ultima-semana", nombre: "Última semana" },
    { id: "ultimo-mes", nombre: "Último mes" },
    { id: "otros", nombre: "Otros" },
]);

const correosFiltrados = computed(() => {
    const now = new Date();
    return listado.value.data.filter((correo) => {
        if (categoriaSeleccionada.value === "hoy") {
            const correoFecha = new Date(correo.created_at);
            return correoFecha.toDateString() === now.toDateString();
        } else if (categoriaSeleccionada.value === "ultima-semana") {
            const correoFecha = new Date(correo.created_at);
            const unaSemanaAtras = new Date();
            unaSemanaAtras.setDate(now.getDate() - 7);
            return correoFecha >= unaSemanaAtras && correoFecha <= now;
        } else if (categoriaSeleccionada.value === "ultimo-mes") {
            const correoFecha = new Date(correo.created_at);
            const unMesAtras = new Date();
            unMesAtras.setMonth(now.getMonth() - 1);
            return correoFecha >= unMesAtras && correoFecha <= now;
        }
        return true; // Mostrar todos si no hay categoría seleccionada o es "todos"
    });
});

const correosAgrupados = computed(() => {
    const agrupados = {};
    correosFiltrados.value.forEach((correo) => {
        if (!agrupados[correo.subject]) {
            agrupados[correo.subject] = [];
        }
        agrupados[correo.subject].push(correo);
    });
    return Object.keys(agrupados).map((subject) => ({
        subject,
        correos: agrupados[subject],
    }));
});

const correoSeleccionado = computed(() => {
    return (
        listado.value.data.find((correo) => correo.id === viendoCorreo.value) ||
        null
    );
});

const correoAnterior = computed(() => {
    if (!correoSeleccionado.value) return null;
    const index = listado.value.data.findIndex(
        (correo) => correo.id === viendoCorreo.value
    );
    return listado.value.data[index - 1] || null;
});

const correoSiguiente = computed(() => {
    if (!correoSeleccionado.value) return null;
    const index = listado.value.data.findIndex(
        (correo) => correo.id === viendoCorreo.value
    );
    return listado.value.data[index + 1] || null;
});

function clickHandle(id) {
    viendoCorreo.value = viendoCorreo.value === id ? null : id;
}

function navegarCorreo(direccion) {
    const index = listado.value.data.findIndex(
        (correo) => correo.id === viendoCorreo.value
    );
    const nuevoIndex = index + direccion;
    if (nuevoIndex >= 0 && nuevoIndex < listado.value.data.length) {
        viendoCorreo.value = listado.value.data[nuevoIndex].id;
    }
}

async function loadMore() {
    if (loading.value || !listado.value.links.next) return;
    loading.value = true;
    try {
        const response = await fetch(listado.value.links.next, {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
        });
        const data = await response.json();
        listado.value.data.push(...data.data);
        listado.value.links = data.links;
    } catch (error) {
        console.error("Error al cargar más correos:", error);
    } finally {
        loading.value = false;
    }
}

function handleScroll(event) {
    const { scrollTop, scrollHeight, clientHeight } = event.target;
    if (scrollTop + clientHeight >= scrollHeight - 50) {
        loadMore();
    }
}
</script>

<style scoped>
/* Estilos adicionales para mejorar la apariencia */
aside {
    border-right: 1px solid #e5e7eb;
}

main {
    display: flex;
    flex-direction: column;
}

.btn {
    background-color: #4a90e2;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    cursor: pointer;
}

.btn:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}
</style>
