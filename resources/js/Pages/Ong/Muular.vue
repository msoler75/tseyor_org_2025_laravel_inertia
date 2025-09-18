<template>

    <div class="container mt-12">
        <Back>ONG</Back>
    </div>

    <Sections>

        <Section class="container">
            <Text>
                <h2>¿Qué es el Muular?</h2>
                <p>El muular es el nombre que recibe el papel moneda tseyoriano. Su denominación deriva de Muul
                    (montículo
                    en maya) y nos ha sido dado por los hermanos mayores del cosmos.</p>
                <p>Es una moneda social de caracter espiritual que se va a utilizar en el ámbito de Tseyor
                    (Muulasterios,
                    Casas y Pueblos Tseyor) y en sus zonas de influencia o proximidad, para un intercambio justo de
                    bienes y
                    servicios que fomente los valores de hermandad, solidaridad y ayuda humanitaria.</p>
            </Text>
        </Section>


        <Section>
            <Text id="muular-electronico" title="Muular Electrónico" class="max-w-lg mt-7 mb-12 container p-5 flex flex-col gap-4 justify-center items-center">
                <h2>Muular Electrónico</h2>
                <p>
                    Es la moneda en versión electrónica, que puede ser empleada igualmente en la comunidad Tseyor.
                    Especialmente en las casas Tseyor y Muulasterios, pero también entre tseyorianos que convivan o
                    intercambien de un modo u otro, incluso por internet.
                </p>
                <ActionButton href="/muular-electronico" external>
                    Acceso al Muular electrónico
                </ActionButton>
                <div>
                    <small>No dispongo de muular electrónico. <Link class="text-secondary underline" href="/contactar">Quiero solicitarlo</Link></small>
                </div>
            </Text>
        </Section>


        <SectionBook
            titulo="Libro: El Muular. El Papel Moneda Tseyoriano"
            slug="el-muular-el-papel-moneda-tseyoriano"
            portada="/almacen/medios/libros/145/El%20Muular.%20El%20Papel%20Moneda%20Tseyoriano.jpg"
            enlace="/libros/el-muular-el-papel-moneda-tseyoriano"
            buttonLabel="Ver en Biblioteca">
            <p>Monografía que recoge todas las informaciones vertidas sobre la ONG Mundo Armónico Tseyor.</p>
        </SectionBook>


        <SectionBook
            titulo="Libro-Taller: Mejor con el Muular"
            slug="taller-mejor-con-el-muular"
            portada="/almacen/medios/libros/349/Taller%20mejor%20con%20el%20muular.jpg"
            enlace="/libros/taller-mejor-con-el-muular"
            buttonLabel="Ver en Biblioteca">
            <p>Taller impartido por la hermana Noiwanak acerca del uso del Muular y su comprensión.</p>
        </SectionBook>


    </Sections>
</template>
