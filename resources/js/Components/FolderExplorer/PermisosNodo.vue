<template>
    <div class="relative flex gap-2 items-center">
        <span>{{ permisos }} {{ permisosChars }}</span>
        <span title="Ver detalles">
            <Icon v-show="!mostrarPropiedades" icon="ph-info-duotone" class="cursor-pointer"
                @click="mostrarPropiedades = true" />
        </span>
    </div>
    <div v-show="mostrarPropiedades" class="relative pr-8">
        <span title="Cerrar detalles" class="absolute -translate-y-6 right-0 top-0 cursor-pointer"
            @click="mostrarPropiedades = false">✕</span>
        <div v-for="frase of obtenerFrasePermisos()" :key="frase">
            {{ frase }}
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    esCarpeta: Boolean,
    permisos: String,
});

const mostrarPropiedades = ref(false)

const permisosChars = computed(() => {
    const p = parseInt(props.permisos, 8)
    return (props.esCarpeta?ch(p, 9, 't'):'') +
        ch(p, 8, 'r') + ch(p, 7, 'w') + ch(p, 6, 'x') + ' ' +
        ch(p, 5, 'r') + ch(p, 4, 'w') + ch(p, 3, 'x') + ' ' +
        ch(p, 2, 'r') + ch(p, 1, 'w') + ch(p, 0, 'x')
})

const ch = (permisos, pos, char) => bitHabilitado(permisos, pos) ? char : '-'

const obtenerFrasePermisos = () => {
    const permisosNumericos = parseInt(props.permisos, 8)

    if (props.esCarpeta) {
        return obtenerFraseCarpeta(permisosNumericos);
    } else {
        return obtenerFraseArchivo(permisosNumericos);
    }
};


const pQuien = ['el usuario propietario', 'el grupo propietario', 'el resto de usuarios']

const pCarpeta = {
    bits: {
        r: 'descargar',
        w: 'escribir (añadir, modificar y eliminar)',
        x: 'listar',
    },
    que: 'contenidos de la carpeta'
}

const pArchivo = {
    bits:{
        r: 'descargar',
        w: 'escribir (añadir, modificar y eliminar)',
        x: 'ejecutar'
    },
    que: 'el archivo'
}


function generarFrases(pUser, pGroup, pOthers, pType) {
        const fraseUsuario = generarFrase(pQuien[0], pUser, pType)

    const fraseGroup = generarFrase(pQuien[1], pGroup, pType)

    const fraseOthers = generarFrase(pQuien[2], pOthers, pType)

    return [fraseUsuario, fraseGroup, fraseOthers]
    }

function generarFrase(quien, bits, permisosTipo) {
    let todosNum = 3
    if(permisosTipo.bits.x =='ejecutar')
    {
        todosNum = 2
        bits=bits.filter(bit=>bit!='x')
    }
    const icon = bits.length === 0 ? '🚫' : '✅'
    return icon + ' ' + quien + ' ' + (bits.length === 0 ? ' no tiene permisos' :
        bits.length === todosNum ? 'tiene todos los permisos' : 'puede ' + bits.map(x => permisosTipo.bits[x]).join(', ') + ' ' + permisosTipo.que)
}

function generarFrasesBase(permisos, pTipo) {
    const pUser = []
    const pGroup = []
    const pOthers = []

    if (bitHabilitado(permisos, 8)) pUser.push('r')
    if (bitHabilitado(permisos, 7)) pUser.push('w')
    if (bitHabilitado(permisos, 6)) pUser.push('x')

    if (bitHabilitado(permisos, 5)) pGroup.push('r')
    if (bitHabilitado(permisos, 4)) pGroup.push('w')
    if (bitHabilitado(permisos, 3)) pGroup.push('x')

    if (bitHabilitado(permisos, 2)) pOthers.push('r')
    if (bitHabilitado(permisos, 1)) pOthers.push('w')
    if (bitHabilitado(permisos, 0)) pOthers.push('x')

    return generarFrases(pUser, pGroup, pOthers, pTipo)
}

const obtenerFraseCarpeta = (permisos) => {
    const fraseSticky = props.esCarpeta&&bitHabilitado(permisos, 9) ? '🔒 los contenidos de la carpeta son administrados por sus propietarios' : '';
    return [fraseSticky, ...generarFrasesBase(permisos, pCarpeta)].filter(x => x)
};

const obtenerFraseArchivo = (permisos) => {
    return generarFrasesBase(permisos, pArchivo)
};

const bitHabilitado = (permisos, posicion) => {
    return (permisos & (1 << posicion)) !== 0;
};

</script>


