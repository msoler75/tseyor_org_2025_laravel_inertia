<template>
    <div class="max-w-md lg:container mx-auto py-12">
        <form @submit.prevent="submit" class="">
            <div class="mb-4">
                <label class="block font-bold mb-2" for="nombre">Nombre del equipo:</label>
                <input class="form-input w-full" id="nombre" type="text" v-model="form.nombre" required>
                <span v-if="form.errors.nombre" class="error">{{ form.errors.nombre }}</span>
            </div>
            <div class="mb-4">
                <label class="block font-bold mb-2" for="nombre">Descripción:</label>
                <textarea class="form-input w-full" id="nombre" type="text" v-model="form.descripcion" />
                <span v-if="form.errors.descripcion" class="error">{{ form.errors.descripcion }}</span>
            </div>
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                Crear equipo
            </button>
        </form>
    </div>
</template>

<script setup>
import { useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

const form = useForm({
    nombre: '',
    descripcion: '',
    imagen: ''
})

function submit() {
    // Clear all errors...
    form.clearErrors()

    // Send the form data to your server-side endpoint for creating a team
    form.post(route('equipo.nuevo'), {
        preserveScroll: true,
        onSuccess: (response) => {
            console.log('respuesta', response)
            // Handle success, e.g., show a success message or redirect to a new page
        }
    });
}
</script>
