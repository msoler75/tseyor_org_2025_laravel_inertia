<template>
    <div class="w-full relative py-12"
    :style="{
        'background-size':'cover',
        'background-attachment': 'fixed',
        background:'black url(/storage/imagenes/space.jpg) repeat'}">
        <div class="card bg-base-100 shadow max-w-lg mx-auto p-7 relative">
            <h1>Inscripción al Curso Holístico Tseyor<small>&nbsp;(gratuito)</small></h1>
            <p>Rellena este formulario para poder ofrecerte un curso adaptado a tus necesidades.</p>
            <form @submit.prevent="submitForm" method="POST" action="{{ route('inscripcion.store') }}">
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="name">Nombre y apellidos:</label>
                    <input class="form-input w-full" id="name" type="text" v-model="form.name" required>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="birthdate">Fecha de nacimiento:</label>
                    <div class="flex">
                        <select class="form-select w-1/3 mr-2" id="day" v-model="form.day" required>
                            <option disabled value="">Día</option>
                            <option v-for="day in days" :value="day">{{ day }}</option>
                        </select>
                        <select class="form-select w-1/3 mr-2" id="month" v-model="form.month" required>
                            <option disabled value="">Mes</option>
                            <option v-for="month in months" :value="month">{{ month }}</option>
                        </select>
                        <select class="form-select w-1/3" id="year" v-model="form.year" required>
                            <option disabled value="">Año</option>
                            <option v-for="year in years" :value="year">{{ year }}</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="city">Ciudad de residencia:</label>
                    <input class="form-input w-full" id="city" type="text" v-model="form.city" required>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="region">Región y País:</label>
                    <input class="form-input w-full" id="region" type="text" v-model="form.region" required>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="email">Correo electrónico:</label>
                    <input class="form-input w-full" id="email" type="email" v-model="form.email" required>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="phone">Teléfono/Whatsapp (opcional):</label>
                    <input class="form-input w-full" id="phone" type="text" v-model="form.phone">
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="contact">Otras formas de contacto:</label>
                    <textarea class="form-textarea w-full" id="contact" v-model="form.contact"></textarea>
                </div>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox" v-model="form.agreement" required>
                        <span class="ml-2">Estoy de acuerdo y he leído la Política de Privacidad</span>
                    </label>
                </div>
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">
                    Enviar
                </button>
            </form>
        </div>
    </div>
</template>




<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'
import { Icon } from '@iconify/vue';
import { Link } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

const form = ref({
    name: '',
    day: '',
    month: '',
    year: '',
    city: '',
    region: '',
    email: '',
    phone: '',
    contact: '',
    agreement: false
})
const days = ref([
    '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31'
])
const months = ref([
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
])
const years = ref([]);
const currentYear = new Date().getFullYear();
const minYear = currentYear - 90; // Edad mínima de 90 años

for (let year = currentYear - 18; year >= minYear; year--) {
    years.value.push(year.toString());
}
</script>
