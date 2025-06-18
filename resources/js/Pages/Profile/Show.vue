<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import BoletinSuscripcion from '@/Pages/Profile/Partials/BoletinSuscripcion.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});
</script>

<template>

        <Header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Mi cuenta
            </h2>
        </Header>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                    <UpdateProfileInformationForm :user="$page.props.auth.user" />

                    <SectionBorder />
                </div>

                <div>
                    <BoletinSuscripcion/>

                    <SectionBorder />
                </div>

                <div id="cambio-clave" v-if="$page.props.jetstream.canUpdatePassword">
                    <UpdatePasswordForm class="mt-10 sm:mt-0" />

                    <SectionBorder />
                </div>

                <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                    <TwoFactorAuthenticationForm
                        :requires-confirmation="confirmsTwoFactorAuthentication"
                        class="mt-10 sm:mt-0"
                    />

                    <SectionBorder />
                </div>

                <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" />

                <SectionBorder />

                <ActionSection>
                    <template #title>
                        Token MCP para Claude Desktop
                    </template>
                    <template #description>
                        Genera tu token MCP personal para acceder a las herramientas avanzadas de Tseyor.org desde aplicaciones externas como Claude Desktop.
                    </template>
                    <template #content>
                        <Link :href="route('profile.mcp-token')" class="btn btn-outline btn-info">
                            Solicitar token MCP
                        </Link>
                        <p class="mt-2 text-gray-700 text-sm">
                            Esta sección te permite generar un <b>token MCP</b> personal, necesario para autenticarte y utilizar las herramientas avanzadas de Tseyor.org desde aplicaciones externas como Claude Desktop. El token confirma tu identidad como usuario registrado y te da acceso seguro a las funciones MCP sin compartir tu contraseña.
                        </p>
                    </template>
                </ActionSection>

                <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                    <SectionBorder />

                    <DeleteUserForm class="mt-10 sm:mt-0" />
                </template>
            </div>
        </div>

</template>
