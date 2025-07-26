<template>
    <input type="hidden" v-model="selectedOutput" :name="name">
    <v-select v-model="selected" :placeholder="placeholder" :multiple="!!multiple" label="label" :filterable="false"
        :options="optionsLabel" @search="onSearch"
        :deselectFromDropdown="!!multiple"
        :closeOnSelect="!multiple"
        transition=""
        >
        <template #no-options>
            No hay resultados
        </template>
        <template v-slot:option="option">
            <div class="d-center flex gap-2">
                <span v-if="isSelected(option.value)" class="w-4">✔️</span>
                <span v-else class="w-4"></span>
                {{ option.label }}
            </div>
        </template>
        <template v-slot:selected-option="option">
            <div class="selected d-center">
                {{ option.label }}
            </div>
        </template>
    </v-select>
</template>

<script setup>
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';

const props = defineProps({
    name: String,
    model: String,
    labelOption: String, // para la obtención del valor displayable de la colección
    placeholder: { default: 'Buscar...' },
    options: { type: String, default: null },
    value: {type: String, default: "[]"},
    multiple: { default: 0 },
    // url: { type: String, required: true },
});

const url = '/admin/search/' + props.model

// Inicialización diferente para múltiple vs único
let initialValue
if (props.multiple) {
    initialValue = JSON.parse(props.value || "[]")
} else {
    if (props.value && props.value !== "[]" && props.value !== "") {
        // Si el valor es solo un número (ID), crear un objeto temporal
        const parsedValue = isNaN(props.value) ? JSON.parse(props.value) : Number(props.value)
        if (typeof parsedValue === 'number') {
            initialValue = {
                value: parsedValue,
                label: `Usuario ID: ${parsedValue}`
            }
        } else {
            initialValue = parsedValue
        }
    } else {
        initialValue = null
    }
}

const selected = ref(initialValue)
const selectedOutput = ref("")
function updateSelected() {
    if (typeof selected.value === 'string' || typeof selected.value === 'number') {
        selectedOutput.value = selected.value
    } else if(Array.isArray(selected.value)) {
        selectedOutput.value = JSON.stringify(selected.value)
    } else if(typeof selected.value=='object') {
        if(selected.value === null) {
            selectedOutput.value = props.multiple ? "[]" : ""
        } else {
            // Si no es múltiple, enviar solo el value del objeto seleccionado
            if (!props.multiple && selected.value.value) {
                selectedOutput.value = selected.value.value
            } else {
                selectedOutput.value = JSON.stringify([selected.value])
            }
        }
    }
}

const optionsArray = ref(props.options ?
    JSON.parse(props.options) :
    (props.multiple ?
        (Array.isArray(selected.value) ? selected.value : []) :
        (selected.value ? [selected.value] : [])
    )
);
const optionsLabel = computed(() => {
    return optionsArray.value
        .map(item => {
            // Si es un número (ID), convertirlo a objeto básico
            if (typeof item === 'number' || typeof item === 'string') {
                return {
                    value: item,
                    label: `Usuario ID: ${item}` // Etiqueta temporal, se actualizará con búsqueda
                }
            }

            // si ya tenemos los valores formateados, devolvemos el item
            if(item && typeof item === 'object' && ('label' in item) && ('value' in item)) return item

            // Si es un objeto modelo completo, formatearlo
            if(item && typeof item === 'object') {
                return {
                    value: item.id,
                    label: props.labelOption ? item[props.labelOption] :
                        item.name || item.title || item.nombre || item.titulo || item.ruta || `ID: ${item.id}`
                }
            }

            // Fallback para casos inesperados
            return {
                value: item,
                label: String(item)
            }
        })
})

updateSelected()
watch(selected, () => updateSelected())


function isSelected(value) {
    if (props.multiple) {
        return Array.isArray(selected.value) ?
            selected.value.find(item => item.value == value) :
            false
    } else {
        return selected.value && selected.value.value == value
    }
}

var debounce = null

async function onSearch(search, loading) {
    if (search.length) {
        loading(true);
        clearTimeout(debounce)
        debounce = setTimeout(()=>modelSearch(loading, search), 800);
    }
}

onMounted(async ()=>{
    // Si hay un valor seleccionado que es solo un ID, buscar los datos del usuario específico
    if(selected.value && selected.value.value && selected.value.label && selected.value.label.startsWith('Usuario ID:')) {
        try {
            // Buscar el usuario específico por ID
            const response = await fetch(url + '?q=' + selected.value.value);
            const json = await response.json();
            if (json.results && json.results.length > 0) {
                // Encontrar el usuario específico en los resultados
                const foundUser = json.results.find(user => user.id == selected.value.value);
                if (foundUser) {
                    // Actualizar el valor seleccionado con el nombre real
                    selected.value = {
                        value: foundUser.id,
                        label: foundUser.name || foundUser.title || foundUser.nombre || foundUser.titulo || `ID: ${foundUser.id}`
                    };
                    // Reemplazar las opciones con solo el usuario encontrado
                    optionsArray.value = [foundUser];
                }
            }
        } catch (error) {
            console.error('Error buscando usuario:', error);
        }
    }
    // Si no hay opciones en absoluto, cargar algunos usuarios por defecto
    else if(!optionsArray.value.length) {
        modelSearch(()=>{}, '')
    }
})

async function modelSearch(loading, search) {
    console.log('modelSearch', loading, search)
    try {
        const response = await fetch(url + '?q=' + search);
        const json = await response.json();
        console.log({ json })
        optionsArray.value = json.results
        loading(false);
    } catch (error) {
        console.error(error);
        loading(false);
    }
}
</script>

<style>
.vs__selected {
    color: unset;
}
</style>

<style scoped>
table, td {
    border: 1px solid gray;
}</style>
