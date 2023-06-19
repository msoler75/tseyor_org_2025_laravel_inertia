<script setup>

import { router, useForm, usePage } from '@inertiajs/vue3';













const props = defineProps({
    team: Object,
    availableRoles: Array,
    userPermissions: Object,
});

const addEquipoMemberForm = useForm({
    email: '',
    role: null,
});

const updateRoleForm = useForm({
    role: null,
});

const leaveEquipoForm = useForm({});
const removeEquipoMemberForm = useForm({});

const currentlyManagingRole = ref(false);
const managingRoleFor = ref(null);
const confirmingLeavingEquipo = ref(false);
const teamMemberBeingRemoved = ref(null);

const addEquipoMember = () => {
    addEquipoMemberForm.post(route('team-members.store', props.team), {
        errorBag: 'addEquipoMember',
        preserveScroll: true,
        onSuccess: () => addEquipoMemberForm.reset(),
    });
};

const cancelEquipoInvitation = (invitation) => {
    router.delete(route('team-invitations.destroy', invitation), {
        preserveScroll: true,
    });
};

const manageRole = (teamMember) => {
    managingRoleFor.value = teamMember;
    updateRoleForm.role = teamMember.membership.role;
    currentlyManagingRole.value = true;
};

const updateRole = () => {
    updateRoleForm.put(route('team-members.update', [props.team, managingRoleFor.value]), {
        preserveScroll: true,
        onSuccess: () => currentlyManagingRole.value = false,
    });
};

const confirmLeavingEquipo = () => {
    confirmingLeavingEquipo.value = true;
};

const leaveEquipo = () => {
    leaveEquipoForm.delete(route('team-members.destroy', [props.team, usePage().props.auth.user]));
};

const confirmEquipoMemberRemoval = (teamMember) => {
    teamMemberBeingRemoved.value = teamMember;
};

const removeEquipoMember = () => {
    removeEquipoMemberForm.delete(route('team-members.destroy', [props.team, teamMemberBeingRemoved.value]), {
        errorBag: 'removeEquipoMember',
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => teamMemberBeingRemoved.value = null,
    });
};

const displayableRole = (role) => {
    return props.availableRoles.find(r => r.key === role).name;
};
</script>

