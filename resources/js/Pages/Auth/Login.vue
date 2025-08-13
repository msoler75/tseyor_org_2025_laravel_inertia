<template>

    <h1 class="hidden">Iniciar Sesión</h1>

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit">

            <h3 class="text-center mb-8">Iniciar Sesión</h3>

            <input type="hidden" name="to" :value="to">

            <div>
                <InputLabel for="loginName" value="Usuario o Correo electrónico" />
                <TextInput id="loginName" v-model="form.loginName" type="text" class="mt-1 block w-full" required
                    autofocus autocomplete="username" />
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
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                ¿Olvidaste tu contraseña?
                </Link>

                <PrimaryButton class="ml-4" :disabled="form.processing || isLoggingIn">
                    <Spinner v-if="form.processing || isLoggingIn" />
                    {{ (form.processing || isLoggingIn) ? 'Validando...' : 'Iniciar sesión' }}
                </PrimaryButton>

            </div>

            <div class="text-center mt-10">
                No dispongo de cuenta
                <Link href="/register"
                    class="underline text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                Registrarme
                </Link>
            </div>
        </form>
    </AuthenticationCard>

</template>


<script setup>
// import useUserStore from '@/Stores/user'

// const userStore = useUserStore()

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const to = ref(null)
const isLoggingIn = ref(false)

onMounted(()=> {
    // obtener parámetro GET "to" de la URL ?
    const urlParams = new URLSearchParams(window.location.search);
    to.value = urlParams.get('to') || '';
})

const form = useForm({
    loginName: '',
    password: '',
    remember: false,
    to: ''
});

const submit = () => {
    isLoggingIn.value = true
    form.transform(data => ({
        ...data,
        to: to.value,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onSuccess: () => {
            // No reseteamos el password aquí para evitar el flash
            // La redirección manejará el cambio de página
        },
        onError: () => {
            // Solo reseteamos el password si hay error
            isLoggingIn.value = false
            form.reset('password')
        }
    });
};
</script>
