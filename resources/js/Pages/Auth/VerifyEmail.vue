<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import usePermisos from '@/Stores/permisos'

const permisos = usePermisos()

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
    setTimeout(() => {
        disabledResend.value = false
    }, 1000 * 15)
};


const logout = () => {
    permisos.permisos = []
    router.post(route('logout'));
};

</script>

<template>

    <Head title="Verificar correo" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div class="text-sm">
                Antes de continuar, <b>revisa por favor tu buzón de correo</b> para que podamos verificar tu dirección
                de correo:
            </div>
                <div class="text-sm">
                <b class="break-keep">{{ $page.props.auth.user.email }}</b>
            </div>
            <div class="text-sm">
                Si no has recibido el mensaje, puedes solicitar nuevamente la verificación.
            </div>
            <div class="flex justify-between items-center gap-4">
                <input :disabled="disabledResend" type="submit" class="btn btn-small flex-shrink"
                    value="Reenviar verificación">
                <div class="flex items-center gap-3 text-sm">
                    <Link :href="route('profile.show')" class="underline">Cambiar correo</Link>
                    <div @click="logout" class="underline cursor-pointer">Cerrar sesión</div>
                </div>
            </div>
        </form>
    </AuthenticationCard>
</template>

<style scoped>
form input {
    width: fit-content !important
}
</style>
