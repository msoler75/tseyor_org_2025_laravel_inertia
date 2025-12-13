<template>
    <button type="button"
        class="w-fit flex items-center text-sm leading-6 text-slate-400 rounded-md ring-1 ring-slate-900/10 shadow-2xs py-1.5 pl-2 pr-3 hover:ring-slate-300 dark:bg-slate-800 dark:highlight-white/5 dark:hover:bg-slate-700 select-none flex-nowrap shrink-0"
        @click="search.opened = true">
        <Icon icon="ph:magnifying-glass-bold" class="mr-2" />
        <span class="hidden xs:inline">Buscar en el sitio...</span>
        <span class="xs:hidden">Buscar...</span>
            <span class="hidden ml-auto pl-3 flex-none text-xs font-semibold">Ctrl K</span>

        <Modal :show="search.opened" @close="closeModal" maxWidth="lg">
            <div class="modal-search bg-base-100 flex flex-col text-sm pb-7">
                <div class="flex gap-2 items-center p-3">
                    <Icon v-show="!search.searching" icon="ph:magnifying-glass-bold" class="text-2xl" />
                    <Spinner v-show="search.searching" class="text-2xl" />
                    <div class="grow relative">
                        <input id="global-search-input" ref="input" class="search-input w-full !pr-8
                        placeholder:text-gray-500 dark:placeholder:text-gray-400"
                            :value="search.query"
                            @input="handleQueryInput"
                            aria-autocomplete="both" autocomplete="off" autocorrect="off" autocapitalize="off"
                            enterkeyhint="go" spellcheck="false" placeholder="Buscar en el sitio web..." :maxlength="INPUT_MAX_LENGTH"
                            type="search" aria-owns="search-input-list"
                            :aria-activedescendant="itemSeleccionado ? itemSeleccionado.idDom : ''"
                            aria-controls="search-input-list" aria-haspopup="true">
                            <button
                                v-if="search.query"
                                type="button"
                                @click="clearQuery"
                                class="text-3xl absolute right-1 top-1/2 -translate-y-1/2 p-0 cursor-pointer"
                                tabindex="-1"
                                aria-label="Limpiar búsqueda"
                                title="Limpiar búsqueda"
                            >
                            <Icon icon="jam:rubber" class="text-orange-500 opacity-80 hover:opacity-100 transform -translate-y-0.5 hover:scale-125 duration-100" />
                            </button>
                        </input>
                    </div>

                    <span title="Cerrar búsqueda" class="text-3xl cursor-pointer transition duration-100 transform hover:scale-125 opacity-80 hover:opacity-100" @click="closeModal" >
                        <Icon icon="material-symbols:cancel" />
                    </span>
                </div>

                <div class="overflow-y-auto max-h-[calc(100vh-170px)] border-t border-gray-500 border-opacity-20"
                    id="search-input-list">

                    <div v-if="!resultadosAgrupados.length" class="p-7">
                        <div v-if="search.lastQuery" class="text-center text-lg text-gray-500">
                            <span v-if="search.valid">No hay resultados para</span>
                            <span v-else>Demasiados resultados para</span> "<span class="text-primary">{{
                                search.lastQuery }}</span>"
                        </div>

                        <div v-if="search.showSuggestions" class="mt-7">
                            <div class="font-bold mb-4">Sugerencias:</div>
                            <div v-for="q of predefinedQueries" :key="q" @click="clickPredefined(q)"
                                class="bg-base-200/50 flex items-center justify-between w-full py-3 px-4 my-1 cursor-pointer rounded-lg text-primary hover:underline">
                                <span class="after:content-['_↗']">
                                    {{ q.query }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div v-else v-for="grupo of resultadosAgrupados" :key="grupo"
                        class="busqueda-resultados flex flex-wrap p-3">
                        <div
                            class="w-full flex justify-between px-1 mt-3 mb-2 font-bold capitalize opacity-75">
                            {{ traducir(grupo.coleccion) }}
                            <span class="ml-auto" :class="selectors.developerMode?'':'hidden'">{{ Math.round(grupo.score*SCORE_ROUND_DIVISOR)/SCORE_ROUND_DIVISOR }}</span>
                            <span v-if="grupo.coleccion in prioridadBoost" :class="selectors.developerMode?'':'hidden'"> +{{ prioridadBoost[grupo.coleccion] }}</span>
                        </div>
                        <!-- Usa el componente Link de Inertia y maneja el evento click correctamente -->
                        <Link v-for="item of grupo.items" :key="item.id" :id="item.idDom"
                            class="w-full py-3 px-4 bg-base-200 rounded-lg my-1 flex gap-3 items-center"
                            role="option"
                            @mouseover="seleccionarItem(item, true)"
                            :href="calcularUrl(item)"
                            @click="onLinkClick($event, item)"
                            :aria-selected="itemSeleccionado && itemSeleccionado.id == item.id"
                            :class="[itemSeleccionado && itemSeleccionado.id == item.id ? 'seleccionado' : '',
                                item.isLinkAll ? '': 'justify-between'
                            ]">
                            <span class="text-lg" v-if="item.isLinkAll">→</span>
                            <div v-html="item.titulo" :class="item.isLinkAll?'text-xs':'text-sm'"/>
                            <span v-if="false && item.isLinkAll && grupo.ocultos" class="text-xs font-italic">(al menos {{ grupo.ocultos }} resultados más)</span>
                            <span class="text-lg" v-if="!item.isLinkAll">›</span>
                        </Link>
                    </div>
                </div>

            </div>
        </Modal>
    </button>
