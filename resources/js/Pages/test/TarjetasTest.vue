<template>
    <GridFill class="p-2 gap-2" colWidth="24rem">
        <!-- Card con imagen normal -->
        <CardContent title="Imagen Normal" description="Esta es una tarjeta con una imagen normal."
            image="https://picsum.photos/300/200" :imageContained="false" :skeleton="false" date="2024-09-15"
            tag="Normal" />

        <!-- Card con imagen contenida -->
        <CardContent title="Imagen Contenida" description="Esta tarjeta tiene una imagen contenida."
            image="https://picsum.photos/300/200" :imageContained="true" :skeleton="false" date="2024-09-15"
            tag="Contenida" />

        <!-- Card con skeleton -->
        <CardContent title="Skeleton" description="Esta tarjeta está en estado de skeleton."
            image="https://picsum.photos/300/200" :imageContained="false" :skeleton="true" date="2024-09-15"
            tag="Skeleton" />

        <!-- Card con imagen a la izquierda -->
        <CardContent title="Imagen a la Izquierda" description="Esta tarjeta tiene la imagen a la izquierda."
            image="https://picsum.photos/300/200" :imageContained="false" :skeleton="false" :imageLeft="true"
            date="2024-09-15" tag="Izquierda" />

        <!-- Card con href -->
        <CardContent title="Con Enlace" description="Esta tarjeta tiene un enlace."
            image="https://picsum.photos/300/200" href="https://example.com" :imageContained="false" :skeleton="false"
            date="2024-09-15" tag="Enlace" />

        <!-- Card con slot personalizado -->
        <CardContent title="Slot Personalizado" image="https://picsum.photos/300/200" :imageContained="false"
            :skeleton="false" date="2024-09-15" tag="Slot">
            <div class="p-4 bg-yellow-100">
                Este es un contenido personalizado en el slot por defecto.
            </div>
        </CardContent>

         <!-- Card con imagen a la izquierda y slot personalizado -->
         <CardContent title="Slot Personalizado con imagen izquierda" image="https://picsum.photos/300/200" :imageContained="false"
         image-left
            :skeleton="false" date="2024-09-15" tag="Slot">
            <div class="p-4 bg-yellow-100">
                Este es un contenido personalizado en el slot por defecto con la imagen a la izquierda.
            </div>
        </CardContent>

        <!-- Card con gradiente desactivado -->
        <CardContent title="Sin Gradiente" description="Esta tarjeta no tiene el efecto de gradiente en la descripción."
            image="https://picsum.photos/300/200" :imageContained="false" :skeleton="false" :gradient="false"
            date="2024-09-15" tag="No Gradiente" />

        <!-- Card en estado de borrador -->
        <CardContent title="Borrador" description="Esta tarjeta está en estado de borrador."
            image="https://picsum.photos/300/200" :imageContained="false" :skeleton="false" :draft="true"
            date="2024-09-15" tag="Borrador" />

        <!-- Card con descripción larga -->
        <CardContent title="Descripción Larga"
            description="Esta es una descripción muy larga que debería ser truncada automáticamente por el componente. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."
            image="https://picsum.photos/300/200" :imageContained="false" :skeleton="false" date="2024-09-15"
            tag="Larga" :maxLength="100" />


        <!-- Card con imagen de ancho personalizado -->
        <CardContent title="Imagen Ancha" description="Esta tarjeta tiene una imagen con ancho personalizado."
            image="https://picsum.photos/600/200" :imageWidth="600" :imageHeight="200" date="2024-09-15"
            tag="Ancho Custom" />

        <!-- Card con nombre de transición de vista personalizado -->
        <CardContent title="Transición de Vista"
            description="Esta tarjeta tiene un nombre de transición de vista personalizado."
            image="https://picsum.photos/300/200" imageViewTransitionName="hero-image" date="2024-09-15"
            tag="Transición" />

        <!-- Card con clase de imagen personalizada -->
        <CardContent title="Clase de Imagen" description="Esta tarjeta tiene una clase de imagen personalizada."
            image="https://picsum.photos/300/200" imageClass="rounded-full" date="2024-09-15" tag="Imagen Class" />

        <!-- Card con clase de descripción personalizada -->
        <CardContent title="Clase de Descripción"
            description="Esta tarjeta tiene una clase de descripción personalizada."
            image="https://picsum.photos/300/200" descriptionClass="italic text-blue-500" date="2024-09-15"
            tag="Desc Class" />

        <!-- Card sin imagen -->
        <CardContent title="Sin Imagen" description="Esta tarjeta no tiene imagen, solo texto." date="2024-09-15"
            tag="Solo Texto" />

        <!-- Card con tagLink -->
        <CardContent title="Tag con Enlace" description="Esta tarjeta tiene un tag con enlace."
            image="https://picsum.photos/300/200" tag="Enlace Tag" tagLink="/libros" date="2024-09-15" />

        <!-- Card con slot de imagen personalizado -->
        <CardContent title="Slot de Imagen" description="Esta tarjeta usa un slot personalizado para la imagen."
            date="2024-09-15" tag="Imagen Custom">
            <template #image>
                <div
                    class="w-full h-full bg-linear-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white">
                    Imagen Personalizada
                </div>
            </template>
        </CardContent>

        <!-- Card con combinación de varias propiedades -->
        <CardContent title="Combinación" description="Esta tarjeta combina varias propiedades diferentes."
            image="https://picsum.photos/300/300" :imageContained="true" :imageLeft="true" :draft="true"
            :gradient="false" imageClass="rounded-lg" date="2024-09-15" tag="Combinado" />

    </GridFill>
</template>
