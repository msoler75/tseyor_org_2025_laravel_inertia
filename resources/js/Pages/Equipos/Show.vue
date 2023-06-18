<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteEquipoForm from '@/Pages/Equipos/Partials/DeleteEquipoForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import EquipoMemberManager from '@/Pages/Equipos/Partials/EquipoMemberManager.vue';
import UpdateEquipoNameForm from '@/Pages/Equipos/Partials/UpdateEquipoNameForm.vue';

defineProps({
    team: Object,
    availableRoles: Array,
    permissions: Object,
});
</script>

<template>
    <AppLayout title="Equipo Settings">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Equipo Settings
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <UpdateEquipoNameForm :team="team" :permissions="permissions" />

                <EquipoMemberManager
                    class="mt-10 sm:mt-0"
                    :team="team"
                    :available-roles="availableRoles"
                    :user-permissions="permissions"
                />

                <template v-if="permissions.canDeleteEquipo && ! team.personal_team">
                    <SectionBorder />

                    <DeleteEquipoForm class="mt-10 sm:mt-0" :team="team" />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