</template>

<script setup>
import useGlobalSearch from "@/Stores/globalSearch.js"
import traducir from '@/composables/traducciones'
import { removeAccents, levenshtein } from '@/composables/textutils'
import { useGoogleAnalytics } from '@/composables/useGoogleAnalytics.js';

// Constantes para evitar magic numbers
const SAVE_SEARCH_DELAY_MS = 2000

// Variables locales del componente
const DOM_OPERATION_DELAY_MS = 10
const RANDOM_ID_MULTIPLIER = 1000
const DEFAULT_PRIORITY_VALUE = 40
const INPUT_MAX_LENGTH = 64
const PRIORITY_FACTOR = 0.1
const SCORE_ROUND_DIVISOR = 10

const selectors = useSelectors();
const { trackSearch } = useGoogleAnalytics();

const prioridad_grupos = [
    'paginas', // el más prioritario
    'terminos',
    'guias',
    'lugares',
    'audios',
    'centros',
    'entradas',
    'libros',
    'psicografias',
    'meditaciones',
    'videos',
    ]

const prioridadBoost = {
    paginas: 5,
    libros: 1,
}

// Mapeo de colecciones a rutas amigables
const rutasColeccion = {
    terminos: 'glosario',
    entradas: 'blog',
    // puedes añadir más si necesitas otras traducciones de rutas
};

// const portada = computed(() => page.url == '/')
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

const currentQueryStd = computed(() => removeAccents(search.lastQuery))

function removeGargabe(txt) {
    return removeAccents(txt.replace(/<em class="search-term">/g, '').replace(/<\/em>/g, ''))
}

function nearness(t) {
    return levenshtein(currentQueryStd.value, removeGargabe(t))
}

const MAX_ITEMS_POR_CATEGORIA = 4 // Cambia este valor para ajustar el máximo de items por categoría

