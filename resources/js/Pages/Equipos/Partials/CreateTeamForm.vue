<script setup>
import { useForm } from '@inertiajs/vue3';






const form = useForm({
    nombre: '',
});

const createEquipo = () => {
    form.post(route('equipos.store'), {
        errorBag: 'createEquipo',
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection @submitted="createEquipo">
        <template #title>
            Equipo Details
        </template>

        <template #description>
            Create a new team to collaborate with others on projects.
        </template>

        <template #form>
            <div class="col-span-6">
                <InputLabel value="Equipo Owner" />

                <div class="flex items-center mt-2">
                    <img class="object-cover w-12 h-12 rounded-full" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.nombre">

                    <div class="ml-4 leading-tight">
                        <div class="text-gray-900 dark:text-white">{{ $page.props.auth.user.nombre }}</div>
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $page.props.auth.user.email }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="nombre" value="Equipo Name" />
                <TextInput
                    id="nombre"
                    v-model="form.nombre"
                    type="text"
                    class="block w-full mt-1"
                    autofocus
                />
                <InputError :message="form.errors.nombre" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Create
            </PrimaryButton>
        </template>
    </FormSection>
</template>
