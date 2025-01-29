<template>
    <div
        class="h-full flex flex-col relative"
        :class="touchable ? 'touchable' : ''"
    >
    <h1 class="hidden" >{{ store.rutaActual }}</h1>
        <div
            class="w-full sticky border-b border-gray-300 shadow-sm bg-base-100 px-4 pb-0 sm:px-6 lg:px-8 z-30"
            :class="[embed ? 'pt-[2rem] top-0' : 'pt-[1rem] lg:pt-[4rem] top-[4rem]']"
        >
            <div
                ref="breadcrumb"
                :class="[
                    embed ? '' : 'lg:container mx-auto',
                    'relative xoverflow-x-auto mb-5',
                ]"
            >
                <Breadcrumb
                    :path="store.rutaActual"
                    :is-links="true"
                    :intercept-click="true"
                    @folder="store.clickBreadcrumb($event)"
                    title="Ruta actual"
                    class="fe-breadcrumb text-xl lg:text-2xl font-bold max-w-full"
                    :rootLabel="rootLabel"
                    :rootUrl="rootUrl"
                />
            </div>

            <div
                class="w-full flex flex-nowrap justify-between mb-4"
                :class="embed ? '' : 'lg:container mx-auto'"
            >
                <div
                    class="flex gap-x items-center w-full"
                    v-if="
                        !store.seleccionando &&
                        store.mostrandoResultadosBusqueda
                    "
                >
                    <span class="text-lg"
                        >Resultados de la búsqueda
                        <span class="font-bold">'{{ store.textoBuscar }}'</span
                        >:</span
                    >

                    <button
                        class="ml-auto btn btn-neutral btn-sm btn-icon"
                        @click.prevent="toggleVista"
                        title="Cambiar vista"
                    >
                        <Icon
                            v-show="selectors.archivosVista == 'lista'"
                            icon="ph:list-dashes-bold"
                            class="transform scale-150"
                        />
                        <Icon
                            v-show="selectors.archivosVista == 'grid'"
                            icon="ph:grid-nine-fill"
                            class="transform scale-150"
                        />
                    </button>

                    <button
                        class="btn btn-neutral btn-sm btn-icon !w-fit whitespace-nowrap"
                        title="Cerrar búsqueda"
                        @click="store.mostrandoResultadosBusqueda = false"
                    >
                        <Icon
                            icon="ph:magnifying-glass-duotone"
                            class="transform scale-150"
                        />
                        <Icon icon="ph:x-bold" />
                    </button>
                </div>

                <div
                    v-if="!store.mostrandoResultadosBusqueda"
                    class="flex gap-x w-full max-w-full"
                >
                    <BotonesIzquierda />

                    <div class="ml-auto flex gap-x">
                        <button
                            class="btn btn-neutral btn-sm"
                            @click.prevent="toggleVista"
                            title="Cambiar vista"
                        >
                            <Icon
                                v-show="selectors.archivosVista == 'lista'"
                                icon="ph:list-dashes-bold"
                                class="transform scale-150"
                            />
                            <Icon
                                v-show="selectors.archivosVista == 'grid'"
                                icon="ph:grid-nine-fill"
                                class="transform scale-150"
                            />
                        </button>

                        <button
                            v-if="store.seleccionando"
                            class="btn btn-neutral btn-sm btn-icon mr-auto"
                            @click.prevent="store.seleccionarTodos"
                            title="Seleccionar todos"
                        >
                            <Icon
                                icon="ph:selection-all-duotone"
                                class="transform scale-150"
                            />
                        </button>

                        <button
                            v-if="
                                !store.seleccionando &&
                                !store.mostrandoResultadosBusqueda
                            "
                            class="btn btn-neutral btn-sm btn-icon"
                            title="Buscar archivos"
                            @click="store.call('buscar')"
                        >
                            <Icon
                                icon="ph:magnifying-glass-duotone"
                                class="transform scale-150"
                            />
                        </button>

                        <Dropdown>
                            <template #trigger>
                                <span
                                    class="btn btn-neutral btn-sm btn-icon"
                                    title="Ordenar los elementos"
                                >
                                    <Icon
                                        icon="lucide:arrow-down-wide-narrow"
                                        class="text-2xl"
                                    />
                                </span>
                            </template>
                            <template #content>
                                <div class="select-none">
                                    <div
                                        v-for="(label, value) in ordenaciones"
                                        :key="value"
                                        class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer"
                                        @click="store.ordenarPor = value"
                                    >
                                        <div class="w-3">
                                            <Icon
                                                icon="ph:check"
                                                v-if="store.ordenarPor == value"
                                            />
                                        </div>
                                        {{ label }}
                                    </div>
                                </div>
                            </template>
                        </Dropdown>

                        <MenuFolder />
                    </div>
                </div>
            </div>
        </div>

        <div
            class="folder-content select-none flex-grow bg-base-100 py-4 pb-14 h-full px-2 sm:px-6 lg:px-8"
            :class="[
                contentClass,
                store.embed
                    ? 'h-[calc(100vh-265px-var(--bh))] max-h-[calc(100vh-265px-var(--bh))] min-h-[calc(100vh-265px-var(--bh))] overflow-y-auto'
                    : '',
            ]"
            :style="{ '--bh': breadcrumbHeight + 'px' }"
        >
            <div
                v-if="cargando"
                class="w-full h-full p-12 flex justify-center items-center text-4xl"
            >
                <Spinner />
            </div>
            <div
                v-else-if="
                    !store.mostrandoResultadosBusqueda && !itemsOrdenados.length
                "
                class="flex flex-col justify-center items-center gap-7 text-xl py-12 mb-14"
            >
                <Icon icon="ph:warning-diamond-duotone" class="text-4xl" />
                <div>No hay archivos</div>
            </div>
            <div
                v-else-if="
                    itemsMostrar.length && selectors.archivosVista === 'lista'
                "
                :class="[
                    itemsMostrar.length ? 'mr-2' : '',
                    store.embed ? 'px-0' : '',
                ]"
            >
                <ItemsTabla :items="itemsMostrar" />
            </div>
            <div
                v-else-if="
                    itemsMostrar.length && selectors.archivosVista === 'grid'
                "
            >
                <ItemsGrid :items="itemsMostrar" />
            </div>

            <BuscarArchivos />
        </div>

        <slot />

        <ModalRenombrar />

        <ModalEliminar />

        <ModalCrearCarpeta />

        <ModalSubirArchivos />

        <ModalPropiedades />

        <ModalPermisos />

        <ModalAcceso />
    </div>

    <!--  Barra de operaciones -->

    <div
        class="sticky bottom-0 left-0 right-0 bg-slate-200 dark:bg-slate-800 bg-opacity-95 p-2 flex gap-4 select-none overflow-x-auto scrollbar-hidden"
        v-if="
            store.itemsSeleccionados.length ||
            store.isMovingFiles ||
            store.isCopyingFiles
        "
        :class="
            store.seleccionando || store.isMovingFiles || store.isCopyingFiles
                ? 'justify-start sm:justify-center'
                : 'justify-end'
        "
    >
        <div
            class="h-12 font-bold flex items-center px-2 rounded-lg"
            v-if="store.buscandoCarpetaDestino"
        >
            Elige Destino
        </div>

        <BotonesOperaciones />
    </div>

    <ImagesViewer
        :show="showImagesViewer"
        @close="showImagesViewer = false"
        :images="store.images.map((x) => x + '?mw=2000&mh=2000')"
        :index="imageIndex"
        :showFilename="true"
    />
