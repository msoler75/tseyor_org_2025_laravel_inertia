<template>
    <FondoEstrellado class="w-full relative py-12" id="myform">
        <div class="card bg-base-100 shadow-2xs max-w-lg mx-auto p-7 relative animate-fade-in">
            <h1>Contactar con Tseyor</h1>
            <div v-if="error">
                <div class="alert alert-error">
                    <Icon icon="ph:warning-circle-duotone" class="text-2xl" />
                    <span> {{ error }}</span>
                </div>
            </div>
            <div v-if="submitted" class="space-y-7">
                <div class="alert alert-success">
                    <Icon icon="ph:check-circle-bold" class="text-2xl" />
                    <span>Se han enviado los datos correctamente.</span>
                </div>

                <div class="alert">
                    <Icon icon="ph:info-bold" class="text-2xl text-info" />
                    <span>Revisa tu bandeja de correo.</span>
                </div>

            </div>
            <!-- Formulario empieza aquí -->
            <form v-else @submit.prevent="submit">
                <p>Escribenos con el siguiente formulario o si lo prefieres dirígete a alguno de nuestros
                    <Link :href="route('contactos')">representantes</Link>
                </p>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="nombre">Nombre y apellidos:</label>
                    <input class="form-input w-full" id="nombre" type="text" v-model="form.nombre" required
                        data-clarity-mask="true">
                    <span v-if="form.errors.nombre" class="error">{{ form.errors.nombre }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="pais">País:</label>
                    <input class="form-input w-full" id="pais" type="text" v-model="form.pais" required
                        data-clarity-mask="true">
                    <span v-if="form.errors.pais" class="error">{{ form.errors.pais }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="email">Correo electrónico:</label>
                    <input class="form-input w-full" id="email" type="email" v-model="form.email" required
                        data-clarity-mask="true">
                    <span v-if="form.errors.email" class="error">{{ form.errors.email }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="telefono">Teléfono/Whatsapp (opcional):</label>
                    <input class="form-input w-full" id="telefono" type="text" v-model="form.telefono"
                        data-clarity-mask="true">
                    <span v-if="form.errors.telefono" class="error">{{ form.errors.telefono }}</span>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2" for="comentario">Tu comentario o petición</label>
                    <textarea class="form-textarea w-full" id="comentario" v-model="form.comentario" required
                        data-clarity-mask="true"></textarea>
                    <span v-if="form.errors.comentario" class="error">{{ form.errors.comentario }}</span>
                </div>
                <div class="mb-4">
                    <label class="inline-flex flex-col gap-2 items-center">
                        <AceptaCondiciones v-model="form.acepto"
                        finalidad="recibir información"/>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    Enviar
                </button>
            </form>
        </div>


        <Section v-show="submitted" class="py-7">
            <TextImage title="¿Y ahora qué?" srcImage="/almacen/medios/paginas/camino-infinito.jpg"
                href="/mis-primeros-pasos" buttonLabel="Mis primeros pasos" class="container"
                textClass="bg-base-100 p-5 rounded-2xl">
                Te recomendamos la lectura de esta sección
            </TextImage>
        </Section>

    </FondoEstrellado>
</template>




<script setup>
import { useForm } from '@inertiajs/vue3'

const submitted = ref(false)
const error = ref(false)

const form = useForm('inscripcion', {
    nombre: '',
    pais: '',
    email: '',
    telefono: '',
    comentario: '',
    acepto: false
})


function scrollTop() {
    console.log('scrollto_5_contactar')
    document.getElementById("myform").scrollIntoView(true)
}

function submit() {
    // Clear all errors...
    error.value = false
    form.clearErrors()
    form.post(route('contactar.send') + '?test=1', {
        preserveScroll: true,
        onSuccess: () => {
            submitted.value = true
            scrollTop()
        },
        onError: (response) => {
            // console.log('onError', response)
            error.value = response[0]
            scrollTop()
        }
    });
}
</script>
