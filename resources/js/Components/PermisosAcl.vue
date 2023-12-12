<template>
    <div class="flex gap-2 items-center">
        ✅
        <span  v-if="acl.usuario"  title="usuario" class="flex items-center gap-1">
            <Icon icon="ph:user-duotone" title="usuario" />
            <span class="font-bold">{{ acl.usuario }}</span>
        </span>

        <span v-else-if="acl.grupo" title="grupo" class="flex items-center gap-1">
            <Icon icon="ph:users-three-duotone" title="grupo" />
            <span class="font-bold">{{ acl.grupo }}</span>
        </span>
        : {{ acl.verbos.replace(/,?ejecutar/, tipo == 'archivo' ? '' : ',listar')
            .replace(/(,?)leer/, '$1descargar')
            .replace(/,/g, ', ')
        }}
    </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    acl: Object,
    tipo: String,
});

</script>
