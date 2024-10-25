<template>
    <div>
        <Dropdown align="right" width="60">

            <template #trigger>
                <span class="btn btn-sm text-xl flex items-center">
                    +
                </span>
            </template>

            <template #content>
                <div class="select-none">
                    <a class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer"
                        :href="'/admin/informe/create?equipo_id=' + equipo.id" title="Crear informe">
                        <Icon icon="ph:plus-circle-duotone" />
                        <span>Crear informe</span>
                    </a>

                    <div class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer"
                        @click="modalSubirArchivos = true">
                        <Icon icon="ph:microsoft-word-logo-duotone" />
                        <span>Importar desde word</span>
                    </div>
                </div>



            </template>



        </Dropdown>



        <!-- Modal Upload -->
        <Modal :show="modalSubirArchivos" @close="modalSubirArchivos = false">

            <div class="p-5 flex flex-col gap-5 items-center">

                <div class="text-xl font-bold">Elige un archivo de Word (.doc o .docx)</div>

                <form method="post" action="/admin/informe/importar/crear" enctype="multipart/form-data"
                @submit.prevent="enviarDocumento">
                    <input type="hidden" name="_token" :value="csrf">
                    <input type="hidden" name="equipo_id" :value="equipo.id">
                    <input type="file" name="file" id="file" accept='.doc, .docx' @input="archivoDoc = $event.target.files[0]">

                    <div class="mt-4 flex gap-4 justify-end">

                        <button type="submit" class="btn btn-primary btn-sm" :disabled="importando">
                            <Spinner v-if="importando"/>
                            <span v-else>Importar</span>
                        </button>

                        <button @click.prevent="archivoDoc=null; modalSubirArchivos = false" class="btn btn-neutral btn-sm"
                        :disabled="importando">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>

        </Modal>

    </div>
</template>

<script setup>
const props = defineProps({
    equipo: Object,
})

const modalSubirArchivos = ref(false)
const page = usePage()
const archivoDoc = ref(null)
const importando = ref(false)
const csrf = ref(page?.props ? page.props.csrf_token : document.querySelector('meta[name="csrf-token"]').content)

function enviarDocumento() {
    if (!archivoDoc.value) return
    importando.value = true
    const formData = new FormData();
    formData.append('csrf_token', csrf.value);
    formData.append('equipo_id', props.equipo.id);
    formData.append('file', archivoDoc.value);
    axios.post('/admin/informe/importar/crear', formData)
                    .then(function(response) {
                        // Éxito en la importación, maneja la respuesta del servidor
                        console.log("import create response", response);
                        if (typeof response.data == 'string') {
                            alert("Hubo un error inesperado");
                        } else if (response.data.id)
                             window.location.href = '/admin/informe/' + response.data.id + '/edit'
                        else {
                            if (response.data.result)
                                alert(response.data.result)
                            if (response.data.redirect)
                                window.location.href = response.data.redirect
                        }
                    })
                    .catch(function(error) {
                        // Error en la importación, maneja el error
                        alert(error.response.data.error)
                        console.error(error);
                        importando.value = false
                    });
                }


</script>
