<template>
    <div id="mc_embed_signup" class="max-w-xs mx-auto">
        <form @submit.prevent="submitForm">
            <h2 class="hidden text-primary">Suscribe</h2>
            <div id="mc_embed_signup_scroll" class="space-y-4">
                <div class="grid grid-cols-1">
                    <input
                    type="email" v-model="email" class="required email w-full px-3 py-2 border rounded-lg"
                        id="mce-EMAIL" placeholder="Correo electrónico">
                    <span id="mce-EMAIL-HELPERTEXT" class="helper_text"></span>
                </div>
                <div id="mce-responses" class="clear foot space-y-2">
                       <div v-if="errorMessage" class="response text-red-500">{{ errorMessage }}</div>
                   </div>
                <div class="optionalParent">
                    <div class="clear foot">
                        <button type="submit" class="text-neutral btn btn-sm bg-base-300 over:outline-2 outline-primary  dark:text-gray-100 w-full" :disabled="!!successMessage||suscribiendose">
                            <Spinner v-if="suscribiendose" class="text-primary mr-2 text-lg" />
                            <span v-if="suscribiendose">Espera...</span>
                            <template v-else>
                                <Icon v-if="!!successMessage" icon="ph:check-fat-fill" class="text-success text-lg" />
                                {{ !!successMessage ? '¡Te has suscrito!' : 'Suscribirme' }}
                            </template>
                        </button>
                    </div>
                </div>
            </div>
            <div >
                <p class="text-xs">Recibirás nuestro boletín con información de nuestros eventos, noticias, y otros contenidos.</p>
            </div>
        </form>
    </div>
</template>

<script setup>

const email = ref('');
const successMessage = ref('');
const errorMessage = ref('');
const suscribiendose = ref(false);

function submitForm() {
    console.log('SUSCRIPCION Email:', email.value);
    suscribiendose.value = true;
    axios.post(route('boletin.suscribir'), { email: email.value })
        .then(response => {
            successMessage.value = 'Suscripción exitosa';
            errorMessage.value = '';
        })
        .catch(error => {
            if (error.response && error.response.data && error.response.data.message) {
                errorMessage.value = error.response.data.message;
            } else {
                errorMessage.value = 'Hubo un error al suscribirse. Por favor, inténtalo de nuevo.';
            }
            successMessage.value = '';
        })
        .finally(() => {
            suscribiendose.value = false;
        });
}

watch(email, () => {
    successMessage.value = '';
});
</script>
