<template>
    <div class="w-full relative py-12" id="myform" :style="{
        'background-size': 'cover',
        'background-attachment': 'fixed',
        background: 'black url(/storage/imagenes/space.jpg) repeat'
    }">
        <div class="card bg-base-100 shadow max-w-lg mx-auto p-7 relative">
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
            </div>
            <!-- Formulario empieza aquí -->
            <form v-else @submit.prevent="submit">
                <p>Rellena este formulario para poder ofrecerte un curso adaptado a tus necesidades.
                    <Link :href="route('cursos')">¿qué es el curso?</Link>
                </p>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="tipo">Tipo de experiencia:</label>
                    <div class="flex gap-3">
                        <label v-for="tipo of tipos" :key="tipo" class="inline-flex items-center">
                            <input type="radio" class="form-radio" name="categoria" :value="tipo" v-model="form.tipo"
                                :disabled="tipo.match(/grupal/i) && !page.props.auth?.user">
                            <span class="ml-2">{{ tipo }}</span>
                        </label>
                    </div>
                    <span v-if="form.errors.tipo" class="error">{{ form.errors.tipo }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="nombre">{{ form.categoria.match(/grupal/i) ?
                        'Nombre de la experiencia grupal' : 'Nombre simbólico' }}</label>
                    <input class="form-input w-full" id="nombre" type="text" v-model="form.nombre">
                    <span v-if="form.errors.nombre" class="error">{{ form.errors.nombre }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="fecha">Fecha de la experiencia:</label>
                    <input class="form-input w-full" id="fecha" type="text" v-model="form.fecha" placeholder="hoy">
                    <small>Deja el campo vacío si fue hoy</small>
                    <span v-if="form.errors.fecha" class="error">{{ form.errors.fecha }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="lugar">Lugar de la experiencia:</label>
                    <input class="form-input w-full" id="lugar" type="text" v-model="form.lugar" required>
                    <span v-if="form.errors.lugar" class="error">{{ form.errors.lugar }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="descripcion">Descripción de la experiencia:</label>
                    <textarea class="form-textarea w-full" id="descripcion" v-model="form.descripcion"></textarea>
                    <span v-if="form.errors.descripcion" class="error">{{ form.errors.descripcion }}</span>
                </div>
                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    Enviar
                </button>
            </form>
        </div>
    </div>
</template>




<script setup>

import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePage } from '@inertiajs/vue3';
defineOptions({ layout: AppLayout })

const page = usePage()

const submitted = ref(false)
const error = ref(false)

const tipos = [
    'Sueños',
    'Extrapolaciones',
    'Seiph',
    'Otros',
    'Experiencia de campo (Grupal)',
    'Rescate adimensional (Grupal)'
]

const form = useForm('inscripcion', {
    nombre: '',
    fecha: '',
    lugar: '',
    categoria: '',
    descripcion: '',
    user_id: page.props.auth?.user?.id,
    acepto: false
})

function scrollTop() {
    document.getElementById("myform").scrollIntoView(true)
}

function submit() {
    // Clear all errors...
    error.value = false
    form.clearErrors()
    form.post(route('experiencia.nueva'), {
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
