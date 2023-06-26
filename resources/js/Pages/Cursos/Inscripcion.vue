<template>
    <div class="w-full relative py-12" :style="{
        'background-size': 'cover',
        'background-attachment': 'fixed',
        background: 'black url(/storage/imagenes/space.jpg) repeat'
    }">
        <div class="card bg-base-100 shadow max-w-lg mx-auto p-7 relative">
            <h1>Inscripción al Curso Holístico Tseyor<small>&nbsp;(gratuito)</small></h1>
            <div v-if="submitted" class="space-y-7">
                <div class="alert alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Se han enviado los datos correctamente.</span>
                </div>

                <div class="alert">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
  <span>Revisa tu bandeja de correo.</span>
</div>

<div class="pt-7">
    <h2>¿Y ahora qué?</h2>
    <p>Puedes disfrutar de alguno de nuestros <Link :href="route('audios')">audios</Link> o leer alguno de los <Link :href="route('comunicados')">comunicados</Link> de nuestros guías estelares.</p>
    <p>También puedes escuchar nuestra <Link :href="route('radio')">Radio TSEYOR</Link>.</p>
</div>

            </div>
            <form v-else @submit.prevent="submit">
                <p>Rellena este formulario para poder ofrecerte un curso adaptado a tus necesidades.</p>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="nombre">Nombre y apellidos:</label>
                    <input class="form-input w-full" id="nombre" type="text" v-model="form.nombre" required>
                    <span v-if="form.errors.nombre" class="error">{{ form.errors.nombre }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="birthdate">Fecha de nacimiento:</label>
                    <div class="flex">
                        <select class="form-select w-1/3 mr-2" id="dia" v-model="form.dia" required>
                            <option disabled value="">Día</option>
                            <option v-for="day in days" :value="parseInt(day)">{{ day }}</option>
                        </select>
                        <select class="form-select w-1/3 mr-2" id="mes" v-model="form.mes" required>
                            <option disabled value="">Mes</option>
                            <option v-for="month in months" :value="month.value">{{ month.name }}</option>
                        </select>
                        <select class="form-select w-1/3" id="anyo" v-model="form.anyo" required>
                            <option disabled value="">Año</option>
                            <option v-for="year in years" :value="year">{{ year }}</option>
                        </select>
                    </div>
                    <span v-if="form.errors.dia" class="error">{{ form.errors.dia }}</span>
                    <span v-if="form.errors.mes" class="error">{{ form.errors.mes }}</span>
                    <span v-if="form.errors.anyo" class="error">{{ form.errors.anyo }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="ciudad">Ciudad de residencia:</label>
                    <input class="form-input w-full" id="ciudad" type="text" v-model="form.ciudad" required>
                    <span v-if="form.errors.ciudad" class="error">{{ form.errors.ciudad }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="region">Provincia o región:</label>
                    <input class="form-input w-full" id="region" type="text" v-model="form.region" required>
                    <span v-if="form.errors.region" class="error">{{ form.errors.region }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="pais">País:</label>
                    <input class="form-input w-full" id="pais" type="text" v-model="form.pais" required>
                    <span v-if="form.errors.pais" class="error">{{ form.errors.pais }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="email">Correo electrónico:</label>
                    <input class="form-input w-full" id="email" type="email" v-model="form.email" required>
                    <span v-if="form.errors.email" class="error">{{ form.errors.email }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="telefono">Teléfono/Whatsapp (opcional):</label>
                    <input class="form-input w-full" id="telefono" type="text" v-model="form.telefono">
                    <span v-if="form.errors.telefono" class="error">{{ form.errors.telefono }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="comentario">Otras formas de contacto (o comentario):</label>
                    <textarea class="form-textarea w-full" id="comentario" v-model="form.comentario"></textarea>
                    <span v-if="form.errors.comentario" class="error">{{ form.errors.comentario }}</span>
                </div>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox" v-model="form.acepto" required>
                        <span class="ml-2">Estoy de acuerdo y he leído la Política de Privacidad</span>
                    </label>
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
defineOptions({ layout: AppLayout })


const days = ref([
    '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31'
])
const months = ref([
    { name: 'Enero', value: 1 },
    { name: 'Febrero', value: 2 },
    { name: 'Marzo', value: 3 },
    { name: 'Abril', value: 4 },
    { name: 'Mayo', value: 5 },
    { name: 'Junio', value: 6 },
    { name: 'Julio', value: 7 },
    { name: 'Agosto', value: 8 },
    { name: 'Septiembre', value: 9 },
    { name: 'Octubre', value: 10 },
    { name: 'Noviembre', value: 11 },
    { name: 'Diciembre', value: 12 }
])
const years = ref([]);
const currentYear = new Date().getFullYear();
const minYear = currentYear - 90; // Edad mínima de 90 años

for (let year = currentYear - 18; year >= minYear; year--) {
    years.value.push(year.toString());
}

let submitted = ref(false)

const form = useForm('inscripcion', {
    nombre: '',
    dia: '',
    mes: '',
    anyo: '',
    ciudad: '',
    region: '',
    pais: '',
    email: '',
    telefono: '',
    comentario: '',
    acepto: false
})

function submit() {
    // Clear all errors...
    form.clearErrors()
    form.post(route('inscripcion.store'), {
        preserveScroll: true,
        onSuccess: () => {
            submitted.value = true
        }
    });
}
</script>
