<template>
    <ActionSection>
        <template #title>
            Suscripción al Boletín
        </template>

        <template #description>
            Administra tu suscripción al boletín Tseyor.
        </template>

        <template #content>
            <div v-if="loading" class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                Cargando información de suscripción...
            </div>

            <div v-else-if="suscripcion && suscripcion.token" class="max-w-xl text-sm text-gray-600 dark:text-gray-400 space-y-4">
                <div>Estás suscrito al boletín en la modalidad: </div>
                <div class="uppercase"><strong>{{ suscripcion.tipo }}</strong></div>
                <div class="mt-4">
                    <Link :href="route('boletin.configurar.mostrar', { token: suscripcion.token })+'?desde-profile'" class="btn btn-secondary">
                        Configurar suscripción
                    </Link>
                </div>
            </div>

            <div v-else class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                <div class="mb-4 font-bold">No estás suscrito al boletín.</div>
                <Suscribe :email="userEmail" :readonly="true" @suscripcion="onSuscrito"/>
            </div>
        </template>
    </ActionSection>
</template>

<script setup>
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';

const page = usePage()

const suscripcion = ref(null);
const loading = ref(true);

const userEmail = computed(() => {
    // Aquí se asume que el correo del usuario está disponible en algún lugar del contexto global o props
    return page.props.auth?.user?.email;
});

function cargarSuscripcion() {
    axios.get(route('boletin.suscripcion'))
        .then(response => {
            suscripcion.value = response.data;
        })
        .catch(() => {
            console.error('Error al cargar la suscripción');
        })
        .finally(() => {
            loading.value = false;
        });
}

onMounted(() => {
    cargarSuscripcion()
});

function onSuscrito(email) {

    setTimeout(() => {
        // Aquí puedes hacer algo después de que el usuario se haya suscrito
        // Por ejemplo, recargar la información de la suscripción
        cargarSuscripcion();
    }, 1000);
}
</script>