</template>

<script setup>
import { usePage, Link } from "@inertiajs/vue3";
import useFolderExplorerStore from "@/Stores/folderExplorer";
import useSelectors from "@/Stores/selectors";
import usePlayer from "@/Stores/player";
import useUserStore from "@/Stores/user";

/*
const vDisableRightClick = {
    mounted(el) {
        if (store.esPantallaTactil())
            el.addEventListener('contextmenu', (e) => e.preventDefault())
    },
    unmounted(el) {
        if (store.esPantallaTactil())
            el.removeEventListener('contextmenu', (e) => e.preventDefault())
    }
}*/

const TIEMPO_ACTIVACION_SELECCION = 1100;
const TIEMPO_SELECCION_SIMPLE = 250;

const userStore = useUserStore();

const props = defineProps({
    ruta: { type: String, required: true },
    rutaBase: { type: String, required: false, default: "" },
    items: Array,
    propietarioRef: Object,
    cargando: Boolean,
    embed: { type: Boolean, default: false },
    contentClass: String,
    rootLabel: { type: String, require: false },
    rootUrl: { type: String, require: false },
    mostrarRutas: { type: Boolean, default: false },
    mostrarMisArchivos: { type: [Boolean, String], default: true },
    modoInsertar: { type: Boolean, default: false },
});

