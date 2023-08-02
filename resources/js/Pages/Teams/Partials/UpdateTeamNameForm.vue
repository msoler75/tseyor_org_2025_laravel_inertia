<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    team: Object,
    permissions: Object,
});

const form = useForm({
    name: props.team.name,
    description: props.team.description
});

const updateTeamName = () => {
    form.put(route('teams.update', props.team), {
        errorBag: 'updateTeamName',
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection @submitted="updateTeamName">
        <template #title>
            Team Name
        </template>

        <template #description>
            The team's name and owner information.
        </template>

        <template #form>
            <!-- Team Owner Information -->
            <div class="col-span-6">
                <InputLabel value="Creador del equipo" />

                <div class="flex items-center mt-2">
                    <Image class="w-12 h-12 rounded-full object-cover" :src="team.owner.profile_photo_url"
                        :alt="team.owner.name"/>

                    <div class="ml-4 leading-tight">
                        <div class="text-gray-900 dark:text-white">{{ team.owner.name }}</div>
                        <div class="text-gray-700 dark:text-gray-300 text-sm">
                            {{ team.owner.email }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Name -->
            <div class="col-span-6 sm:col-span-4 space-y-7">

                <div class="flex flex-col gap-2">
                    <InputLabel for="name" value="Nombre del equipo" />
                    <TextInput id="name" v-model="form.name" type="text" class="block w-full"
                        :disabled="!permissions.canUpdateTeam" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="flex flex-col gap-2">
                    <InputLabel for="description" value="Descripción" />
                    <TextArea id="description" v-model="form.description" class="block w-full"
                        :disabled="!permissions.canUpdateTeam" />
                    <InputError :message="form.errors.description" />
                </div>

            </div>
        </template>

        <template v-if="permissions.canUpdateTeam" #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
