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

const selected = ref(JSON.parse(props.value?props.value:"[]"))
const selectedOutput = ref("")
function updateSelected() {
    if (typeof selected.value === 'string' || typeof selected.value === 'number') {
        selectedOutput.value = selected.value
    } else if(Array.isArray(selected.value)) {
        selectedOutput.value = JSON.stringify(selected.value)
    } else if(typeof selected.value=='object') {
        if(selected.value ===null)
        selectedOutput.value = "[]"
        else
        selectedOutput.value = JSON.stringify([selected.value])
    }
}

const optionsArray = ref(props.options ? JSON.parse(props.options) : selected.value);
const optionsLabel = computed(() => {
    return optionsArray.value
        .map(item => {
            // si ya tenemos los valores formateados, devolvemos el item
            if(('label' in item) && ('value' in item)) return item
            return {
                value: item.id,
                label: props.labelOption ? item[props.labelOption] :
                    item.name || item.title || item.nombre || item.titulo || item.ruta
            }
        })
})

updateSelected()
watch(selected, () => updateSelected())


function isSelected(value) {
    return Array.isArray(selected.value)?selected.value.find(item => item.value == value):selected?selected.value==value:false
}

var debounce = null

async function onSearch(search, loading) {
    if (search.length) {
        loading(true);
        clearTimeout(debounce)
        debounce = setTimeout(()=>modelSearch(loading, search), 800);
    }
}

onMounted(()=>{
    if(!optionsArray.value.length)
     modelSearch(()=>{}, '')
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

<style scoped>
table, td {
    border: 1px solid gray;
}</style>