const emit = defineEmits([
    "updated",
    "disk",
    "folder",
    "file",
    "insert",
    "images",
]);

const store = useFolderExplorerStore();

store.ruta = props.ruta;
store.rutaBase = props.rutaBase;
store.modoInsertar = props.modoInsertar;
store.items = props.items;
store.embed = props.embed;
store.esAdministrador = computed(
    () => !!userStore.permisos.filter((p) => p == "administrar archivos").length
);
store.infoCargada = false;
store.propietarioRef = props.propietarioRef;
store.mostrandoResultadosBusqueda = false;
store.navegando = false;
store.mostrarRutas = props.mostrarRutas;
store.on("update", () => {
    emit("updated");
});
store.on("clickItem", onClickItem);
store.on("clickDisk", onClickDisk);
store.on("clickFolder", onClickFolder);
store.on("clickFile", onClickFile);
store.on("clickBreadcrumb", onClickBreadcrumb);
store.on("touchStart", onTouchStart);
store.on("touchEnd", onTouchEnd);
store.on("touchMove", onTouchMove);
store.on("insertar", onInsertar);

// para scroll
const breadcrumb = ref(null);

// CLICK

const player = usePlayer();

function onClickItem(item, event) {
    console.log("clickItem", item, "store.seleccionando:", store.seleccionando);
    /*if (!event.target.closest('[data-allow-touch]')) {
        event.preventDefault();
    }*/
    if (store.seleccionando) {
        item.seleccionado = !item.seleccionado;
        event.preventDefault();
        return;
    }
    if (item.tipo == "disco") store.clickDisk(item, event);
    else if (item.tipo == "carpeta") store.clickFolder(item, event);
    else store.clickFile(item, event);
}

function onClickDisk(item, event) {
    console.log("clickDisk", item);
    emit("disk", item);
    event.preventDefault();
}

function onClickFolder(item, event) {
    console.log(
        "clickFolder",
        item,
        "store.seleccionando:",
        store.seleccionando
    );
    if (store.seleccionando) {
        item.seleccionado = !item.seleccionado;
        event.preventDefault();
    } else {
        console.log("navegando=", item.url);
        store.navegando = item.url;
        if (store.embed) {
            emit("folder", item);
            event.preventDefault();
        }
    }
}

const showImagesViewer = ref(false);
const imageIndex = ref(0);

async function onClickFile(item, event) {
    console.log("clickFile", item, "store.seleccionando:", store.seleccionando);
    if (store.seleccionando) {
        item.seleccionado = !item.seleccionado;
        event.preventDefault();
    } else if (store.embed) {
        emit("file", item);
        event.preventDefault();
    } else if (store.isImage(item.url)) {
        event.preventDefault();
        imageIndex.value = store.images.findIndex((x) => x == item.url);
        showImagesViewer.value = true;
        /* if (store.v3ImgPreviewFn) {
             console.log("VISOR", item.url, index);
             store.v3ImgPreviewFn({
                 images: store.images.map((x) => x + "?mw=3000&mh=3000"),
                 index,
             });
         } else store.mostrandoImagen = item;
          */
    } else if (player.isPlayable(item.url)) {
        player.play(item.url, item.nombre);
        event.preventDefault();
    }
}

function onClickBreadcrumb(item, event) {
    console.log("clickBreadcrumb", { item });
    if (store.seleccionando) {
        return;
    }
    store.navegando = item.url;
    if (store.embed) emit("folder", { ...item, ruta: item.url });
    else {
        store.clickFolder(item, event);
        if (!store.embed) {
            console.log("visita1", item.url);
            router.visit(item.url);
        }
    }
}

// embed
const breadcrumbHeight = ref(0);

function calcBreadcrumbHeight() {
    const elem = document.querySelector(".fe-breadcrumb");
    if (elem) breadcrumbHeight.value = elem.offsetHeight;
}

watch(
    () => store.rutaActual,
    () => nextTick(() => calcBreadcrumbHeight())
);

