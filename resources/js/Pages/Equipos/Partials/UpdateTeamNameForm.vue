<script setup>
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    team: Object,
    permissions: Object,
});

const form = useForm({
    nombre: props.team.nombre,
});

const updateEquipoName = () => {
    form.put(route('equipos.update', props.team), {
        errorBag: 'updateEquipoName',
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection @submitted="updateEquipoName">
        <template #title>
            Equipo Name
        </template>

        <template #description>
            The team's nombre and owner information.
        </template>

        <template #form>
            <!-- Equipo Owner Information -->
            <div class="col-span-6">
                <InputLabel value="Equipo Owner" />

                <div class="flex items-center mt-2">
                    <img class="w-12 h-12 rounded-full object-cover" :src="team.owner.profile_photo_url" :alt="team.owner.nombre">

                    <div class="ml-4 leading-tight">
                        <div class="text-gray-900 dark:text-white">{{ team.owner.nombre }}</div>
                        <div class="text-gray-700 dark:text-gray-300 text-sm">
                            {{ team.owner.email }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Equipo Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="nombre" value="Equipo Name" />

                <TextInput
                    id="nombre"
                    v-model="form.nombre"
                    type="text"
                    class="mt-1 block w-full"
                    :disabled="! permissions.canUpdateEquipo"
                />

                <InputError :message="form.errors.nombre" class="mt-2" />
            </div>
        </template>

        <template v-if="permissions.canUpdateEquipo" #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
