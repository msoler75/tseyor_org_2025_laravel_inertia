<script setup>

import { useForm } from '@inertiajs/vue3';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <ActionSection>
        <template #title>
            Eliminar cuenta
        </template>

        <template #description>
            Elimina tu cuenta de forma permanente.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                Una vez tu cuenta sea eliminada, todos sus recursos y datos se eliminarán permanentemente. Antes de eliminar tu cuenta, descarga cualquier información o datos que desees conservar.
            </div>

            <div class="mt-5">
                <DangerButton @click="confirmUserDeletion">
                    Eliminar cuenta
                </DangerButton>
            </div>

            <!-- Delete Account Confirmation Modal -->
            <DialogModal :show="confirmingUserDeletion" @close="closeModal">
                <template #title>
                    Eliminar cuenta
                </template>

                <template #content>
                    ¿Estás seguro/a que quieres eliminar tu cuenta? Una vez eliminada, todos sus recursos y datos se eliminarán permanentemente. Por favor, introduce tu contraseña para confirmar que deseas eliminar permanentemente tu cuenta.

                    <div class="mt-4">
                        <TextInput
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4"
                            placeholder="Contraseña"
                            autocomplete="current-password"
                            @keyup.enter="deleteUser"
                        />

                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                </template>

                <template #footer>
                    <SecondaryButton @click="closeModal">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton
                        class="ml-3"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Eliminar cuenta
                    </DangerButton>
                </template>
            </DialogModal>
        </template>
    </ActionSection>
</template>
