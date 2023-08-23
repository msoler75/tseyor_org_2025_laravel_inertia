<template>
    <input type="hidden" v-model="selectedOutput" :name="name">
    <v-select v-model="selected" :placeholder="placeholder" :multiple="!!multiple" label="name" :filterable="false"
        :options="optionsLabel" @search="onSearch"
        :selectable="(option) => !isSelected(option.value)"
        :closeOnSelect="!multiple"
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
    displayField: String,
    placeholder: { default: 'Buscar...' },
    options: { type: String, default: "[]" },
    value: {type: String, default: "[]"},
    multiple: { default: 0 },
    // url: { type: String, required: true },
});

console.log("1", props.value)
const selected = ref(JSON.parse(props.value?props.value:"[]"))
const selectedOutput = ref("")
function updateSelected() {
    console.log('updateSelected', selected.value)
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

updateSelected()
watch(selected, () => updateSelected())

const url = '/admin/search/' + props.model

const options = ref(props.options ? JSON.parse(props.options) : []);
const optionsLabel = computed(() => {
    return options.value
        .map(item => {
            return {
                value: item.id,
                label: props.displayField ? item[props.displayField] :
                    item.name || item.title || item.nombre || item.titulo || item.ruta,
                    name: item.id
            }
        })
})

function isSelected(value) {
    return Array.isArray(selected.value)?selected.value.find(item => item.value == value):selected?selected.id==value:false
}

async function onSearch(search, loading) {
    if (search.length) {
        loading(true);
        modelSearch(loading, search);
    }
}

onMounted(()=>{
    modelSearch(()=>{}, '')
})

async function modelSearch(loading, search) {
    console.log('modelSearch', loading, search)
    try {
        const response = await fetch(url + '?q=' + search);
        const json = await response.json();
        console.log({ json })
        options.value = json.results
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
