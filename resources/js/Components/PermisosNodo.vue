<template>
    <div class="relative flex gap-2 items-center" >
        <span>{{permisos}} {{permisosChars}}</span>
        <span title="Ver detalles"><Icon v-show="!mostrarPropiedades" icon="ph-info-duotone" class="cursor-pointer" @click="mostrarPropiedades=true" /></span>
    </div>
    <div v-show="mostrarPropiedades" class="relative pr-8">
        <span title="Cerrar detalles" class="absolute -translate-y-6 right-0 top-0 cursor-pointer" @click="mostrarPropiedades=false">✕</span>
        <div  v-for="frase of obtenerFrasePermisos()" :key="frase">
            - {{frase}}
        </div>
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

const mostrarPropiedades = ref(false)

const permisosChars = computed(()=>{
    const p = parseInt(props.permisos, 8)
    return ch(p,9,'d')+ 
    ch(p,8,'r')+ch(p,7,'w')+ch(p,6,'x')+' '+
    ch(p,5,'r')+ch(p,4,'w')+ch(p,3,'x')+' '+
    ch(p,2,'r')+ch(p,1,'w')+ch(p,0,'x')
})

const ch = (permisos, pos, char)=> bitHabilitado(permisos, pos) ? char : '-'

const obtenerFrasePermisos = () => {
    const permisosNumericos = parseInt(props.permisos, 8)

    if (props.esCarpeta) {
        return obtenerFraseCarpeta(permisosNumericos);
    } else {
        return obtenerFraseArchivo(permisosNumericos);
    }
};

const obtenerFraseCarpeta = (permisos) => {
    console.log('permisos', permisos, typeof permisos)

    const fraseSticky = bitHabilitado(permisos, 9) ? 'los contenidos de la carpeta son administrados por sus propietarios' : '';

    const fraseUsuarioLeer = bitHabilitado(permisos, 8) ? 'el usuario propietario puede descargar los contenidos de la carpeta' : '';
    const fraseUsuarioEscribir = bitHabilitado(permisos, 7) ? 'el usuario propietario puede subir archivos a la carpeta' : '';
    const fraseUsuarioEjecutar = bitHabilitado(permisos, 6) ? 'el usuario propietario puede ver el contenido de la carpeta' : '';

    const fraseGrupoLeer = bitHabilitado(permisos, 5) ? 'el grupo propietario puede descargar los contenidos de la carpeta' : '';
    const fraseGrupoEscribir = bitHabilitado(permisos, 4) ? 'el grupo propietario puede subir archivos a la carpeta' : '';
    const fraseGrupoEjecutar = bitHabilitado(permisos, 3) ? 'el grupo propietario puede ver el contenido de la carpeta' : '';

    const fraseTodosLeer = bitHabilitado(permisos, 2) ? 'todos los usuarios pueden descargar los contenidos de la carpeta' : '';
    const fraseTodosEscribir = bitHabilitado(permisos, 1) ? 'todos los usuarios pueden subir archivos a la carpeta' : '';
    const fraseTodosEjecutar = bitHabilitado(permisos, 0) ? 'todos los usuarios pueden ver el contenido de la carpeta' : '';

    const frases = [fraseSticky, fraseTodosEjecutar, fraseTodosLeer, fraseTodosEscribir,
        fraseGrupoEjecutar, fraseGrupoLeer, fraseGrupoEscribir,
        fraseUsuarioEjecutar, fraseUsuarioLeer, fraseUsuarioEscribir].filter(x => x);

    return frases
};

const obtenerFraseArchivo = (permisos) => {
    console.log('permisos', permisos)
    const fraseUsuarioLeer = bitHabilitado(permisos, 8) ? 'todos los usuarios pueden descargar este archivo' : '';
    const fraseUsuarioEscribir = bitHabilitado(permisos, 7) ? 'todos los usuarios pueden modificar/renombrar este archivo' : '';
    const fraseUsuarioEjecutar = false &&  bitHabilitado(permisos, 6) ? 'todos los usuarios pueden ejecutar este archivo' : '';

    const fraseGrupoLeer = bitHabilitado(permisos, 5) ? 'el grupo propietario puede descargar este archivo' : '';
    const fraseGrupoEscribir = bitHabilitado(permisos, 4) ? 'el grupo propietario puede modificar/renombrar este archivo' : '';
    const fraseGrupoEjecutar = false && bitHabilitado(permisos, 3) ? 'el grupo propietario puede ejecutar este archivo' : '';

    const fraseTodosLeer = bitHabilitado(permisos, 2) ? 'el usuario propietario puede descargar este archivo' : '';
    const fraseTodosEscribir = bitHabilitado(permisos, 1) ? 'el usuario propietario puede modificar/renombrar este archivo' : '';
    const fraseTodosEjecutar = false && bitHabilitado(permisos, 0) ? 'el usuario propietario puede ejecutar este archivo' : '';

    const frases = [fraseTodosLeer, fraseTodosEjecutar, fraseTodosEscribir,
        fraseGrupoLeer, fraseGrupoEjecutar, fraseGrupoEscribir,
        fraseUsuarioLeer, fraseUsuarioEjecutar, fraseUsuarioEscribir].filter(x => x);

    return frases
};

const bitHabilitado = (permisos, posicion) => {
    return (permisos & (1 << posicion)) !== 0;
};

</script>
  