window.addEventListener("resize", calcBreadcrumbHeight);

function onInsertar() {
    console.log("insertarImagenes", store.imagenesSeleccionadas);
    emit("images", store.imagenesSeleccionadas);
}

// información más detallada de cada archivo (se carga después de mostrar el contenido de la carpeta)
// const infoCargada = ref(false)
const info_archivos = ref({});

// Carga la información adicional de los contenidos de la carpeta actual
async function cargarInfo() {
    store.infoCargada = false;

    return axios
        .get("/archivos_info" + "?ruta=/" + store.rutaActual)
        .then((response) => {
            // info.value = response.data
            info_archivos.value = response.data;
            store.infoCargada = true;
        });
}

// IMAGES PREVIEW

/*async function cargarVisorImagenes() {
    // importación dinámica:
    await import('@/resources/js/Components/ImagesViewer.vue').then((module) => {
        store.v3ImgPreviewFn = module.v3ImgPreviewFn;
    })
}*/

function actualizarListaImagenes() {
    store.images = props.items
        .filter((x) => store.isImage(x.url))
        .map((x) => x.url);
}

// genera la lista de items final
function calcularItems() {
    // para evitar modificación de la prop
    // https://github.com/inertiajs/inertia/issues/854#issuecomment-896089483
    const items = JSON.parse(JSON.stringify(props.items));

    const fields = [
        "nodo_id",
        "puedeEscribir",
        "puedeLeer",
        "permisos",
        "propietario",
        "privada",
        "acl",
        "archivos",
        "subcarpetas",
    ];
    console.log("info", info_archivos.value);
    for (const item of items) {
        const inf = info_archivos.value[item.nombre];
        if (inf) {
            for (const field of fields) item[field] = inf[field];
        }
    }
    console.log({ items });

    store.itemsShow = items.filter(x => !x.oculto);

    actualizarListaImagenes();
}

calcularItems();

watch(
    () => props.items,
    () => {
        console.log("watch props.items");
        store.navegando = "";
        store.items = props.items;
        calcularItems();
        // itemsShow.value = JSON.parse(JSON.stringify(props.items))
        cargarInfo();
    }
);

watch(info_archivos, () => {
    console.log("watch info_archivos");
    calcularItems();
});

const touchable = ref(true);

onMounted(() => {
    touchable.value = store.esPantallaTactil();

    cargarInfo(); // .then(() => cargarVisorImagenes())

    document.addEventListener("keydown", onKeyDown);

    calcBreadcrumbHeight();

    // posicionamos el scroll horizontal de breadcrumb al final, instantaneamente, sin SetTimeout
    if (breadcrumb.value)
        breadcrumb.value.scrollTo(breadcrumb.value.scrollWidth, 0);
});

onUnmounted(() => {
    document.removeEventListener("keydown", onKeyDown);
});

function onKeyDown(event) {
    //Si es Ctrl+I
    if (event.ctrlKey && event.key === "i")
        selectors.mostrarPermisos = !selectors.mostrarPermisos;
}

// otros datos
const page = usePage();
const user = computed(() => page?.props?.auth?.user);

// ITEMS A MOSTRAR

const itemsMostrar = computed(() =>
    store.mostrandoResultadosBusqueda
        ? store.resultadosBusqueda
        : itemsOrdenados.value
);

// EVENTOS TOUCH

const nav = useNav();
// posicion inicial del scroll al comienzo del touch
var scrollPosAtStart = -1;
// registra el movimiento de touch
var touchPosAtStart = 0;
var lastYTouch = 0;
function onTouchMove(event) {
    lastYTouch = event.changedTouches[0].clientY; // get touch y on screen
    // console.log('touchmove - lastYTouch:', lastYTouch)
}

