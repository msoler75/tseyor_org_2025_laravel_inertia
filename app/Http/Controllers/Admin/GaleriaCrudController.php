<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Nodo;
use App\Models\Galeria;
use App\Models\GaleriaItem;

/**
 * Class GaleriaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class GaleriaCrudController extends CrudController
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
        \Log::info('=== SETUP GaleriaCrudController ===');

        CRUD::setModel(\App\Models\Galeria::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/galeria');
        CRUD::setEntityNameStrings('galeria', 'galerias');

        // Cargar relaciones necesarias
        $this->crud->with(['items.nodo', 'items.user']);

        // Agregar ruta personalizada para escanear carpeta
        $this->crud->allowAccess('scan');
        $this->crud->operation('scan', function() {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-list
     * @return void
     */
    protected function setupListOperation()
    {
        //CRUD::column('created_at')->label('Creado')->type('datetime');
        CRUD::column('id')->label('id')->type('number');
        CRUD::column('updated_at')->label('Actualizado')->type('datetime');
        CRUD::column('titulo')->label('Título');
        //CRUD::column('slug')->label('Slug');
        // CRUD::column('descripcion')->label('Descripción');
        CRUD::column('imagen')->label('Imagen')->type('image')
            // ->prefix('archivos/')
            ->height('60px');
        CRUD::column('ruta')->label('Ruta');
    }

    public function scan($id)
    {
        $galeria = Galeria::findOrFail($id);

        try {
            $galeria->releerCarpeta();

            return response()->json([
                'success' => true,
                'message' => 'Carpeta escaneada correctamente. ' . $galeria->items()->count() . ' items encontrados.',
                'items_count' => $galeria->items()->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al escanear la carpeta: ' . $e->getMessage()
            ], 500);
        }
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
            'titulo' => 'required|string|max:255',
            'slug' => ['nullable', 'regex:/^[a-z0-9\-]+$/', \Illuminate\Validation\Rule::unique('galerias', 'slug')->ignore(request()->id)],
            'descripcion' => 'nullable|string',
            'ruta' => 'required|string|max:500',
        ]);
        CRUD::field('titulo');
        CRUD::addField([
            'name' => 'slug',
            'label' => 'Slug',
            'type' => 'text',
            'hint' => 'Opcional. Si se deja vacío se genera automáticamente a partir del título.',
        ]);
        CRUD::field('descripcion');
        CRUD::field('ruta');

        // Hook para procesar items después de crear
        $this->crud->operation('create', function() {
            $this->crud->setOperationSetting('success', function($entry) {
                $this->processItemsData($entry);
                return $entry;
            });
        });
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        CRUD::setValidation([
            'titulo' => 'required|string|max:255',
            'slug' => ['nullable', 'regex:/^[a-z0-9\-]+$/', \Illuminate\Validation\Rule::unique('galerias', 'slug')->ignore(request()->id)],
            'descripcion' => 'nullable|string',
            'ruta' => 'required|string|max:500',
            'assign_user_to_all' => 'nullable|exists:users,id',
        ]);

        CRUD::field('titulo');
        CRUD::addField([
            'name' => 'slug',
            'label' => 'Slug',
            'type' => 'text',
            'hint' => 'Opcional. Si se deja vacío se genera automáticamente a partir del título.',
        ]);
        CRUD::field('descripcion');
        CRUD::field('ruta');

        // Campo personalizado para gestionar items de la galería
        CRUD::addField([
            'name' => 'galeria_items',
            'label' => 'Items de la Galería',
            'type' => 'view',
            'view' => 'vendor.backpack.crud.fields.galeria_items',
            'attributes' => [
                'users' => \App\Models\User::select('id', 'name')->get(),
            ],
        ]);

        // Campo para asignar usuario a todos los items
        CRUD::addField([
            'name' => 'assign_user_to_all',
            'label' => 'Asignar usuario a todos los items',
            'type' => 'select',
            'model' => 'App\Models\User',
            'attribute' => 'name',
            'allows_null' => true,
        ]);
    }

    /**
     * Override the update method to process items data after update
     */
    public function update($id = null)
    {
        \Log::info('=== INICIO UPDATE GaleriaCrudController ===', ['id' => $id, 'request_data' => request()->all()]);

        $this->crud->hasAccessOrFail('update');
        \Log::info('Access check passed');

        $this->crud->setOperation('update');
        \Log::info('Operation set to update');

        // Obtener datos del request sin validación automática
        $data = request()->only(['titulo', 'descripcion', 'ruta', 'assign_user_to_all']);

        \Log::info('Datos del request:', $data);

        $this->crud->update($id, $data);
        \Log::info('CRUD update executed');

        $response = $this->crud->performSaveAction($id);
        \Log::info('Perform save action executed, response status: ' . $response->getStatusCode());

        // Procesar items después de la actualización exitosa
        $entry = $this->crud->getCurrentEntry();
        \Log::info('Current entry ID: ' . ($entry ? $entry->id : 'null'));
        if ($entry) {
            \Log::info('Procesando items para galería ID: ' . $entry->id);
            $this->processItemsData($entry);
        }

        \Log::info('=== FIN UPDATE GaleriaCrudController ===');
        return $response;
    }

    /**
     * Procesar los datos de items de la galería
     */
    private function processItemsData($entry)
    {
        \Log::info('=== PROCESANDO ITEMS para galería ID: ' . $entry->id . ' ===');

        $request = request();
        $allData = $request->all();

        \Log::info('Todos los datos del request:', $allData);

        // Buscar datos de items en el request
        if (isset($allData['items']) && is_array($allData['items'])) {
            foreach ($allData['items'] as $itemId => $itemData) {
                \Log::info("Procesando item ID: {$itemId}", $itemData);
                $item = GaleriaItem::find($itemId);
                if ($item && $item->galeria_id == $entry->id) {
                    $oldDescripcion = $item->descripcion;
                    $item->update([
                        'titulo' => $itemData['titulo'] ?? '',
                        'descripcion' => $itemData['descripcion'] ?? '',
                        'orden' => $itemData['orden'] ?? null,
                        'user_id' => $itemData['user_id'] ?? null,
                    ]);
                    \Log::info("Item {$itemId} actualizado. Descripción anterior: '{$oldDescripcion}' -> Nueva: '{$itemData['descripcion']}'");
                } else {
                    \Log::info("Item {$itemId} no encontrado o no pertenece a la galería");
                }
            }
        } else {
            \Log::info('No se encontraron datos de items para procesar');
        }

        // Procesar selección de imagen de portada
        if ($request->has('portada_item') && $request->input('portada_item')) {
            $portadaItemId = $request->input('portada_item');
            $portadaItem = GaleriaItem::find($portadaItemId);

            if ($portadaItem && $portadaItem->galeria_id == $entry->id && $portadaItem->nodo) {
                $entry->imagen = $portadaItem->nodo->ubicacion;
                $entry->save();
                \Log::info('Imagen de portada actualizada: ' . $portadaItem->nodo->ubicacion);
            }
        } else {
            // Si no se seleccionó ninguna portada, quitar la imagen
            $entry->imagen = null;
            $entry->save();
            \Log::info('Imagen de portada removida');
        }
    }

    private function scanAndCreateItems($galeria)
    {
        $path = $galeria->ruta;
        if (!$path) return;

        // Buscar nodos en la carpeta
        $nodos = Nodo::where('ubicacion', 'like', $path . '/%')
                     ->where('es_carpeta', 0)
                     ->get();

        foreach ($nodos as $nodo) {
            // Verificar si ya existe un item para este nodo en la galeria
            $existing = GaleriaItem::where('galeria_id', $galeria->id)
                                   ->where('nodo_id', $nodo->id)
                                   ->first();
            if (!$existing) {
                GaleriaItem::create([
                    'galeria_id' => $galeria->id,
                    'nodo_id' => $nodo->id,
                    'descripcion' => '',
                    'user_id' => null,
                    'orden' => null,
                ]);
            }
        }
    }

    public function scanFolder($id)
    {
        $galeria = Galeria::findOrFail($id);

        try {
            $galeria->releerCarpeta();

            return response()->json([
                'success' => true,
                'message' => 'Carpeta escaneada correctamente. ' . $galeria->items()->count() . ' items encontrados.',
                'items_count' => $galeria->items()->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al escanear la carpeta: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        return redirect("/galerias/$id");
    }

}
