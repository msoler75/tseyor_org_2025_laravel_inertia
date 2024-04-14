<template>
    <button type="button"
        class="w-42 flex items-center text-sm leading-6 text-slate-400 rounded-md ring-1 ring-slate-900/10 shadow-sm py-1.5 pl-2 pr-3 hover:ring-slate-300 dark:bg-slate-800 dark:highlight-white/5 dark:hover:bg-slate-700 select-none flex-nowrap flex-shrink-0"
        @click="search.opened = true">
        <Icon icon="ph:magnifying-glass-bold" class="mr-2" />
        Buscar...<span class="hidden lg:inline ml-auto pl-3 flex-none text-xs font-semibold">Ctrl K</span>

        <Modal :show="search.opened" @close="search.opened = false" maxWidth="lg">
            <div class="modal-search bg-base-100 flex flex-col text-sm pb-7">
                <div class="flex gap-2 items-center p-3">
                    <Icon v-show="!loading" icon="ph:magnifying-glass-bold" class="text-lg" />
                    <Spinner v-show="loading" class="text-lg" />
                    <div class="flex-grow relative">
                        <input id="global-search-input" ref="input" class="search-input w-full" v-model="search.query"
                            aria-autocomplete="both" autocomplete="off" autocorrect="off" autocapitalize="off"
                            enterkeyhint="go" spellcheck="false" placeholder="Buscar en el sitio web..." maxlength="64"
                            type="search" aria-owns="search-input-list"
                            :aria-activedescendant="itemSeleccionado ? itemSeleccionado.idDom : ''"
                            aria-controls="search-input-list" aria-haspopup="true">
                    </div>

                    <kbd class="hidden lg:flex kbd cursor-pointer select-none text-xs font-semibold"
                        @click="search.opened = false">ESC</kbd>
                    <Icon icon="material-symbols-light:close-rounded" class="lg:hidden text-3xl"
                        @click="search.opened = false" />
                </div>

                <div class="overflow-y-auto max-h-[calc(100vh-170px)] border-t border-gray-500 border-opacity-20"
                    id="search-input-list">

                    <div v-if="!resultadosAgrupados.length" class="p-7">
                        <div v-if="search.lastQuery" class="text-center text-lg text-gray-500">
                            <span v-if="search.valid">No hay resultados para</span>
                            <span v-else>Demasiados resultados para</span> "<span class="text-primary">{{ search.lastQuery }}</span>"
                        </div>

                        <div v-if="search.showSuggestions" class="mt-7">
                            <div class="font-bold mb-4">Sugerencias:</div>
                            <div v-for="q of predefinedQueries" :key="q" @click="clickPredefined(q)"
                                class="bg-base-200 bg-opacity-50 flex items-center justify-between w-full py-3 px-4 my-1 cursor-pointer rounded-lg text-primary hover:underline">
                                <span class="after:content-['_↗']">
                                    {{ q.query }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div v-else v-for="grupo of resultadosAgrupados" :key="grupo"
                        class="busqueda-resultados flex flex-wrap p-3">
                        <div
                            class="w-full flex justify-between px-1 mt-3 mb-2 text-neutral font-bold capitalize opacity-50">
                            {{
                                traducir(grupo.coleccion) }}
                        </div>
                        <Link v-for="item of grupo.items" :key="item.id" :id="item.idDom"
                            class="w-full py-3 px-4 bg-base-200 rounded-lg my-1 flex gap-3 justify-between items-center"
                            role="option" @mouseover="seleccionarItem(item, true)" :href="calcularUrl(item)"
                            @click="clickHandle(calcularUrl(item))"
                            :aria-selected="itemSeleccionado && itemSeleccionado.id == item.id"
                            :class="itemSeleccionado && itemSeleccionado.id == item.id ? 'seleccionado' : ''">
                        <div v-html="item.titulo" />
                        <span class="text-lg">›</span>
                        </Link>
                    </div>
                </div>

            </div>
        </Modal>
    </button>
</template>

<script setup>
import useGlobalSearch from "@/Stores/globalSearch.js"
import { usePage } from '@inertiajs/vue3';


const portada = computed(() => page.url == '/')
const page = usePage()
const nav = useNav()
const search = useGlobalSearch()

const input = ref(null)

const predefinedQueries = ref([
    { query: 'Contacta con nosotros', collections: 'paginas' },
    { query: '¿Dónde estamos?', collections: 'paginas' },
    { query: 'Libros para comenzar', collections: 'paginas' },
    { query: '¿Dónde puedo inscribirme?', collections: 'paginas' },
    { query: 'Ayuda humanitaria' }
])

const resultadosAgrupados = computed(() => {
    if (search.results === null) return []

    const agrupados = {}
    console.log('search.results', search.results)
    for (const item of search.results.data) {

        if (!item.idDom)
            item.idDom = 'result-' + item.slug_ref + '-' + Math.floor(Math.random() * 1000)

        if (!agrupados[item.coleccion]) {
            agrupados[item.coleccion] = []
        }
        agrupados[item.coleccion].push(item)
    }

    // Crear el array de objetos agrupados
    const items = Object.entries(agrupados).map(([coleccion, items]) => ({
        coleccion,
        items
    }))

    // Ordenar el array de items
    items.sort((a, b) => {
        const prioridad = ['libros', 'centros', 'lugares', 'guias', 'terminos', 'paginas'] // paginas es el más prioritario

        const indexA = prioridad.indexOf(a.coleccion)
        const indexB = prioridad.indexOf(b.coleccion)

        return indexB - indexA
    })

    nextTick(() => {
        seleccionarItem(null)
        siguienteItem()
    })

    return items
})

const itemsArray = computed(() => {
    const items = []
    for (var grupo of resultadosAgrupados.value) {
        for (var item of grupo.items)
            items.push(item)
    }
    return items
})

var usoTeclas = false

onMounted(() => {
    window.addEventListener('keydown', function (event) {
        if (event.ctrlKey && event.key === 'k') {
            event.preventDefault()
            search.opened = true
        }
        else if (search.opened) {
            console.log(event.key)
            switch (event.key) {
                case 'Enter':
                    if (itemSeleccionado.value) {
                        event.preventDefault()
                        const elem = document.querySelector('.busqueda-resultados .seleccionado')
                        elem.click()
                    }
                    break;
                case 'ArrowDown':
                    event.preventDefault()
                    siguienteItem()
                    usoTeclas = true
                    break;
                case 'ArrowUp':
                    event.preventDefault()
                    anteriorItem()
                    usoTeclas = true
                    break;
                case 'Home':
                    if (usoTeclas) {
                        event.preventDefault()
                        primerItem()
                    }
                    break;
                case 'End':
                    if (usoTeclas) {
                        event.preventDefault()
                        ultimoItem()
                    }
                    break;
            }
        }
    });
})

watch(() => search.opened, (value) => {
    usoTeclas = false
    if (value) {
        nav.sideBarShow = false // cerramos la sidebar
        currentUrl = window.location.pathname
        nextTick(() => {
            input.value.focus()
        })
    }
})

function calcularUrl(item) {
    return item.coleccion != 'paginas' ? (route(item.coleccion) + '/' + (item.slug_ref || item.id_ref)) : '/' + item.slug_ref
}

// para buscar
const loading = ref(false)
const queryLoading = ref("")
var timerBuscar = null

// para guardar estadísticas de búsqueda
var currentUrl = window.location.pathname
var timerGuardarBusqueda = null
var busquedaId = null

function buscar() {
    if (loading.value) {
        console.log('esperando carga de anterior busqueda', queryLoading.value, search.query)
        // clearTimeout(timerBuscar)
        timerBuscar = setTimeout(buscar, 250)
        return
    }
    if (search.query) {
        var currentQuery = search.query
        queryLoading.value = currentQuery
        loading.value = true
        axios.get(route('buscar') + '?query=' + search.query
            + (search.restrictToCollections ? '&collections=' + search.restrictToCollections : '')
        )
            .then(response => {
                console.log('response-data', response.data)
                search.results = response.data.listado
                search.valid = response.data.busquedaValida
                loading.value = false
                timerGuardarBusqueda = setTimeout(guardarBusqueda, 2000)
            })
            .catch(error => {
                loading.value = false
            })
            .finally(() => {
                search.lastQuery = currentQuery
            })
    }

}



async function guardarBusqueda(url) {
    clearTimeout(timerGuardarBusqueda)
    console.log('guardarBusqueda', url)
    const data = new FormData();
    data.append('query', search.query ? search.query : search.lastQuery);
    data.append('origen', currentUrl);
    if (busquedaId)
        data.append('id', busquedaId);
    if (url)
        data.append('click_url', url);

    return axios.post(route('busqueda.guardar'), data)
        .then(response => {
            busquedaId = response.data.id
        })
}

watch(() => search.query, (value) => {
    busquedaId = null // borramos id de la busqueda actual
    clearTimeout(timerGuardarBusqueda) // borramos contador de tiempo para guardar los datos de la busqueda actual, seguramente el usuario está escribiendo aún
    clearTimeout(timerBuscar) // borramos contador de tiempo para ejecutar la busqueda
    if (value)
        timerBuscar = setTimeout(buscar, 250)
    else {
        search.results = null
        search.lastQuery = null
        search.restrictToCollections = null
    }
})


function clickPredefined(q) {
    search.query = q.query
    search.restrictToCollections = q.collections
}

function clickHandle(url) {
    console.log('clickHandle', url)
    nextTick(async () => {
        search.opened = false
        await guardarBusqueda(url)
        // busquedaId = null
        // search.query = ""
        search.showSuggestions = true
    })
}

const traducciones = {
    paginas: 'páginas',
    guias: 'guías estelares',
    terminos: 'glosario',
    lugares: 'lugares de la galaxia'
}

function traducir(col) {
    return traducciones[col] || col
}


// SELECCIONES DE ITEM

const itemSeleccionado = ref(null)

function seleccionarItem(item, noScroll) {
    itemSeleccionado.value = item
    if (item && !noScroll) {
        // comprueba si el resultado está fuera de visión, en tal caso desplaza el scroll
        const element = document.querySelector("#" + item.idDom)
        if (!element) return

        const container = document.getElementById('search-input-list');

        const containerRect = container.getBoundingClientRect();
        const elementRect = element.getBoundingClientRect();

        // Verificar si el elemento está fuera del contenedor
        if (
            elementRect.bottom > containerRect.bottom ||
            elementRect.top < containerRect.top
        ) {
            element.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
        }

    }
}

function siguienteItem() {
    if (!itemSeleccionado.value) {
        itemSeleccionado.value = itemsArray.value[0]
        return
    }
    var idx = itemsArray.value.findIndex(x => x.id == itemSeleccionado.value.id)
    idx = (idx + 1) % itemsArray.value.length
    seleccionarItem(itemsArray.value[idx])
}

function anteriorItem() {
    if (!itemSeleccionado.value) {
        itemSeleccionado.value = itemsArray.value[itemsArray.length - 1]
        return
    }
    var idx = itemsArray.value.findIndex(x => x.id == itemSeleccionado.value.id)
    idx = (idx + itemsArray.value.length - 1) % itemsArray.value.length
    seleccionarItem(itemsArray.value[idx])
}

function primerItem() {
    if (itemsArray.value.length)
        seleccionarItem(itemsArray.value[0])
}

function ultimoItem() {
    if (itemsArray.value.length)
        seleccionarItem(itemsArray.value[itemsArray.value.length - 1])
}
</script>

<style scoped>
a.seleccionado {
    @apply bg-primary text-white dark:text-black;
}

a.seleccionado :deep(em.search-term) {
    @apply text-white;
}

[data-theme='winter'] a.seleccionado :deep(em.search-term),
.dark a.seleccionado :deep(em.search-term) {
    @apply text-black;
}
</style>