<template>
    <div>
        <div v-if="userPermissions.canAddEquipoMembers">
            <SectionBorder />

            <!-- Add Equipo Member -->
            <FormSection @submitted="addEquipoMember">
                <template #title>
                    Add Equipo Member
                </template>

                <template #description>
                    Add a new team member to your team, allowing them to collaborate with you.
                </template>

                <template #form>
                    <div class="col-span-6">
                        <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                            Please provide the email address of the person you would like to add to this team.
                        </div>
                    </div>

                    <!-- Member Email -->
                    <div class="col-span-6 sm:col-span-4">
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            v-model="addEquipoMemberForm.email"
                            type="email"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="addEquipoMemberForm.errors.email" class="mt-2" />
                    </div>

                    <!-- Role -->
                    <div v-if="availableRoles.length > 0" class="col-span-6 lg:col-span-4">
                        <InputLabel for="roles" value="Role" />
                        <InputError :message="addEquipoMemberForm.errors.role" class="mt-2" />

                        <div class="relative z-0 mt-1 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer">
                            <button
                                v-for="(role, i) in availableRoles"
                                :key="role.key"
                                type="button"
                                class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                                :class="{'border-t border-gray-200 dark:border-gray-700 focus:border-none rounded-t-none': i > 0, 'rounded-b-none': i != Object.keys(availableRoles).length - 1}"
                                @click="addEquipoMemberForm.role = role.key"
                            >
                                <div :class="{'opacity-50': addEquipoMemberForm.role && addEquipoMemberForm.role != role.key}">
                                    <!-- Role Name -->
                                    <div class="flex items-center">
                                        <div class="text-sm text-gray-600 dark:text-gray-400" :class="{'font-semibold': addEquipoMemberForm.role == role.key}">
                                            {{ role.name }}
                                        </div>

                                        <svg v-if="addEquipoMemberForm.role == role.key" class="ml-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>

                                    <!-- Role Description -->
                                    <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 text-left">
                                        {{ role.description }}
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </template>

                <template #actions>
                    <ActionMessage :on="addEquipoMemberForm.recentlySuccessful" class="mr-3">
                        Added.
                    </ActionMessage>

                    <PrimaryButton :class="{ 'opacity-25': addEquipoMemberForm.processing }" :disabled="addEquipoMemberForm.processing">
                        Add
                    </PrimaryButton>
                </template>
            </FormSection>
        </div>

        <div v-if="team.team_invitations.length > 0 && userPermissions.canAddEquipoMembers">
            <SectionBorder />

            <!-- Equipo Member Invitations -->
            <ActionSection class="mt-10 sm:mt-0">
                <template #title>
                    Pending Equipo Invitations
                </template>

                <template #description>
                    These people have been invited to your team and have been sent an invitation email. They may join the team by accepting the email invitation.
                </template>

                <!-- Pending Equipo Member Invitation List -->
                <template #content>
                    <div class="space-y-6">
                        <div v-for="invitation in team.team_invitations" :key="invitation.id" class="card bg-base-100">
                            <div class="text-gray-600 dark:text-gray-400">
                                {{ invitation.email }}
                            </div>

                            <div class="flex items-center">
                                <!-- Cancel Equipo Invitation -->
                                <button
                                    v-if="userPermissions.canRemoveEquipoMembers"
                                    class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none"
                                    @click="cancelEquipoInvitation(invitation)"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </ActionSection>
        </div>

        <div v-if="team.users.length > 0">
            <SectionBorder />

            <!-- Manage Equipo Members -->
            <ActionSection class="mt-10 sm:mt-0">
                <template #title>
                    Equipo Members
                </template>

                <template #description>
                    All of the people that are part of this team.
                </template>

                <!-- Equipo Member List -->
                <template #content>
                    <div class="space-y-6">
                        <div v-for="user in team.users" :key="user.id" class="card bg-base-100">
                            <div class="flex items-center">
                                <img class="w-8 h-8 rounded-full object-cover" :src="user.profile_photo_url" :alt="user.name">
                                <div class="ml-4 dark:text-white">
                                    {{ user.name }}
                                </div>
                            </div>

                            <div class="flex items-center">
                                <!-- Manage Equipo Member Role -->
                                <button
                                    v-if="userPermissions.canUpdateEquipoMembers && availableRoles.length"
                                    class="ml-2 text-sm text-gray-400 underline"
                                    @click="manageRole(user)"
                                >
                                    {{ displayableRole(user.membership.role) }}
                                </button>

                                <div v-else-if="availableRoles.length" class="ml-2 text-sm text-gray-400">
                                    {{ displayableRole(user.membership.role) }}
                                </div>

                                <!-- Leave Equipo -->
                                <button
                                    v-if="$page.props.auth.user.id === user.id"
                                    class="cursor-pointer ml-6 text-sm text-red-500"
                                    @click="confirmLeavingEquipo"
                                >
                                    Leave
                                </button>

                                <!-- Remove Equipo Member -->
                                <button
                                    v-else-if="userPermissions.canRemoveEquipoMembers"
                                    class="cursor-pointer ml-6 text-sm text-red-500"
                                    @click="confirmEquipoMemberRemoval(user)"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </ActionSection>
        </div>

        <!-- Role Management Modal -->
        <DialogModal :show="currentlyManagingRole" @close="currentlyManagingRole = false">
            <template #title>
                Manage Role
            </template>

            <template #content>
                <div v-if="managingRoleFor">
                    <div class="relative z-0 mt-1 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer">
                        <button
                            v-for="(role, i) in availableRoles"
                            :key="role.key"
                            type="button"
                            class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                            :class="{'border-t border-gray-200 dark:border-gray-700 focus:border-none rounded-t-none': i > 0, 'rounded-b-none': i !== Object.keys(availableRoles).length - 1}"
                            @click="updateRoleForm.role = role.key"
                        >
                            <div :class="{'opacity-50': updateRoleForm.role && updateRoleForm.role !== role.key}">
                                <!-- Role Name -->
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600 dark:text-gray-400" :class="{'font-semibold': updateRoleForm.role === role.key}">
                                        {{ role.name }}
                                    </div>

                                    <svg v-if="updateRoleForm.role == role.key" class="ml-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>

                                <!-- Role Description -->
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                    {{ role.description }}
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="currentlyManagingRole = false">
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': updateRoleForm.processing }"
                    :disabled="updateRoleForm.processing"
                    @click="updateRole"
                >
                    Save
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Leave Equipo Confirmation Modal -->
        <ConfirmationModal :show="confirmingLeavingEquipo" @close="confirmingLeavingEquipo = false">
            <template #title>
                Leave Equipo
            </template>

            <template #content>
                Are you sure you would like to leave this team?
            </template>

            <template #footer>
                <SecondaryButton @click="confirmingLeavingEquipo = false">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    :class="{ 'opacity-25': leaveEquipoForm.processing }"
                    :disabled="leaveEquipoForm.processing"
                    @click="leaveEquipo"
                >
                    Leave
                </DangerButton>
            </template>
        </ConfirmationModal>

        <!-- Remove Equipo Member Confirmation Modal -->
        <ConfirmationModal :show="teamMemberBeingRemoved" @close="teamMemberBeingRemoved = null">
            <template #title>
                Remove Equipo Member
            </template>

            <template #content>
                Are you sure you would like to remove this person from the team?
            </template>

            <template #footer>
                <SecondaryButton @click="teamMemberBeingRemoved = null">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    :class="{ 'opacity-25': removeEquipoMemberForm.processing }"
                    :disabled="removeEquipoMemberForm.processing"
                    @click="removeEquipoMember"
                >
                    Remove
                </DangerButton>
            </template>
        </ConfirmationModal>
    </div>
</template>
