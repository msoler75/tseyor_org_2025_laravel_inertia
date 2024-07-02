<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use App\Imports\GlosarioImport;
use Illuminate\Support\Facades\Cache;

/**
 * Class TerminCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TerminoCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\ReviseOperation\ReviseOperation;
    use \App\Traits\CrudContenido;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Termino::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/termino');
        CRUD::setEntityNameStrings('término', 'términos');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */


        //add div row using 'div' widget and make other widgets inside it to be in a row
        Widget::add()->to('before_content')->type('div')->class('row')->content([

            //widget made using fluent syntax
            Widget::make()
                ->type('card')
                ->class('card bg-dark text-white mb-1') // optional
                ->content([
                    'body' => 'Términos del glosario. No se incluyen términos de Guías Estelares ni de Lugares, como bases o planetas, porque estos van en sus propios tipos de contenidos. Se pueden importar todos los términos del glosario importando el archivo .docx del glosario entero y pulsar en \'Crear desde Word\''
                ])


        ]);


        CRUD::addButtonFromView('top', 'import_create', 'import_create', 'end ');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'nombre' => 'required|min:1',
            'slug' => [ \Illuminate\Validation\Rule::unique('terminos', 'slug')->ignore($this->crud->getCurrentEntryId()) ],
            'descripcion' => 'required|max:400'
        ]);
        // CRUD::setValidation(EntradaRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

        $folder = "/medios/terminos";

        CRUD::field('descripcion')->type('textarea')->attributes(['maxlength'=>400]);

        CRUD::field('texto')->type('tiptap_editor')->attributes(['folder' => $folder])->after('descripcion');

        CRUD::field('visibilidad')->type('visibilidad');
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }



    public function importCreate()
    {
        try {
            $num = GlosarioImport::importar();

            return response()->json([
                // "result" => "Archivo word copiado. Ahora procesará todos los términos",
                "redirect" => "/admin/termino/importando/paso1"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }


    public function importando1()
    {
        GlosarioImport::borrar_temporales();

        return "Procesando términos del glosario...
        <script>setTimeout(()=>{window.location.href='/admin/termino/importando/paso2'},1000)</script>";
    }

    public function importando2()
    {
        try {
            $terminado = GlosarioImport::procesar();
            if ($terminado) {
                Cache::forget('letras_glosario');
                return "Proceso terminado.<script>setTimeout(()=>{window.location.href='/admin/termino'},3000)</script>";
            } else
                return "Procesando términos del glosario...
        <script>setTimeout(()=>{window.location.href='/admin/termino/importando/paso2'},1000)</script>";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function test()
    {
        try {
            $terminos = GlosarioImport::parse("

> **GLOSARIO DE TÉRMINOS**

**Abstracción**. Separación de la forma. “Es muy difícil abstraerse en un mundo de formas, como es el nuestro, en este caso aquí, en vuestra situación, porque todo os conduce a una reflexión, a un análisis, a un raciocinio, a unas conclusiones. Digamos que se somete

todo aritméticamente en un cuestionamiento puro, puramente racional, dos y dos son cuatro, no pueden ser cinco ni pueden ser tres.

A partir de este razonamiento, todo lo demás se desenvuelve, más o menos, de forma similar, y comporta que el individuo se estanque y únicamente genere y regenere y estimule su energía mental a fin de obtener el máximo rendimiento a las necesidades que se le piden en esta situación, en este formato mental, psicológico, en este día a día.

Pero hay más, obviamente, y ese más únicamente se establece a través de la abstracción. Por eso, últimamente este taller de la abstracción, en el que se conjugan esas láminas de colores, dibujos, formas diversas, pueden ayudar muchísimo a establecer esa conexión interdimensional, porque lo que se pretende es que el individuo sepa parar su pensamiento, pueda en todo momento dejar de pensar, para abrirse a un nuevo mundo de percepciones.

La extrapolación mental, el conocimiento de esos mundos sublimes, únicamente el acceso a ellos, es por medio del no pensamiento, y ese taller de abstracción puede ayudar mucho a que este proceso se practique y se obtengan los resultados que cada uno habrá de obtener si precisamente actúa como se está indicando: con el no pensamiento.” Shilcars com. 1143.

> (Véase *Taller de abstracción.* Monografía:
>
> *Taller de abstracción*, Biblioteca Tseyor)

**Absoluto.** Lo que no depende de otra realidad. El Absoluto es incondicionado y previo a toda creación o manifestación de un universo. El Absoluto se diversifica hasta el infinito a través de la creación de universos que después retornan a la Fuente, cuando acaban los procesos de involución y evolución, para de nuevo proyectarse en una nueva creación. De esta forma funciona el Absoluto como

una noria que acarrea el agua de la vida hacia su origen. Los millones de trillones de réplicas en que el Absoluto se diversifica en la manifestación llevan un proceso de unificación en el que se acaban cristificando, unificándose en la unidad esencial común a todas ellas. Las que no lleguen a cristificarse seguirán en la dualidad indefinidamente.

> (Véase *Espíritu, Fractal, Dios, (33) Micropartícula.*
>
> Monografía*: El Absoluto.* Biblioteca *Tseyor)*


**Abducción.** Proceso por el cual se hace una réplica energética del cuerpo de una persona y es trasladada a otro espacio tiempo, nivel vibratorio o dimensión. La abducción suele hacerse de forma inconsciente, siempre con permiso o a petición del individuo. Por este procedimiento nos reunimos con frecuencia o estamos presentes en la Nave Tseyor. También se producen abducciones a las bases de la Confederación de Mundos Habitados de la Galaxia. Mediante el rescate adimensional se puede recuperar la experiencia vivida. Este tipo de abducción es la abducción de réplica, diferente de la abducción en la que se extrae el cuerpo físico como tal a la nave y es transportada por medio de ella a otro lugar.

La abducción también se produce cuando alcanzamos un cierto nivel vibratorio en el que todos salimos de este espacio-tiempo y en un segundo de aquí, pasamos horas, días y tal vez años, trabajando, laborando, corrigiendo y transmutando, gracias a la retroalimentación que se obtiene de todo el conjunto.

> (Véase *Intermitencia, Nave Tseyor, Naves cósmicas, Rescate adimensional; Traspaso adimensional*)


**Cristo Cósmico**. Es algo muy superior a lo que podamos imaginar. Va a llegar y lo hará de una forma tangible, despertando la consciencia de muchos, sus resultados se apreciarán especialmente en nuestro nivel. Muchas de las personas que están encarnadas actualmente han venido a ayudar en este proceso de la venida de Cristo. Un buen porcentaje de Tseyor son seres que han venido aquí en estos

tiempos que corren para ayudar al cambio. El cosmos entero está regido por el Cristo Cósmico.

El Cristo Cósmico se muestra en nuestro interior a partir del vino y del pan, como manifiesta el mantra de protección, en el tseek, en el 33. Es una energía que regenera nuestro cuerpo y mente.

No confundiremos el Cristo Cósmico con Cristo como religión. (Rasbek, TAP 162)

> (Véase *Pequeño Christian*, *Treinta y tres (33), Unos*)

**Cromosoma**. El ADN se agrupa formando los cromosomas, estos constituyen el material genético del cuerpo físico, que se extiende también a los demás cuerpos no físicos. La alineación de los cromosomas de los diferentes cuerpos y su activación consciente facilita acceder a otras dimensiones y al conocimiento de nuestra realidad multidimensional.



> **Clon**. Conjunto de individuos que proceden de un único individuo por vía asexual. Es un duplicado tridimensional del cuerpo físico.
>
> (Véase *Réplica*).

<img src='./medios/image10.jpeg' style=\"width:3.1239in;height:1.75in\" />

> **Fotograma del *film Cocoon***
>
> ***Cocoon.*** “Ya conocéis mi morfología, no hace falta especificar cómo soy, pero puedo daros también una referencia, tenéis un gran *film* aquí, en

vuestro planeta, que se denomina *Cocoon*. Ahí podréis observar cómo son mis congéneres, cómo viven, cómo se desplazan y cómo transmiten su luz y su conocimiento. Y también cómo se expresan en el momento de la relación con el trabajo *Juul*.” Noiwanak, com. 1169.

> (Véase Congéneres de Noiwanak, *Noiwanak*)

**TABLA EVALUATORIA, MANTRA **
        ");

            dd($terminos);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }


    public function show($id)
    {
        $termino = \App\Models\Termino::find($id);
        return $termino->visibilidad == 'P' ? redirect("/glosario/$id") : redirect("/glosario/$id?borrador");
    }
}
