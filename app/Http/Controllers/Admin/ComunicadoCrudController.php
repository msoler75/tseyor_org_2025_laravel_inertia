<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Storage;
use App\Models\Comunicado;

/**
 * Class ComunicadoCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ComunicadoCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Comunicado::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/comunicado');
        CRUD::setEntityNameStrings('comunicado', 'comunicados');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */

        $this->crud->addColumn([
            'name'  => 'titulo',
            'label' => 'Título',
            'type'  => 'text'
        ]);


        $this->crud->addColumn([
            'name' => 'updated_at',
            'label' => 'Modificado',
            'type' => 'datetime', // Puedes usar 'datetime' o 'date' según el formato que desees mostrar
        ]);


        $this->crud->addColumn([
            'name'  => 'categoria',
            'label' => 'Categoría',
            'type'  => 'enum',
            'options'     => ['GEN' => 'General', 'TAP' => 'TAP', 'DOCEM' => 'Doce del Muulasterio', 'MUUL' => 'Muul']
        ]);

        $this->crud->addColumn([
            'name'  => 'visibilidad',
            'label' => 'Estado',
            'type'  => 'text',
            'value' => function ($entry) {
                return $entry->visibilidad == 'P' ? '✔️ Publicado' : '⚠️ Borrador';
            }
        ]);

        $this->crud->addButtonFromView('top', 'import', 'import', 'end');
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
            'titulo' => 'required|min:8',
        ]);

        CRUD::setFromDb(); // set fields from db columns.

        CRUD::field([
            'name' => 'numero',
            'wrapper' => [
                'class' => 'form-group col-md-3'
            ]
        ]);

        CRUD::field([   // select_from_array
            'name'        => 'categoria',
            'label'       => "Categoría",
            'type'        => 'select_from_array',
            'options'     => ['GEN' => 'General', 'TAP' => 'TAP', 'DOCEM' => 'Doce del Muulasterio', 'MUUL' => 'Muul'],
            'allows_null' => false,
            'default'     => 'GEN',
            // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;

            'wrapper'   => [
                'class'      => 'form-group col-md-3'
            ],
        ]);

        $folder = $this->mediaFolder();

        // CRUD::field('texto')->type('markdown_tinymce')->attributes(['folder' => $folder]);

        CRUD::field('texto')->type('markdown_tinymce')->attributes(['folder' => $folder]);

        CRUD::field('imagen')->type('image_cover')->attributes(['folder' => $folder, 'from' => 'texto']);

        CRUD::field('visibilidad')->type('visibilidad');
    }

    private function mediaFolder()
    {
        $anioActual = date('Y');
        $mesActual = date('m');

        $folder = "/media/comunicados/$anioActual/$mesActual";

        // Verificar si la carpeta existe en el disco 'public'
        if (!Storage::disk('public')->exists($folder)) {
            // Crear la carpeta en el disco 'public'
            Storage::disk('public')->makeDirectory($folder);
        }

        return $folder;
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


    protected function show($id)
    {
        return redirect("/comunicados/$id?borrador");
    }

    // show whatever you want
    protected function setupShowOperation2()
    {
        // MAYBE: do stuff before the autosetup

        // automatically add the columns
        // $this->autoSetupShowOperation();

        $id = 0;
        if (preg_match('/\/(\d+)\/show$/', $_SERVER['REQUEST_URI'], $match))
            $id = $match[1];

        // https://backpackforlaravel.com/docs/6.x/crud-columns#custom_html-1
        if ($id)
            $this->crud->addColumn(
                [
                    'name'     => 'my_custom_html',
                    'label'    => 'Ver en Web',
                    'type'     => 'custom_html',
                    'value'    => "<a href='/comunicados/$id?borrador' target='_blank'>➡️ Ver Comunicado en el Sitio Web</a>"
                ]
            );

        CRUD::column('titulo')->type('text');
        CRUD::column('numero')->type('number');
        CRUD::column('categoria')->type('text');
        CRUD::column('descripcion')->type('textarea');
        CRUD::column('texto')->type('mymarkdown');
        CRUD::column('imagen')->type('image');

        $this->crud->addColumn([
            'name'  => 'visibilidad',
            'label' => 'Estado',
            'type'  => 'text',
            'value' => function ($entry) {
                return $entry->visibilidad == 'P' ? '✔️ Publicado' : '⚠️ Borrador';
            }
        ]);

        // MAYBE: do stuff after the autosetup


        // or maybe remove a column
        // CRUD::column('text')->remove();
    }

    public function import()
    {
        // Directorio temporal para almacenar el archivo ZIP
        $tempDir = sys_get_temp_dir();

        // Generar un nombre único para el archivo ZIP
        $outputFilePath = tempnam($tempDir, 'import_') . '.zip';

        // Ruta del archivo .docx recibido
        $tempFilePath = $_FILES['file']['tmp_name'];

        // Obtener la extensión del archivo original
        $originalExtension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        // Generar una nueva ruta para la copia del archivo con la extensión correcta
        $docxFilePath = $tempDir . '/import_' . uniqid() . '.' . $originalExtension;

        // Copiar el archivo temporal a la nueva ubicación con la extensión correcta
        if (!copy($tempFilePath, $docxFilePath)) {
            return "Error al copiar nuevo archivo";
        }

        // Obtener la URL de la variable de entorno
        $wordToMdUrl = env('WORD_TO_MD_URL');

        if (!$wordToMdUrl)
            return response()->json([
                "error" => "Servidor de conversión no configurado"
            ], 500);

        // Realizar la petición al servidor para convertir el archivo .docx a markdown
        $curl = curl_init();

        $postData = [
            'file' => curl_file_create($docxFilePath)
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => $wordToMdUrl,
            CURLOPT_RETURNTRANSFER => true,
            // CURLOPT_VERBOSE => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        // Verificar el código de respuesta HTTP
        if ($httpCode === 200) {
            // Guardar la respuesta en un archivo ZIP
            file_put_contents($outputFilePath, $response);

            // Descomprimir el archivo ZIP
            $zip = new \ZipArchive();
            if ($zip->open($outputFilePath) === true) {
                // Extraer el archivo content.md
                $contentMd = $zip->getFromName('output.md');

                // Extraer las imágenes de la carpeta 'media'
                $mediaFolder = 'media/';
                $extractedImages = array();
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if (strpos($filename, $mediaFolder) === 0) {
                        $extractedImages[] = $filename;
                        $zip->extractTo($tempDir, $filename);
                    }
                }

                $zip->close();

                $comunicado = Comunicado::create([
                    "titulo" => substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10),
                    "texto" => $contentMd
                ]);

                // Copiaremos las imágenes a la carpeta de destino
                $destinationFolder = "media/comunicados/id_{$comunicado->id}";

                // Verificar si la carpeta existe en el disco 'public'
                if (!Storage::disk('public')->exists($destinationFolder)) {
                    // Crear la carpeta en el disco 'public'
                    Storage::disk('public')->makeDirectory($destinationFolder);
                }

                // reemplazar la ubicación de las imágenes en el texto del comunicado
                $comunicado->texto = preg_replace("/\bmedia\//", "$destinationFolder/", $comunicado->texto);
                $comunicado->texto = preg_replace("/\.\/media\//", "/storage/media/", $comunicado->texto);

                $comunicado->imagen = preg_replace("/\bmedia\//", "$destinationFolder/", $comunicado->imagen);
                $comunicado->imagen = preg_replace("/\.\/media\//", "/storage/media/", $comunicado->imagen);
                $comunicado->save();

                // Copiamos las imágenes a la carpeta de destino
                foreach ($extractedImages as $image) {
                    $imageFilename = basename($image);
                    // die("c.id={$comunicado->id};tempDir=$tempDir; image=$image; imageFileName=$imageFilename; dest=".public_path("storage/".$destinationFolder . "/" .  $imageFilename));
                    copy($tempDir . '/' . $image, public_path("storage/".$destinationFolder . "/" .  $imageFilename));
                }

                // Eliminar los archivos y carpetas temporales
                @unlink($outputFilePath);
                foreach ($extractedImages as $image) {
                    @unlink($tempDir . '/' . $image);
                }
                @unlink($tempDir . '/output.md');

                return response()->json([
                    "id" => $comunicado->id
                ], 200);
            } else {
                return response()->json([
                    "error" => $response
                ], 500);
            }
        } else {
            // Mostrar información sobre el error
            $error = curl_error($curl);
            $verboseInfo = curl_multi_getcontent($curl);
            return response()->json([
                "error" => "Error en la solicitud cURL: " . $error . ' response:' . $response . ' v:' . $verboseInfo
            ], 500);
        }

        return 'final';
    }
}
