<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    description: ''
});

const createTeam = () => {
    form.post(route('teams.store'), {
        errorBag: 'createTeam',
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection @submitted="createTeam">
        <template #title>
            Detalles del equipo
        </template>

        <template #description>
            Creación de un equipo.
        </template>

        <template #form>
            <div class="col-span-6">
                <InputLabel value="Creador del equipo" />

                <div class="flex items-center mt-2">
                    <Image class="object-cover w-12 h-12 rounded-full" :src="$page.props.auth.user.profile_photo_url"
                        :alt="$page.props.auth.user.name">

                    <div class="ml-4 leading-tight">
                        <div class="text-gray-900 dark:text-white">{{ $page.props.auth.user.name }}</div>
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $page.props.auth.user.email }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4 space-y-7">
                <div class="flex flex-col gap-2">
                    <InputLabel for="name" value="Nombre del equipo" />
                    <TextInput id="name" v-model="form.name" type="text" class="block w-full" autofocus />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="flex flex-col gap-2">
                    <InputLabel for="description" value="Descripción" />
                    <TextArea id="description" v-model="form.description" class="block w-full" />
                    <InputError :message="form.errors.description" />
                </div>
            </div>
        </template>

        <template #actions>
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Create
            </PrimaryButton>
        </template>
    </FormSection>
</template>