// agrupamos por categorias, pero tenemos en cuenta los resultados de la búsqueda
const resultadosAgrupados = computed(() => {
    if (search.results === null) return []

    const agrupados = {}
    console.log('search.results:', search.results)
    for (const key in search.results) {
        const item = search.results[key]
        if (!item.idDom)
            item.idDom = 'result-' + item.slug_ref + '-' + Math.floor(Math.random() * RANDOM_ID_MULTIPLIER)
        if (!agrupados[item.coleccion]) {
            agrupados[item.coleccion] = []
        }
        agrupados[item.coleccion].push(item)
    }


    // Crear el array de objetos agrupados
    const grupos = Object.entries(agrupados).map(([coleccion, items]) => {
        const itemsLimitados = items.slice(0, MAX_ITEMS_POR_CATEGORIA)
        const ocultos = items.length > MAX_ITEMS_POR_CATEGORIA ? (items.length - MAX_ITEMS_POR_CATEGORIA) : 0
        if (coleccion !== 'paginas') {
            const linkItem = {
                id: `ver-todos-${coleccion}`,
                idDom: `ver-todos-${coleccion}`,
                coleccion,
                // Traducción para el texto del enlace especial
                titulo: `<span class="italic">Buscar en ${traducir(coleccion)}</span>`,
                isLinkAll: true,
                __tntSearchScore__: -1
            }
            itemsLimitados.push(linkItem)
        }
        return {
            coleccion,
            items: itemsLimitados,
            ocultos, // <-- número de items ocultos en esta categoría
            score: items.reduce((max, item) => Math.max(max, item.__tntSearchScore__), 0)
        }
    })


    grupos.forEach(grupo => {
        grupo.items.sort((a, b) => {
            // Si alguno es el "link all", siempre va al final
            if (a.isLinkAll) return 1
            if (b.isLinkAll) return -1
            if (a.__tntSearchScore__ == b.__tntSearchScore__) {
                const nA = nearness(a.titulo)
                const nB = nearness(b.titulo)
                if (nA == nB)
                    return a.titulo.length - b.titulo.length
                return nA - nB
            }
            return b.__tntSearchScore__ - a.__tntSearchScore__
        })
    })


    // Ordenar el array de items
    grupos.sort((a, b) => {
        const Kp = PRIORITY_FACTOR
        const dScore = b.score - a.score
        const prioridad = prioridad_grupos
        const pA = prioridad.indexOf(a.coleccion)
        const pB = prioridad.indexOf(b.coleccion)
        const dP = (pA<0?DEFAULT_PRIORITY_VALUE:pA) - (pB<0?DEFAULT_PRIORITY_VALUE:pB)// reverse order
        const boostA = prioridadBoost[a.coleccion] || 0
        const boostB = prioridadBoost[b.coleccion] || 0
        const dBoost = boostB - boostA
        console.log('a', a.coleccion, 'b', b.coleccion, 'dP', dP, 'dScore', dScore, 'dBoost', dBoost)
        return dP*Kp + dScore*(1-Kp) + dBoost
    })

    nextTick(() => {
        seleccionarItem(null)
        siguienteItem()
    })

    return grupos
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
let modalHistoryState = false // Flag para controlar si hemos agregado estado al historial
let savedScrollPosition = 0 // Posición de scroll guardada

// Función para manejar el evento popstate (botón atrás)
function handlePopState(event) {
    console.log('handlePopState: evento popstate detectado', { opened: search.opened, modalHistoryState })
    if (search.opened && modalHistoryState) {
        // Cerrar el modal sin navegar
        search.opened = false
        modalHistoryState = false

        // Restaurar la posición de scroll después de un pequeño delay
        nextTick(() => {
            setTimeout(() => {
                window.scrollTo(0, savedScrollPosition)
                console.log('handlePopState: scroll restaurado a', savedScrollPosition)
            }, 10)
        })

        // Agregar de nuevo el estado actual para mantener la URL con la posición de scroll
        window.history.pushState({
            modalOpen: false,
            scrollY: savedScrollPosition
        }, '', window.location.href)
        console.log('handlePopState: modal cerrado, estado restaurado')
    }
}

onMounted(() => {
    // Registrar callback para cambios de query
    search.onQueryChange(handleQueryChange)

    window.addEventListener('keydown', function (event) {
        if (event.ctrlKey && event.key === 'k') {
            event.preventDefault()
            search.opened = true
            console.log('keydown: ctrl+k, search.opened = true')
        }
        else if (search.opened) {
            console.log(event.key)
            switch (event.key) {
                case 'Escape':
                    event.preventDefault()
                    closeModal()
                    break;
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

    // Gestión del botón "atrás" para cerrar el modal
    window.addEventListener('popstate', handlePopState);

    console.log('onMounted: event listeners añadidos')
})

onBeforeUnmount(() => {
    // Limpiar event listeners
    window.removeEventListener('popstate', handlePopState)
    console.log('onBeforeUnmount: event listeners removidos')
})

watch(() => search.opened, (value) => {
    console.log('watch search.opened', value)
    usoTeclas = false
    if (value) {
        nav.sideBarShow = false // cerramos la sidebar
        currentUrl = page.url

        // Guardar la posición actual de scroll antes de abrir el modal
        savedScrollPosition = window.scrollY || window.pageYOffset || document.documentElement.scrollTop
        console.log('watch search.opened: scroll guardado en', savedScrollPosition)

        // Agregar estado al historial para capturar el botón "atrás"
        if (!modalHistoryState) {
            window.history.pushState({
                modalOpen: true,
                scrollY: savedScrollPosition
            }, '', window.location.href)
            modalHistoryState = true
            console.log('watch search.opened: estado de historial agregado')
        }

        nextTick(() => {
            console.log('watch search.opened: nextTick focus')
            if (search.autoFocus) {
                input.value.focus()
            }
        })
    } else {
        // Al cerrar el modal, limpiar el estado del historial si es necesario
        if (modalHistoryState) {
            modalHistoryState = false
            console.log('watch search.opened: modal cerrado, flag de historial limpiado')
        }
    }
})

function calcularUrl(item) {
    // Si es el "item" especial de ver todos
    if (item.isLinkAll) {
        // Usa la ruta traducida si existe
        const coleccion = item.rutaColeccion || item.coleccion
        let base = '/' + (rutasColeccion[coleccion] || coleccion)
        return `${base}?buscar=${encodeURIComponent(search.query || search.lastQuery || '')}`
    }
    return item.coleccion != 'paginas'
        ? (route(item.coleccion) + '/' + (item.slug_ref || item.id_ref))
        : '/' + item.slug_ref
}

// para buscar

const queryLoading = ref("")

// para guardar estadísticas de búsqueda
var currentUrl = page.url
var timerGuardarBusqueda = null
var busquedaId = null

function buscar() {
    if (search.searching) {
        search.searchWithDelay(buscar)
        return
    }
    if (search.query) {
        var currentQuery = search.query
        queryLoading.value = currentQuery
        search.searching = true
        search.includeDescription = false

        // Track search event to Google Analytics
        trackSearch(currentQuery, 'búsqueda_global');

        search.searchNow()
        .then(()=>{
            timerGuardarBusqueda = setTimeout(guardarBusqueda, SAVE_SEARCH_DELAY_MS)
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

// Función para manejar cambios en el input de búsqueda
function handleQueryInput(event) {
    const newQuery = event.target.value
    search.setQuery(newQuery)
}

// Función para limpiar la búsqueda
function clearQuery() {
    search.setQuery('')
}

// Función que maneja los cambios de query (reemplaza al watcher)
function handleQueryChange(newQuery, oldQuery) {
    busquedaId = null // borramos id de la busqueda actual
    clearTimeout(timerGuardarBusqueda) // borramos contador de tiempo para guardar los datos de la busqueda actual, seguramente el usuario está escribiendo aún
    if (newQuery) {
        search.searchWithDelay(buscar)
    } else {
        search.cancelTimer()
        search.results = null
        search.lastQuery = null
        search.restrictToCollections = null
    }
}
// Función para cerrar el modal y restaurar scroll si es necesario
function closeModal() {
    if (search.opened) {
        search.opened = false
        // Si se cierra manualmente y estamos en la misma página, restaurar scroll
        if (modalHistoryState && currentUrl === page.url) {
            nextTick(() => {
                setTimeout(() => {
                    window.scrollTo(0, savedScrollPosition)
                    console.log('closeModal: scroll restaurado a', savedScrollPosition)
                }, DOM_OPERATION_DELAY_MS)
            })
        }
    }
}

function clickPredefined(q) {
    search.setQuery(q.query)
    search.restrictToCollections = q.collections
}

function clickHandle(url, item) {
    console.log('clickHandle: inicio', { url, item, opened: search.opened })
    // Cierra el modal y fuerza el refresco del estado
    search.opened = false
    console.log('clickHandle: search.opened = false')

    // Si el modal se cerró manualmente, no necesitamos restaurar scroll ya que vamos a navegar
    setTimeout(() => {
        console.log('clickHandle: setTimeout ejecutado', { opened: search.opened })
        // Si es el "item" especial de ver todos, solo navega, no guarda búsqueda
        if (item && item.isLinkAll) {
            console.log('clickHandle: router.visit', url)
            router.visit(url)
            return
        }
        guardarBusqueda(url).then(() => {
            console.log('clickHandle: guardarBusqueda done')
            search.showSuggestions = true
        })
    }, DOM_OPERATION_DELAY_MS)
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

function onLinkClick(e, item) {
    // Siempre previene el comportamiento por defecto
    if (e && typeof e.preventDefault === 'function') e.preventDefault()
    clickHandle(calcularUrl(item), item)
}
</script>

<style scoped>
@reference "../../css/app.css";

a.seleccionado {
    @apply bg-primary text-white dark:text-black;
}

a.seleccionado :deep(em.search-term) {
    @apply text-white;
}

[data-theme='night'] a.seleccionado :deep(em.search-term),
.dark a.seleccionado :deep(em.search-term) {
    @apply text-black;
}


</style>