function onTouchStart(item, event) {
    scrollPosAtStart = nav.scrollY;
    item.touching = true;
    // get touch y
    lastYTouch = event.changedTouches[0].clientY;
    touchPosAtStart = lastYTouch;
    console.log("touchstart", "touchPosAtStart:", touchPosAtStart);
    console.log("store.seleccionando", store.seleccionando);
    item.touchStartAt = new Date().getTime();
    if (store.seleccionando) {
        console.log("modo Seleccionando ON");
        /*item.shortTouchTimer = setTimeout(() => {
            if (scrollPosAtStart != nav.scrollY) return
            item.seleccionado = !item.seleccionado
            console.log('item.seleccionado =', item.seleccionado)
        }, TIEMPO_SELECCION_SIMPLE);*/
        event.preventDefault();
    } else if (["carpeta", "archivo"].includes(item.tipo)) {
        item.longTouchTimer = setTimeout(() => {
            if (!item.touching) return;
            if (!isInPlace()) {
                item.touching = false;
                return;
            }

            if (event.target.closest("[menu]")) {
                console.log("ES MENU");
                item.touching = false;
                return;
            }

            item.seleccionado = true;
            item.touching = false;
            store.seleccionando = true;
            console.log("TIMEOUT: store.seleccionando", store.seleccionando);
            console.log("item.seleccionado2 =", item.seleccionado);
        }, TIEMPO_ACTIVACION_SELECCION); // tiempo en milisegundos para considerar un "long touch"
    }
}

// devuelve true si el usuario no ha movido el touch
function isInPlace() {
    const y = lastYTouch; // get touch y on screen
    const ty = touchPosAtStart - y; // touch Y diff
    const sy = scrollPosAtStart - nav.scrollY; // scroll diff
    const dy = Math.abs(sy) + Math.abs(ty);
    const r = dy <= 7;
    console.log("InPlace?", r, { ty, sy, dy });
    return r;
}

function onTouchEnd(item, event) {
    lastYTouch = event.changedTouches[0].clientY;
    console.log(
        "touchend",
        { item, target: event.target },
        "lastYTouch:",
        lastYTouch
    );
    console.log("store.seleccionando", store.seleccionando);
    clearTimeout(item.longTouchTimer);
    // clearTimeout(item.shortTouchTimer);
    // item.touching = false
    if (event.target.closest("[menu]")) {
        console.log("ES MENU");
        item.touching = false;
        event.target.closest(".cursor-pointer").click();
        return;
    }

    const ellapsed = new Date().getTime() - item.touchStartAt;
    if (!isInPlace()) {
        // si nos hemos movido, no hacemos nada (está haciendo scroll)
        item.touching = false;
        return;
    }
    if (store.seleccionando) {
        console.log("modo seleccionando ONNN");
        if (item.touching) item.seleccionado = !item.seleccionado;
        console.log("item.seleccionado =", item.seleccionado);
        item.touching = false;
    } else if (ellapsed < TIEMPO_ACTIVACION_SELECCION) {
        item.touching = false;
        // SIMPLE CLICK
        console.log("simulating single click");
        if (item.tipo == "carpeta" || item.tipo == "disco") {
            store.clickFolder(item, event);
            if (!props.embed) {
                console.log("visita2", item.url);
                router.visit(item.url);
            }
        } else {
            const target = event.target;
            console.log("target", target);
            if (target.hasAttribute("download")) target.click();
            else store.clickFile(item, event);
        }
    }
}

// ORDENACION

const ordenaciones = {
    normal: "Normal",
    fechaDesc: "Recientes",
    fechaAsc: "Antiguos",
    nombreAsc: "A-Z",
    nombreDesc: "Z-A",
    tamañoAsc: "Pequeños",
    tamañoDesc: "Grandes",
};

