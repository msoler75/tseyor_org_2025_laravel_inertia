<template>
    <div class="container mx-auto py-12">

        <div class="container mx-auto flex justify-between items-center mb-7">
            <Back href="/muul">Espacio Muul</Back>
        </div>


        <h1 class="text-center">Diseñador de Tarjeta de Visita</h1>

        <p class="text-center">Rellena tus datos y se generará una tarjeta en formato imagen, que se enviará a tu correo
            electrónico.</p>

        <div class="w-full max-w-screen-md mx-auto flex flex-wrap justify-between gap-12 my-12">

            <div v-if="enviado" class="w-1/2 flex flex-col gap-5">
                <div class="alert alert-success">
                    <Icon icon="ph:check-circle-bold" class="text-2xl" />
                    <span>Se ha generado la tarjeta y se ha enviado a tu correo.</span>
                </div>

                <div class="alert alert-warning">
                    <Icon icon="ph:info-bold" class="text-2xl text-neutral" />
                    <span>Revisa tu bandeja de correo. Si no ves ningún mensaje, verifica el correo
                    "spam" o carpeta de "correos no deseados".</span>
                </div>

                <div class="alert">
                    <Icon icon="ph:info-bold" class="text-2xl text-neutral" />
                        <span>Ahora puedes consultar acerca de los correos @tseyor.org</span>
                </div>
                <ActionButton href="/muul/correos.tseyor">Correos @tseyor.org</ActionButton>
            </div>

            <form v-else class="flex flex-col bg-base-300 px-12 py-5 rounded-xl" @submit.prevent="generarEnviar">

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
                    <button class="btn btn-primary flex gap-2 items-center" type="submit" value='Generar y Enviar'
                    :disabled="enviando">
                        <Spinner v-if="enviando" />
                        <span v-if="enviando">Enviando</span>
                        <span v-else>Generar y Enviar</span>
                        </button>
                </div>
            </form>

            <div>
                <h3>Vista previa:</h3>
                <canvas class="shadow-xl" ref="preview" width="300" height="450" />
            </div>
        </div>
        <canvas class="hidden" ref="render" />
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue'
import { ucFirstAllWords, removeAccents, ucFirst } from '@/composables/textutils.js'

const carpeta = '/almacen/medios/muul/tarjeta'

defineOptions({ layout: AppLayout })


const page = usePage()
const user = page.props.auth?.user
const preview = ref(null)
const render = ref(null)

const datos = ref({
    nombre: user?.name,
    cargo: '',
    correo: user?.email,
    telefono: '',
    ciudad: '',
    pais: ''
})

const diseno = ref(1)

const nombre_simbolico = computed(() => ucFirstAllWords(datos.value.nombre.replace(/^\s+|\s+$/g, '').toLowerCase()).replace(/\s+/g, ' '))
const email_tseyor = computed(() => removeAccents(nombre_simbolico.value.toLowerCase(), true).replace(/\bpm\s*$/, '.pm').replace(/la\s+\.pm/, '.la.pm').replace(/\s+/g, '') + '@tseyor.org')


/**
 * Generar diseño
 * Si le pasamos cb (callback) es que estamos renderizando la tarjeta final
 */
function generarDiseño(cb) {
    console.log('generarDiseño', preview.value)

    if (typeof cb != "function")
        cb = null;

    const canvas = cb ? render.value :preview.value

    if (!canvas.getContext)
        return;

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

    fondo.src = carpeta + '/tarjeta_2019_plantilla' + diseno.value + '.' + (cb ? 'png' : 'thumb.jpg');

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

        var nombre = nombre_simbolico.value

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
        if (datos.value.ciudad) loc.push(ucFirst(datos.value.ciudad));
        if (datos.value.pais) loc.push(ucFirst(datos.value.pais));
        loc = loc.join(' - ');
        if (loc)
            contact_data.push(loc);
        if (datos.value.correo)
            contact_data.push(email_tseyor.value);
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
    generarDiseño();
    watch(() => datos, generarDiseño, { deep: true })
    watch(diseno, generarDiseño)
})


const enviado = ref(false)
const enviando = ref(false)

function generarEnviar() {
    enviando.value = true
    const canvas = preview.value

    // se genera la preview con imagenes de alta resolución, y se envia con un callback
    generarDiseño(() => {

        canvas.toBlob(function (blob) {

            const formData = new FormData();
            formData.append('nombre_tseyor', nombre_simbolico.value);
            formData.append('email_tseyor', email_tseyor.value);
            formData.append('imagen', blob, 'tarjeta.jpg');

            axios.post(route('tarjeta.visita.enviar'), formData)
                .then(response => {
                    console.log(response)
                    enviado.value = true
                    enviando.value = false
                })
        })
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
