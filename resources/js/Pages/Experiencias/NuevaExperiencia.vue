<template>
    <FondoEstrellado class="w-full relative py-12" id="myform">

        <div class="container mx-auto flex justify-between items-center mb-20 text-white">
            <Back>Experiencias</Back>
            <AdminPanel modelo="experiencia" necesita="administrar experiencias" />
        </div>


        <div class="card bg-base-100 shadow max-w-lg mx-auto p-7 relative animate-fade-in">
            <h1>Comparte tus experiencias interdimensionales</h1>
            <div v-if="error">
                <div class="alert alert-error">
                    <Icon icon="ph:warning-circle-duotone" class="text-2xl" />
                    <span> {{ error }}</span>
                </div>
            </div>

            <div v-if="submitted" class="space-y-7">
                <div class="alert alert-success">
                    <Icon icon="ph:check-circle-bold" class="text-2xl" />
                    <span>Se han enviado los datos correctamente.</span>
                </div>
                <div>Puedes ver <Link :href="route('experiencias')" class="underline">otras experiencias</Link></div>
                <div>Puedes escuchar <Link :href="route('radio')" class="underline">Radio Tseyor</Link></div>
            </div>
            <!-- Formulario empieza aquí -->
            <form v-else @submit.prevent="submit">
                <div class="mb-4 bg-base-200 p-2 rounded">
                    <label class="block font-bold mb-2" for="categoria">Categoría de la experiencia:</label>
                    <div class="flex flex-wrap gap-3">
                        <label v-for="descripcion, categoria of categorias" :key="categoria" class="inline-flex items-center"
                        :class="categoria.match(/grupal/i) && !page.props.auth?.user?'opacity-50':''"
                        >
                            <input type="radio" class="form-radio" name="categoria" :value="categoria" v-model="form.categoria"
                                :disabled="categoria.match(/grupal/i) && !page.props.auth?.user" required>
                            <span class="ml-2" :title="descripcion">{{ categoria }}</span>
                        </label>
                    </div>
                    <span v-if="form.errors.categoria" class="error">{{ form.errors.categoria }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="nombre">{{ form.categoria.match(/grupal/i) ?
                        'Nombre de la experiencia grupal' : 'Nombre simbólico' }}</label>
                    <input class="form-input w-full" id="nombre" type="text" v-model="form.nombre" required>
                    <span v-if="form.errors.nombre" class="error">{{ form.errors.nombre }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="fecha">Fecha de la experiencia:</label>
                    <input class="form-input w-full" id="fecha" type="text" v-model="form.fecha" placeholder="hoy">
                    <span v-if="form.errors.fecha" class="error">{{ form.errors.fecha }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="lugar">Lugar de la experiencia:</label>
                    <input class="form-input w-full" id="lugar" type="text" v-model="form.lugar" required>
                    <span v-if="form.errors.lugar" class="error">{{ form.errors.lugar }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="texto">Descripción de la experiencia:</label>
                    <textarea class="form-textarea w-full" id="texto" v-model="form.texto" required></textarea>
                    <span v-if="form.errors.texto" class="error">{{ form.errors.texto }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="texto">Archivo adjunto (opcional):</label>
                    <input type="file" class="form-input w-full" id="texto" v-on:change="form.archivo = $event.target.files[0]">
                    <span v-if="form.errors.texto" class="error">{{ form.errors.texto }}</span>
                </div>
                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    Enviar
                </button>
            </form>
        </div>
    </FondoEstrellado>
</template>




<script setup>

import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePage } from '@inertiajs/vue3';
defineOptions({ layout: AppLayout })

const props = defineProps({
    categorias: Object
})

const page = usePage()

const submitted = ref(false)
const error = ref(false)

const form = useForm('inscripcion', {
    nombre: page.props.auth?.user?.name,
    fecha: '',
    lugar: '',
    categoria: '',
    texto: '',
    archivo: null,
    user_id: page.props.auth?.user?.id,
})

function scrollTop() {
    document.getElementById("myform").scrollIntoView(true)
}

function submit() {
    // Clear all errors...
    error.value = false
    form.clearErrors()
    form.post('/experiencia/store', {
        preserveScroll: true,
        onSuccess: () => {
            submitted.value = true
            scrollTop()
        },
        onError: (response) => {
            // console.log('onError', response)
            error.value = response[0]
            scrollTop()
        }
    });
}
</script>
