<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    team: Object,
});

const confirmingEquipoDeletion = ref(false);
const form = useForm({});

const confirmEquipoDeletion = () => {
    confirmingEquipoDeletion.value = true;
};

const deleteEquipo = () => {
    form.delete(route('equipos.destroy', props.team), {
        errorBag: 'deleteEquipo',
    });
};
</script>

<template>
    <ActionSection>
        <template #title>
            Delete Equipo
        </template>

        <template #description>
            Permanently delete this team.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                Once a team is deleted, all of its resources and data will be permanently deleted. Before deleting this team, please download any data or information regarding this team that you wish to retain.
            </div>

            <div class="mt-5">
                <DangerButton @click="confirmEquipoDeletion">
                    Delete Equipo
                </DangerButton>
            </div>

            <!-- Delete Equipo Confirmation Modal -->
            <ConfirmationModal :show="confirmingEquipoDeletion" @close="confirmingEquipoDeletion = false">
                <template #title>
                    Delete Equipo
                </template>

                <template #content>
                    Are you sure you want to delete this team? Once a team is deleted, all of its resources and data will be permanently deleted.
                </template>

                <template #footer>
                    <SecondaryButton @click="confirmingEquipoDeletion = false">
                        Cancel
                    </SecondaryButton>

                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteEquipo"
                    >
                        Delete Equipo
                    </DangerButton>
                </template>
            </ConfirmationModal>
        </template>
    </ActionSection>
</template>
