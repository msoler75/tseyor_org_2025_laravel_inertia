<template>
    <FondoEstrellado class="w-full h-full relative py-12" id="myform">
        <div
            class="card bg-base-100 shadow-2xs max-w-lg mx-auto p-7 relative animate-fade-in"
        >
            <h1>
                Inscripción al Curso Holístico Tseyor<small
                    >&nbsp;(gratuito)</small
                >
            </h1>
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

                <div class="alert">
                    <Icon icon="ph:info-bold" class="text-2xl text-info" />
                    <span>Revisa tu bandeja de correo.</span>
                </div>
            </div>

            <!-- Formulario empieza aquí -->
            <form v-else @submit.prevent="submit">
                <p>
                    Rellena este formulario para poder ofrecerte un curso
                    adaptado a tus necesidades.
                    <Link :href="route('cursos')">¿qué es el curso?</Link>
                </p>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="nombre"
                        >Nombre y apellidos:</label
                    >
                    <input
                        class="form-input w-full"
                        id="nombre"
                        type="text"
                        v-model="form.nombre"
                        required
                        data-clarity-mask="true"
                    />
                    <span v-if="form.errors.nombre" class="error">{{
                        form.errors.nombre
                    }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="birthdate">
                        <span v-if="preguntarFechaCompleta">
                            Fecha completa de nacimiento:
                        </span>
                        <span v-else> Año de nacimiento: </span>
                    </label>
                    <div class="flex">
                        <select
                            v-if="preguntarFechaCompleta"
                            class="form-select w-1/3 mr-2"
                            id="dia"
                            v-model="form.dia"
                            data-clarity-mask="true"
                        >
                            <option disabled value="">Día</option>
                            <option v-for="day in days" :value="parseInt(day)">
                                {{ day }}
                            </option>
                        </select>
                        <select
                            v-if="preguntarFechaCompleta"
                            class="form-select w-1/3 mr-2"
                            id="mes"
                            v-model="form.mes"
                            required
                            data-clarity-mask="true"
                        >
                            <option disabled value="">Mes</option>
                            <option
                                v-for="month in months"
                                :value="month.value"
                            >
                                {{ month.name }}
                            </option>
                        </select>
                        <select
                            class="form-select w-1/3"
                            id="anyo"
                            v-model="form.anyo"
                            required
                            data-clarity-mask="true"
                        >
                            <option disabled value="">Año</option>
                            <option v-for="year in years" :value="year">
                                {{ year }}
                            </option>
                        </select>
                    </div>
                    <span v-if="form.errors.dia" class="error">{{
                        form.errors.dia
                    }}</span>
                    <span v-if="form.errors.mes" class="error">{{
                        form.errors.mes
                    }}</span>
                    <span v-if="form.errors.anyo" class="error">{{
                        form.errors.anyo
                    }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="ciudad"
                        >Ciudad de residencia:</label
                    >
                    <input
                        class="form-input w-full"
                        id="ciudad"
                        type="text"
                        v-model="form.ciudad"
                        required
                        data-clarity-mask="true"
                    />
                    <span v-if="form.errors.ciudad" class="error">{{
                        form.errors.ciudad
                    }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="region"
                        >Provincia o región:</label
                    >
                    <input
                        class="form-input w-full"
                        id="region"
                        type="text"
                        v-model="form.region"
                        required
                    />
                    <span v-if="form.errors.region" class="error">{{
                        form.errors.region
                    }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="pais">País:</label>
                    <input
                        class="form-input w-full"
                        id="pais"
                        type="text"
                        v-model="form.pais"
                        required
                    />
                    <span v-if="form.errors.pais" class="error">{{
                        form.errors.pais
                    }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="email"
                        >Correo electrónico:</label
                    >
                    <input
                        class="form-input w-full"
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                    />
                    <span v-if="form.errors.email" class="error">{{
                        form.errors.email
                    }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="telefono"
                        >Teléfono/Whatsapp:</label
                    >
                    <input
                        class="form-input w-full"
                        id="telefono"
                        type="text"
                        v-model="form.telefono"
                        required
                    />
                    <span v-if="form.errors.telefono" class="error">{{
                        form.errors.telefono
                    }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="comentario"
                        >Otras formas de contacto o comentario:</label
                    >
                    <textarea
                        class="form-textarea w-full"
                        id="comentario"
                        v-model="form.comentario"
                    ></textarea>
                    <span v-if="form.errors.comentario" class="error">{{
                        form.errors.comentario
                    }}</span>
                </div>
                <div class="mb-4 inline-flex flex-col gap-2 ">
                    <AceptaCondiciones v-model="form.acepto"
                    finalidad="gestionar mi inscripción al curso y mantener las comunicaciones necesarias relacionadas con el mismo"/>
                </div>
                <button
                    type="submit"
                    class="btn btn-primary"
                    :disabled="form.processing"
                >
                    Enviar
                </button>
            </form>
        </div>

        <Section v-show="submitted" class="py-7">
            <TextImage
                title="¿Y ahora qué?"
                srcImage="/almacen/medios/paginas/camino-infinito.jpg"
                href="/mis-primeros-pasos"
                buttonLabel="Mis primeros pasos"
                class="container"
                textClass="bg-base-100 p-5 rounded-2xl"
            >
                Te recomendamos la lectura de esta sección
            </TextImage>
        </Section>
    </FondoEstrellado>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";

const days = ref([
    "01",
    "02",
    "03",
    "04",
    "05",
    "06",
    "07",
    "08",
    "09",
    "10",
    "11",
    "12",
    "13",
    "14",
    "15",
    "16",
    "17",
    "18",
    "19",
    "20",
    "21",
    "22",
    "23",
    "24",
    "25",
    "26",
    "27",
    "28",
    "29",
    "30",
    "31",
]);
const months = ref([
    { name: "Enero", value: 1 },
    { name: "Febrero", value: 2 },
    { name: "Marzo", value: 3 },
    { name: "Abril", value: 4 },
    { name: "Mayo", value: 5 },
    { name: "Junio", value: 6 },
    { name: "Julio", value: 7 },
    { name: "Agosto", value: 8 },
    { name: "Septiembre", value: 9 },
    { name: "Octubre", value: 10 },
    { name: "Noviembre", value: 11 },
    { name: "Diciembre", value: 12 },
]);
const years = ref([]);
const currentYear = new Date().getFullYear();
const minYear = currentYear - 90; // Edad mínima de 90 años

for (let year = currentYear - 15; year >= minYear; year--) {
    years.value.push(year.toString());
}

const esMenor = computed(() => parseInt(form.anyo) + 18 >= currentYear + 1);
const posibleMenor = computed(() => parseInt(form.anyo) + 18 >= currentYear);
const preguntarFechaCompleta = computed(() => posibleMenor.value && !esMenor.value);

const submitted = ref(false);
const error = ref(false);

const form = useForm("inscripcion", {
    nombre: "",
    dia: "",
    mes: "",
    anyo: "1900",
    ciudad: "",
    region: "",
    pais: "",
    email: "",
    telefono: "",
    comentario: "",
    acepto: false,
});

function submit() {
    // Clear all errors...
    error.value = false;
    form.clearErrors();
    form.post(route("cursos.inscripcion.store"), {
        preserveScroll: true,
        onSuccess: () => {
            submitted.value = true;
            useNav().scrollToTopPage("smooth");
        },
        onError: (response) => {
            // console.log('onError', response)
            error.value = response[0];
            useNav().scrollToTopPage("smooth");
        },
    });
}
</script>

<style scoped>
label {
    font-size: var(--text-sm, 0.875rem);
}
</style>
