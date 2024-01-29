<template>
    <div class="container mx-auto py-12">

        <h1 class="text-center">Diseñador de Tarjeta de Visita</h1>

        <p class="text-center">Rellena tus datos y se generará una tarjeta en formato imagen, que se enviará a tu correo
            electrónico.</p>

        <div class="w-full max-w-screen-md mx-auto flex flex-wrap justify-between gap-12 my-12">

            <form class="flex flex-col bg-base-300 px-12 py-5 rounded-xl" @submit.prevent="generarEnviar">

                <label>Nombre Simbólico TSEYOR<input name="nombre" type="text" v-model="datos.nombre"
                        placeholder="Nombre Simbólico de Tseyor"></label>
                <label>Cargo/Función/Rol <input name="cargo" type="text" v-model="datos.cargo"
                        placeholder="Cargo en Tseyor"></label>
                <!--<label>Correo Electrónico <input name="email" type="email" v-model="datos.correo"
                        placeholder="Correo electrónico"></label> -->
                <label>Teléfono <input name="telefono" type="text" v-model="datos.telefono" placeholder="Teléfono"></label>
                <label>Ciudad <input name="ciudad" type="text" v-model="datos.ciudad"
                        placeholder="Ciudad de residencia"></label>
                <label>País <input name="pais" type="text" v-model="datos.pais" placeholder="País de residencia"></label>
                <div class="flex gap-4 justify-center">
                    <label>Plantilla 1 <input name="diseno" type="radio" value="1" v-model="diseno"></label>
                    <label>Plantilla 2 <input name="diseno" type="radio" value="2" v-model="diseno"></label>
                </div>

                <div class="flex justify-center pt-5">
                    <input class="btn btn-primary" type="submit" value='Generar y Enviar'>
                </div>
            </form>

            <div>
                <h3>Vista previa:</h3>
                <canvas class="shadow-xl" ref="preview" width="300" height="450" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue'
import {ucFirstAllWords, removeAccents, ucFirst} from '@/composables/textutils.js'
defineOptions({ layout: AppLayout })


const page = usePage()
const user = page.props.auth?.user
const preview = ref(null)

const datos = ref({
    nombre: user?.name,
    cargo: '',
    correo: user?.email,
    telefono: '',
    ciudad: '',
    pais: ''
})

const diseno = ref(1)


function draw_data(cb) {
    console.log('draw_data', preview.value)

    const canvas = preview.value

    if (!canvas.getContext)
        return;

    // limpiamos el nombre y lo formateamos
    const nombre = ucFirstAllWords(datos.value.nombre.replace(/^\s+|\s+$/g, '').toLowerCase()).replace(/\s+/g, ' ');

    // generamos el correo tseyor
    const email_tseyor = removeAccents(nombre.toLowerCase(), true).replace(/\bpm\s*$/, '.pm').replace(/la\s+\.pm/, '.la.pm').replace(/\s+/g, '') + '@tseyor.org';


    if (typeof cb != "function")
        cb = null;

    if (cb) {
        canvas.width = 1441;
        canvas.height = 2150;
    }

    var w = canvas.width,
        h = canvas.height,
        f = canvas.height / 50,
        mw = .86 * w;

    const ctx = canvas.getContext('2d'),
        fondo = new Image();

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    fondo.src = '/almacen/medios/muul/tarjeta/tarjeta_2019_plantilla' + diseno.value + '.' + (cb ? 'png' : 'thumb.jpg');

    // Use the identity matrix while clearing the canvas
    //context.setTransform(1, 0, 0, 1, 0, 0);

    fondo.onload = function () {
        //ctx.fillStyle = 'rgb(255, 0, 0)';

        var r = fondo.width / fondo.height;
        ctx.drawImage(fondo, 0, 0, w, h);

        /*ctx.fillStyle = 'rgb(0, 255, 255)';
        ctx.fillRect(0, 0, w, h);*/

        ctx.fillStyle = 'rgb(0, 0, 0)';
        ctx.textAlign = 'center';

        if (nombre) {
            ctx.font = 'bold ' + Math.round(2.5 * f) + 'pt Palatino, Georgia, Arial, serif';
            if (nombre.length < 12)
                nombre = nombre.split("").join(String.fromCharCode(8202));
            ctx.fillText(nombre, w / 2, (diseno.value == 1 ? .37 : .47) * h, mw)
        }

        if (datos.value.cargo) {
            ctx.font = 'italic ' + Math.round(1 * f) + 'pt Calibri';
            ctx.fillText(ucFirst(datos.value.cargo), w / 2, (diseno.value == 1 ? .43 : .52) * h, mw)
        }

        ctx.font = Math.round(1 * f) + 'pt Calibri, serif';
        ctx.fillStyle = diseno.value == 1 ? '#fff' : '#0f409d';

        const contact_data = [];
        var loc = [];
        if (datos.value.ciudad) loc.push(datos.value.ciudad);
        if (datos.value.pais) loc.push(datos.value.pais);
        loc = loc.join(' - ');
        if (loc)
            contact_data.push(loc);
        if (datos.value.correo)
            contact_data.push(email_tseyor);
        if (datos.value.telefono)
            contact_data.push(datos.value.telefono);

        var y = (diseno.value == 1 ? .525 : .62) + (3 - contact_data.length) * (diseno.value == 1 ? .024 : .021), step = .04;
        for (var i in contact_data) {
            ctx.fillText(contact_data[i], w / 2, y * h, mw)
            y += step;
        }

        if (cb)
            cb();
    }

}

onMounted(() => {
    draw_data();
    watch(() => datos, draw_data, { deep: true })
    watch(diseno, draw_data)
})



function generarEnviar() {
    canvas.toBlob(function (blob) {

        formData = new FormData();
        formdatos.append('blob', blob, 'tarjeta.jpg');
        formdatos.append('nombre', nombre);
        formdatos.append('cargo', cargo);
        formdatos.append('email', email);
        formdatos.append('telefono', telefono);
        formdatos.append('ciudad', ciudad);
        formdatos.append('pais', pais);
        formdatos.append('email_tseyor', email_tseyor);

        uploading_file = true;

        xhr.send(formData);
    })
}

</script>

<style scoped>
form label {
    @apply text-xs;
}
form input[type="text"] {
    @apply text-base w-64;
}
</style>
