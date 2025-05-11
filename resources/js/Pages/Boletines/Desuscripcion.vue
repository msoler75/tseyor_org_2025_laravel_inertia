<template>
    <Page>
        <div class="w-full max-w-xl mx-auto bg-base-100 p-[5vw] shadow">
            <div v-if="desuscrito">
                <h1>¡Desuscripción confirmada!</h1>
                <p>👍 Ya no recibirás más boletines.</p>
            </div>
            <div v-else>
                <h1>Boletín Tseyor</h1>
                <p class="font-bold">¿Estás seguro/a de que deseas desuscribirte?</p>
                <button @click="confirmarDesuscripcion" class="btn btn-primary mt-5">Confirmar</button>
            </div>
        </div>
    </Page>
</template>

<script setup>
const desuscrito = ref(false);

onMounted(() => {
    // Si la página se recarga, asumimos que ya está desuscrito
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("confirmado") === "true") {
        desuscrito.value = true;
    }
});

function confirmarDesuscripcion() {
    // Simula la confirmación de desuscripción
    desuscrito.value = true;
    // Cambia la URL para reflejar el estado confirmado
    const updatedUrl = `${window.location.pathname}?confirmado=true`;
    window.history.pushState({ path: updatedUrl }, "", updatedUrl);
}
</script>

<style scoped>
/* Agrega estilos específicos para la vista aquí */
</style>
