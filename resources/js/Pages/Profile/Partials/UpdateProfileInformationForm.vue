
<template>
    <FormSection @submitted="updateProfileInformation">
        <template #title>
            Información de cuenta
        </template>

        <template #description>
            Actualiza tu información de cuenta y tu correo electrónico.
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input ref="photoInput" type="file" class="hidden" accept="image/*" @change="updatePhotoPreview">


                <Modal :show="isCropping" @close="isCropping = false" max-width="lg" id='modal-cropper'>
                    <div class="h-[calc(100vh_-_190px)] flex flex-col justify-center gap-3 select-none">
                        <cropper :src="photoPreview" image-restriction="stencil" :resize-image="{
                            adjustStencil: false
                        }" :stencil-component="CircleStencil" :background-wrapper-component="CustomBackgroundWrapper"
                            :stencil-props="{
                                aspectRatio: 1 / 1,
                                previewClass: 'stencil-crop',
                                movable: false,
                                resizable: false,
                                handlers: {},
                                lines: {}
                            }" ref="cropperElem" />
                    </div>
                    <div class="px-5 text-sm text-center my-3">Arrastra la imagen hasta la posición deseada y pulsa en
                        Recortar.</div>
                    <div class="px-5 py-3 flex justify-between sm:justify-between gap-5">
                        <div class="flex gap-4">
                            <button @click.prevent="cropperElem.zoom(1.2)" class="btn btn-primary text-3xl w-[2em]"> +
                            </button>
                            <button @click.prevent="cropperElem.zoom(.8)" class="btn btn-primary text-3xl w-[2em]"> -
                            </button>
                        </div>
                        <button @click.prevent="changePhotoCropper" class="btn btn-primary">
                            Recortar
                        </button>
                    </div>
                </Modal>

                <InputLabel for="photo" value="Imagen de perfil" />

                <!-- Current Profile Photo -->
                <div v-show="!photoPreview" class="mt-2">
                    <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full h-20 w-20 object-cover" />
                </div>

                <!-- New Profile Photo Preview -->
                <div v-show="photoPreview" class="mt-2">
                    <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                        :style="'background-image: url(\'' + photoPreview + '\');'" />
                </div>

                <SecondaryButton class="mt-2 mr-2" type="button" @click.prevent="selectNewPhoto">
                    Elegir otra imagen
                </SecondaryButton>

                <SecondaryButton v-if="user.profile_photo_url" type="button" class="mt-2" @click.prevent="deletePhoto">
                    Eliminar imagen
                </SecondaryButton>

                <InputError :message="form.errors.photo" class="mt-2" />
            </div>

            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Nombre simbólico TSEYOR" />
                <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" autocomplete="name" />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="email" value="Correo electrónico" />
                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full"
                    autocomplete="username" />
                <InputError :message="form.errors.email" class="mt-2" />

                <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                    <p class="text-sm mt-2 dark:text-white">
                        Tu dirección de correo no ha sido verificada.

                        <Link :href="route('verification.send')" method="post" as="button"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                            @click.prevent="sendEmailVerification">
                        Reenviar verificación para validar tu actual correo.
                        </Link>
                    </p>

                    <div v-show="verificationLinkSent"
                        class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        Un enlace de verificación ha sido enviado a tu correo.
                    </div>
                </div>
            </div>
        </template>

        <template #actions>
            <Link class="btn mr-auto" :href="route('usuario', user.id)">→ Ir al Perfil</Link>

            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Guardado.
            </ActionMessage>

            <PrimaryButton :disabled="form.processing || saved">
                <Spinner v-if="form.processing" />
                {{ form.processing ? 'Guardando' : 'Guardar' }}
            </PrimaryButton>
        </template>
    </FormSection>
</template>

<script setup>

import { router, useForm } from '@inertiajs/vue3';
import { CircleStencil, Cropper } from 'vue-advanced-cropper';
import CustomBackgroundWrapper from '@/Components/CustomBackgroundWrapper.vue';

import 'vue-advanced-cropper/dist/style.css';


const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    frase: props.user.frase,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);
const saved = ref(true)
const cropperElem = ref(null)
const isCropping = ref(false);

watch(() => form.name + form.email, () => {
    saved.value = false
})

function dataURItoFile(dataURI, fileName) {
    const arr = dataURI.split(',');
    const mime = arr[0].match(/:(.*?);/)[1];
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);

    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }

    return new File([u8arr], fileName, { type: mime });
}

function reduceImageSize(cb) {
    // verifica que la imagen en photoPreview tenga un tamaño máximo a 1024x1024. si no es así, la reducimos
    const image = new Image();
    image.src = photoPreview.value;
    image.onload = () => {
        if (image.width > 1024 || image.height > 1024) {
            const canvas = document.createElement('canvas');
            canvas.width = 1024;
            canvas.height = 1024;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(image, 0, 0, 1024, 1024);
            photoPreview.value = canvas.toDataURL();
            cb()
            return
        }
    };

    cb()
}

const updateProfileInformation = () => {
    if (photoInput.value && photoInput.value.files.length) {
        // form.photo = photoInput.value.files[0];
        // hemos de tomar la imagenen formato dataURI de photoPReview y convertirla a archivo de alguna forma para adjuntarla al formulario
        // para que el servidor nos acepte la imagen, esta debe ser en formato jpg o png. Porque sino, nos da el error: El campo foto debe ser un archivo con formato: jpg, jpeg, png
        const reader = new FileReader();
        reader.readAsDataURL(photoInput.value.files[0]);
        reader.onload = (e) => {
            // adjuntamos como si fuera un File
            reduceImageSize(() => {
                form.photo = dataURItoFile(photoPreview.value, photoInput.value.files[0].name)
                send()
            })

        }
        return
    }

    send()
};

const send = () => {
    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => {
            clearPhotoFileInput(),
                saved.value = true
        }
    });
}

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (!photo) return;


    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
        console.log(photoPreview.value)
        isCropping.value = true
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};


const changePhotoCropper = () => {
    const result = cropperElem.value.getResult()
    photoPreview.value = result.canvas.toDataURL()
    saved.value = false
    isCropping.value = false
}

var checkFrase = null
onMounted(() => {
    checkFrase = setInterval(() => {
        const div = document.querySelector(".cropper-event-notification")
        if (div)
            div.innerHTML = "Usa dos dedos para mover la imagen"
    }, 1000)
})

onBeforeUnmount(() => {
    console.log('unload checkfrase')
    clearInterval(checkFrase)
})


</script>


<style>
.stencil-crop {
    border: 4px rgba(255, 235, 56, 0.5);
    border-style: dashed;
}
</style>
