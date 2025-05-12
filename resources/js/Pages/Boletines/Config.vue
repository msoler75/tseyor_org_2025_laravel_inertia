<template>
    <Page>
        <div class="w-full max-w-xl mx-auto bg-base-100 p-[5vw] shadow">
            <h1>Suscripción al Boletín Tseyor</h1>
            <div v-if="email">
                <p>Correo asociado: <b>{{ email }}</b></p>
            </div>
            <div v-else class="mb-3">
                <input type="email" v-model="inputEmail" placeholder="Introduce tu correo electrónico" class="input input-bordered w-full max-w-xs" />
            </div>
            <form @submit.prevent="submitConfig">
                <div>
                    <label>
                        <input
                            type="radio"
                            v-model="servicio"
                            value="boletin:semanal"
                        />
                        Semanal
                    </label>
                </div>
                <div>
                    <label>
                        <input
                            type="radio"
                            v-model="servicio"
                            value="boletin:bisemanal"
                        />
                        Quincenal
                    </label>
                </div>
                <div>
                    <label>
                        <input
                            type="radio"
                            v-model="servicio"
                            value="boletin:mensual"
                        />
                        Mensual
                    </label>
                </div>
                <div>
                    <label>
                        <input
                            type="radio"
                            v-model="servicio"
                            value="boletin:bimensual"
                        />
                        Bi-mensual
                    </label>
                </div>
                <div v-if="email">
                    <label class="text-lg text-error">
                        <input
                            type="radio"
                            v-model="servicio"
                            value="darse_baja"
                        />
                        Darse de baja
                    </label>
                </div>
                <button type="submit" class="btn btn-primary mt-5">Guardar</button>
            </form>
            <div v-if="mensajeGuardado" class="alert alert-success mt-3">
                {{ mensajeGuardado }}
            </div>
        </div>
    </Page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

// Definir las props que recibe la vista
const props = defineProps({
    token: String,
    servicioActual: String,
    email: String,
});


const inputEmail = ref(props.email);
const servicio = ref(props.servicioActual || "boletin:mensual");
const mensajeGuardado = ref("");
const urlParams = new URLSearchParams(window.location.search); // Usar URLSearchParams para obtener parámetros GET

function submitConfig() {
    axios.post(route("boletin.configurar", props.token), {
        servicio: servicio.value,
        email: inputEmail.value,
    }).then(() => {
        if (servicio.value === "darse_baja") {
            mensajeGuardado.value = "Te has dado de baja. Ya no recibirás más emails.";
        } else {
            mensajeGuardado.value = "Configuración guardada con éxito.";
        }
        if (urlParams.has('desde-profile')) {
            setTimeout(() => {
                history.back();
            }, 2000);
        }
        setTimeout(() => {
            mensajeGuardado.value = "";
        }, 30000); // El mensaje desaparece después de 30 segundos
    });
}
</script>
