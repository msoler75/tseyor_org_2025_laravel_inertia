<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import usePermisos from '@/Stores/permisos'
import AppLayout from '@/Layouts/AppLayout.vue'

const permisos = usePermisos()

defineOptions({ layout: AppLayout })

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    loginName: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => {
            form.reset('password'),
            permisos.cargarPermisos()
        }
    });
};
</script>

<template>
    <Head title="Iniciar Sesión" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit">

            <h3 class="text-center mb-8">Iniciar Sesión</h3>

            <div>
                <InputLabel for="loginName" value="Usuario o Correo electrónico" />
                <TextInput id="loginName" v-model="form.loginName" type="text" class="mt-1 block w-full" required autofocus
                    autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.loginName" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Contraseña" />
                <PasswordInput id="password" v-model="form.password" class="mt-1 block w-full"
                    autocomplete="current-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Recuérdame</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-4">
                <Link v-if="canResetPassword" :href="route('password.request')"
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                ¿Olvidaste tu contraseña?
                </Link>

                <PrimaryButton class="ml-4" :disabled="form.processing">
                    <Spinner v-if="form.processing" />
                    {{ form.processing ? 'Validando' : 'Iniciar sesión' }}
                </PrimaryButton>

            </div>

            <div class="text-center mt-10">
                No dispongo de cuenta
                <Link href="/register"
                    class="underline text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                Registrarme
                </Link>
            </div>
        </form>
    </AuthenticationCard>
</template>
