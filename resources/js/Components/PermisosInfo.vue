<template>
        <div v-for="frase of obtenerFrasePermisos()" :key="frase">
            - {{frase}}
        </div>
</template>
  
<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    esCarpeta: Boolean,
    propietarioUser: Number,
    propietarioGrupo: Number,
    permisos: String,
});

const obtenerFrasePermisos = () => {
    const permisosNumericos = parseInt(props.permisos, 10)

    if (props.esCarpeta) {
        return obtenerFraseCarpeta(permisosNumericos);
    } else {
        return obtenerFraseArchivo(permisosNumericos);
    }
};

const obtenerFraseCarpeta = (permisos) => {
    console.log('permisos', permisos, typeof permisos)

    const fraseUsuarioLeer = bitHabilitado(permisos, 8) ? 'el usuario propietario puede leer los contenidos de la carpeta' : '';
    const fraseUsuarioEscribir = bitHabilitado(permisos, 7) ? 'el usuario propietario puede subir archivos a la carpeta' : '';
    const fraseUsuarioEjecutar = bitHabilitado(permisos, 6) ? 'el usuario propietario puede listar la carpeta' : '';

    const fraseGrupoLeer = bitHabilitado(permisos, 5) ? 'el grupo propietario puede leer los contenidos de la carpeta' : '';
    const fraseGrupoEscribir = bitHabilitado(permisos, 4) ? 'el grupo propietario puede subir archivos a la carpeta' : '';
    const fraseGrupoEjecutar = bitHabilitado(permisos, 3) ? 'el grupo propietario puede listar la carpeta' : '';

    const fraseTodosLeer = bitHabilitado(permisos, 2) ? 'todos los usuarios pueden leer los contenidos de la carpeta' : '';
    const fraseTodosEscribir = bitHabilitado(permisos, 1) ? 'todos los usuarios pueden subir archivos a la carpeta' : '';
    const fraseTodosEjecutar = bitHabilitado(permisos, 0) ? 'todos los usuarios pueden listar la carpeta' : '';

    const frases = [fraseTodosEjecutar, fraseTodosLeer, fraseTodosEscribir,
        fraseGrupoEjecutar, fraseGrupoLeer, fraseGrupoEscribir,
        fraseUsuarioEjecutar, fraseUsuarioLeer, fraseUsuarioEscribir].filter(x => x);

    return frases
};

const obtenerFraseArchivo = (permisos) => {
    console.log('permisos', permisos)
    const fraseUsuarioLeer = bitHabilitado(permisos, 8) ? 'todos los usuarios pueden ver/descargar este archivo' : '';
    const fraseUsuarioEscribir = bitHabilitado(permisos, 7) ? 'todos los usuarios pueden modificar/renombrar este archivo' : '';
    const fraseUsuarioEjecutar = bitHabilitado(permisos, 6) ? 'todos los usuarios pueden ejecutar este archivo' : '';

    const fraseGrupoLeer = bitHabilitado(permisos, 5) ? 'el grupo propietario puede ver/descargar este archivo' : '';
    const fraseGrupoEscribir = bitHabilitado(permisos, 4) ? 'el grupo propietario puede modificar/renombrar este archivo' : '';
    const fraseGrupoEjecutar = bitHabilitado(permisos, 3) ? 'el grupo propietario puede ejecutar este archivo' : '';

    const fraseTodosLeer = bitHabilitado(permisos, 2) ? 'el usuario propietario puede ver/descargar este archivo' : '';
    const fraseTodosEscribir = bitHabilitado(permisos, 1) ? 'el usuario propietario puede modificar/renombrar este archivo' : '';
    const fraseTodosEjecutar = bitHabilitado(permisos, 0) ? 'el usuario propietario puede ejecutar este archivo' : '';

    const frases = [fraseTodosLeer, fraseTodosEjecutar, fraseTodosEscribir,
        fraseGrupoLeer, fraseGrupoEjecutar, fraseGrupoEscribir,
        fraseUsuarioLeer, fraseUsuarioEjecutar, fraseUsuarioEscribir].filter(x => x);

    return frases
};

const bitHabilitado = (permisos, posicion) => {
    return (permisos & (1 << posicion)) !== 0;
};

</script>
  

