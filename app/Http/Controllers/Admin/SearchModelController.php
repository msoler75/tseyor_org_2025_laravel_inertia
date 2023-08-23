<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Database\Schema\Builder;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class SearchModelController extends Controller
{
    public function index(Request $request, string $model)
    {
        // to-do: añadir seguridad (csrf?)

        /* if (Gate::denies('buscar', $nodo, $acl)) {
            throw new AuthorizationException('No tienes permisos para ver la carpeta', 403);
        }
        */

        $query = $request->input('q');

        $model = ucfirst($model);

        // Verificar si el modelo es válido (por seguridad)
        $validModels = ['Libro', 'Grupo', 'Equipo', 'Nodo', 'User']; // Agrega aquí los nombres de los modelos válidos para la búsqueda
        if (!in_array($model, $validModels)) {
            return response()->json(['error' => 'Modelo no válido'], 400);
        }

        // Obtener el nombre de la clase completa del modelo
        $modelClass = $this->resolveClassName($model);

        // Verificar si los campos existen en el modelo
        $fields = ['title', 'name', 'titulo', 'nombre', 'apellidos', 'direccion', 'ruta'];
        $existingFields = $this->getExistingFields($modelClass, $fields);
        if (empty($existingFields)) {
            return response()->json(['error' => 'El modelo no tiene campos mostrables'], 400);
        }

        // Buscar en el modelo indicado
        $queryBuilder = $modelClass::query();
        foreach ($existingFields as $field) {
            $queryBuilder->orWhere($field, 'like', "%$query%");
        }
        $results = $queryBuilder->take(128)->get();

        // Devolver los resultados en JSON
        return response()->json(['results' => $results]);
    }

    protected function resolveClassName(string $model): string
    {
        return 'App\\Models\\' . $model;
    }

    protected function getExistingFields(string $modelClass, array $fields): array
    {
        $existingFields = [];
        $table = app($modelClass)->getTable();
        $schema = app(Builder::class)->getColumnListing($table);
        foreach ($fields as $field) {
            if (in_array($field, $schema)) {
                $existingFields[] = $field;
            }
        }
        return $existingFields;
    }
}