const itemsOrdenados = computed(() => {
    // Separar las carpetas y los archivos en dos grupos
    var items = store.itemsShow.filter(
        (item) => !item.padre && !item.actual && !item.eliminado
    );

    // Mostramos "mis archivos" ?
    if (!props.mostrarMisArchivos)
        items = items.filter(
            (item) => item.tipo != "disco" || item.nombre != "mis_archivos"
        );

    // carpetas y archivos
    const carpetas = items.filter((item) => item.tipo === "carpeta");
    const archivos = items.filter((item) => item.tipo !== "carpeta");

    switch (store.ordenarPor) {
        case "normal":
            // Ordenar los archivos por fecha de modificación descendente
            carpetas.sort(
                (a, b) => b.nombre.toUpperCase() - a.nombre.toUpperCase()
            );
            archivos.sort(
                (a, b) => b.fecha_modificacion - a.fecha_modificacion
            );
            return [...carpetas, ...archivos];

        case "fechaAsc":
            // Ordenar los archivos por fecha de modificación ascendente
            carpetas.sort(
                (a, b) => a.fecha_modificacion - b.fecha_modificacion
            );
            archivos.sort(
                (a, b) => a.fecha_modificacion - b.fecha_modificacion
            );
            return [...carpetas, ...archivos];

        case "fechaDesc":
            // Ordenar los archivos por fecha de modificación descendente
            carpetas.sort(
                (a, b) => b.fecha_modificacion - a.fecha_modificacion
            );
            archivos.sort(
                (a, b) => b.fecha_modificacion - a.fecha_modificacion
            );
            return [...archivos, ...carpetas];

        case "nombreAsc":
            // Ordenar todos los elementos por nombre ascendente
            return items.sort((a, b) => a.nombre.localeCompare(b.nombre));

        case "nombreDesc":
            // Ordenar todos los elementos por nombre descendente
            return items.sort((a, b) => b.nombre.localeCompare(a.nombre));

        case "tamañoAsc":
            // Ordenar los archivos por tamaño ascendente
            archivos.sort((a, b) => a.tamano - b.tamano);
            return [...archivos, ...carpetas];

        case "tamañoDesc":
            // Ordenar los archivos por tamaño descendente
            archivos.sort((a, b) => b.tamano - a.tamano);
            return [...archivos, ...carpetas];

        default:
            // Si el criterio de ordenación no coincide, devolver el listado sin cambios
            return items;
    }
});

/* function crearCarpetaKeydown(event) {
    if (event.keyCode === 27) {
        modalCrearCarpeta.value = false;
        // La tecla ESC fue presionada
    }
} */

// VISTA DE ITEMS
const selectors = useSelectors();
if (!["lista", "grid"].includes(selectors.archivosVista))
    selectors.archivosVista = "lista";

// obtenemos parámetro de URL "vista"
if ("URLSearchParams" in window) {
    const urlParams = new URLSearchParams(window.location.search);
    const vista = urlParams.get("vista");
    if (vista && ["lista", "grid"].includes(vista))
        selectors.archivosVista = vista;
}

console.log(selectors.value);
const toggleVista = () => {
    selectors.archivosVista =
        selectors.archivosVista === "grid" ? "lista" : "grid";
    console.log(selectors.value);
};

// copia datos de una fuente (de tipo objeto o de tipo array) a un destino, concretamente la clave k
// de forma que mantiene la reactividad
function copyData(dest, src, key) {
    // primera llamada
    if (!key) {
        for (const key in src) copyData(dest, src, key);
        return;
    }
    // si es un objeto, copiamos con recursividad
    if (typeof src[key] === "object") {
        console.log("copiando", key);
        if (!dest[key]) dest[key] = {};
        for (const subkey in src[key]) copyData(dest[key], src[key], subkey);
        return;
    }

    if (src[key]) dest[key] = src[key];
}
</script>

<style scoped>
@reference "../../../css/app.css";

.btn-icon {
    @apply w-[40px] sm:w-[46px];
}

.touchable .hide-if-touchable {
    @apply opacity-0 pointer-events-none;
}

.fondo-transparencia {
    background-image: linear-gradient(45deg, #ccc 25%, transparent 25%),
        linear-gradient(-45deg, #ccc 25%, transparent 25%),
        linear-gradient(45deg, transparent 75%, #ccc 75%),
        linear-gradient(-45deg, transparent 75%, #ccc 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
}

table td,
table th {
    @apply px-2;
}

.lista {
    display: block;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
}

@media (min-width: 768px) {
    .grid {
        gap: 2rem;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }
}

@media (min-width: 1024px) {
    .grid {
        gap: 2rem;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
}
</style>

<style>
.gap-x {
    @apply gap-1 xs:gap-2 sm:gap-3;
}

/* base */
.file {
    backface-visibility: hidden;
    z-index: 1;
}

/* moving */
.animating .files-move {
    transition: all 600ms ease-in-out 50ms;
}

/* appearing */
.animating .files-enter-active {
    transition: all 400ms ease-out;
    transform: scale(0);
}

/* disappearing */
.animating .files-leave-active {
    transition: all 200ms ease-in;
    position: absolute;
    z-index: 0;
    transform: scale(0);
}

/* appear at / disappear to */
.animating .files-enter-to {
    opacity: 1;
    transform: scale(1);
}

.animating .files-leave-to {
    opacity: 0;
    transform: scale(0);
}
</style>
