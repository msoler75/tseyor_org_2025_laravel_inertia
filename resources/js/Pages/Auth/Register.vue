<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const emailGiven = ref(false)

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const email = urlParams.get('email');

    if (email) {
        // Hacer algo con el valor del email
        form.email = email
        emailGiven.value = !!email
    }
});




</script>

<template>

    <Head title="Registro de usuario" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <form @submit.prevent="submit" class="select-none">
            <div v-if="$page.props.flash.message" class="alert mb-12">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    class="stroke-info shrink-0 w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ $page.props.flash.message }}</span>
            </div>

            <div class="space-y-4">
                <h3 class="text-center mb-4">Registro de usuario</h3>

                <div>
                    <InputLabel for="name" value="Nombre simbólico TSEYOR" />
                    <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autofocus
                        autocomplete="name" />
                    <InputError class="mt-2" :message="form.errors.name" />
                    <small>Si no dispones de nombre simbólico, necesitas el <Referencia>curso holístico</Referencia>
                        </small>
                </div>

                <div>
                    <InputLabel for="email" value="Correo electrónico" />
                    <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required
                        :disabled="emailGiven" autocomplete="username" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Contraseña" />
                    <PasswordInput id="password" v-model="form.password"     required
                        autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password" />
                    <small>Crea una contraseña para tu cuenta en tseyor.org</small>
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Confirmar Contraseña" />
                    <PasswordInput id="password_confirmation" v-model="form.password_confirmation"
                        class="mt-1 block w-full" required autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature">
                    <InputLabel for="terms">
                        <div class="flex items-center">
                            <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

                            <div class="ml-2">
                                I agree to the <a target="_blank" :href="route('terms.show')"
                                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Terms
                                    of Service</a> and <a target="_blank" :href="route('policy.show')"
                                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Privacy
                                    Policy</a>
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.terms" />
                    </InputLabel>
                </div>

                <div class="mt-7 text-sm">
                    Los datos introducidos son de uso exclusivo de TSEYOR y no se compartirán a terceros ni se
                    publicarán.
                    <label class="mt-8 inline-flex items-center">
                        <AceptaCondiciones/>
                    </label>
                </div>

                <div class="mt-4 flex justify-end">
                    <PrimaryButton class="ml-4" :disabled="form.processing">
                        <Spinner v-if="form.processing" />
                        {{ form.processing ? 'Enviando' : 'Crear cuenta' }}
                    </PrimaryButton>
                </div>

                <div>
                    <Link :href="route('login')"
                        class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                    Ya dispongo de cuenta
                    </Link>
                </div>
            </div>
        </form>
    </AuthenticationCard>
</template>
