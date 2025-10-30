<template>
    <div v-if="editor">
        <template v-if="contenido">
            <div v-if="isDraft" class="alert alert-warning w-fit ml-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span class="md:hidden">Borrador</span>
                <span class="hidden md:inline">Pendiente de publicación</span>
                <a :href="editUrl" class="underline">Editar</a>
            </div>
            <div v-else class="w-fit ml-auto">
                <a :href="editUrl" class="ml-auto btn btn-xs btn-secondary"> <Icon icon="ph:pencil-duotone" /> Editar</a>
            </div>
        </template>
        <div v-else class="w-fit ml-auto">
            <div v-if="soloInforma">ERES ADMINISTRADOR</div>
            <a v-else :href="adminUrl" class="btn btn-xs btn-secondary">
                <Icon icon="ph:pencil-duotone" />
                Administrar</a>
        </div>
    </div>
    <div v-else-if="isDraft" class="alert alert-warning w-fit ml-auto">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <span class="md:hidden">Borrador</span>
        <span class="hidden md:inline">Pendiente de publicación</span>
    </div>
</template>

<script setup>
import useUserStore from '@/Stores/user'

const userStore = useUserStore()

const props = defineProps({
    necesita: { type: [String, Array], required: true },
    modelo: String,
    contenido: Object,
    soloInforma: Boolean,
    esAutor: {type: Boolean, default: false}
})

const editor = computed(()=>
props.esAutor || (
    typeof props.necesita == 'string' ? userStore.permisos.includes(props.necesita) :
    userStore.permisos.filter(permiso => props.necesita.includes(permiso)).length )
)

const isDraft = ref(props.contenido && ('visibilidad' in props.contenido) && props.contenido.visibilidad != 'P')

const editUrl = ref(props.contenido ? `/admin/${props.modelo.replace(/e?s$/, '')}/${props.contenido.id}/edit` : null)

const adminUrl = ref(props.modelo?`/admin/${props.modelo.replace(/e?s$/, '')}`:'/admin')

</script>
