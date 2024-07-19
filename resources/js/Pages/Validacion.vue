<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import usePermisos from '@/Stores/permisos'
import AppLayout from '@/Layouts/AppLayout.vue'

const permisos = usePermisos()

// defineOptions({ layout: AppLayout })

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    loginName: '',
    password: '',
    remember: false,
});

const disabledResend = ref(false)
const submit = () => {
    disabledResend.value = true
    axios.post(route('verification.send'))
    setTimeout(()=>{
        disabledResend.value = false
    }, 1000*15)
};
</script>

<template>

    <Head title="Validar correo" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div class="text-sm">
                Antes de continuar, <b>revisa por favor tu buzón de correo</b> para que podamos verificar tu dirección de
                correo electrónico.
                Si no has recibido el mensaje, puedes solicitar nuevamente la verificación.
            </div>
            <div class="flex justify-between items-center gap-4">
                <input :disabled="disabledResend" type="submit" class="btn btn-small flex-shrink" value="Reenviar verificación">
                <div class="flex items-center gap-3 text-sm">
                    <Link :href="route('profile.show')" class="underline">Mi cuenta</Link>
                    <Link :href="route('logout')" class="underline">Cerrar sesión</Link>
                </div>
            </div>
        </form>
    </AuthenticationCard>
</template>

<style scoped>
form input {width: fit-content !important}
</style>
