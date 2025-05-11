<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use App\Models\Boletin;
use App\Models\Comunicado;
use App\Models\Libro;
use App\Models\Entrada;
use App\Pigmalion\Markdown;

class BoletinCrudController extends CrudController
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
        CRUD::setModel(Boletin::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/boletin');
        CRUD::setEntityNameStrings('boletín', 'boletines');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumn([
            'name' => 'id',
            'label' => 'ID',
            'type' => 'number',
        ]);

        $this->crud->addColumn([
            'name' => 'titulo',
            'label' => 'Título',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'tipo',
            'label' => 'Tipo',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'anyo',
            'label' => 'Año',
            'type' => 'number',
        ]);

        $this->crud->addColumn([
            'name' => 'mes',
            'label' => 'Mes',
            'type' => 'number',
        ]);

        $this->crud->addColumn([
            'name' => 'semana',
            'label' => 'Semana',
            'type' => 'number',
        ]);

        $this->crud->addColumn([
            'name' => 'enviado',
            'label' => 'Enviado',
            'type' => 'boolean',
        ]);

        CRUD::addButtonFromView('line', 'enviar_boletin', 'enviar_boletin', 'beginning');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation([ // Puedes agregar reglas de validación aquí
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|string',
            'texto' => 'required|string',
            'anyo' => 'required|integer',
            'mes' => 'required|integer',
            'semana' => 'required|integer',
        ]);


        CRUD::addField([   // select_from_array
            'name'        => 'tipo',
            'label'       => 'Tipo',
            'type'        => 'select_from_array',
            'options'     => Boletin::TIPOS,
            'allows_null' => false,
            'default'     => 'mensual',
            'wrapper'   => [
                'class'      => 'form-group col-md-3'
            ],
        ]);

        CRUD::field('generar')->type('generar_boletin');

        CRUD::field('titulo');


        CRUD::field('texto')->type('tiptap_editor');
        CRUD::field('anyo');
        CRUD::field('mes');
        CRUD::field('semana');
        CRUD::field('enviado')->type('boolean');
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    /**
     * Método para enviar un boletín.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enviarBoletin($id)
    {
        $boletin = Boletin::findOrFail($id);
        $boletin->enviarBoletin();

        return redirect()->back()->with('success', 'Boletín enviado correctamente.');
    }

    public function show($id)
    {
        return redirect("/boletines/$id");
    }



    public function generarBoletin(Request $request)
    {
        $tipo = $request->input('tipo');
        $hoy = now();
        $semana = $hoy->weekOfYear;
        $mes = $hoy->month;
        $anyo = $hoy->year;



        // ahora tenemos el día, mes, año y semana actuales
        // hemos de recoger los comunicados comprendidos en el rango anterior
        // si el tipo es "semanal" el rango es la semana $semana - 1
        // si el tipo es "bisemanal" el rango es la semana $semana par - 2
        // si el tipo es "mensual" el rango es el mes $mes - 1
        // si el tipo es "bimensual" el rango es el mes $mes par - 2
        // si el tipo es "trimestral" el rango es el mes $mes%3 - 3
        // proceso: tomamos la fecha de hoy, y buscamos el rango de fecha correspondiente segun el tipo y los datos que nos dan
        // pueden haber pasado unos días desde que empezó el rango actual, pero hemos de buscar el rango anterior

        if ($tipo == 'semanal') {
            $semana = $semana - 1;
            if ($semana < 1) {
                $semana = 52;
                $anyo = $anyo - 1;
            }
            $inicio = now()->startOfWeek()->subWeek();
            $fin = now()->startOfWeek()->subWeek()->endOfWeek();
            $titulo = "Boletín Tseyor semanal 2025 - semana $semana";
        } elseif ($tipo == 'bisemanal') {
            $semana = $semana - 2;
            if ($semana < 1) {
                $semana += 52;
                $anyo = $anyo - 1;
            }
            $inicio = now()->startOfWeek()->subWeeks(2);
            $fin = now()->startOfWeek()->subWeek()->endOfWeek();
            $titulo = "Boletín Tseyor 2025 - semanas $semana y " . ($semana + 1);
        } elseif ($tipo == 'mensual') {
            $mes = $mes - 1;
            if ($mes < 1) {
                $mes += 12;
                $anyo = $anyo - 1;
            }
            $inicio = now()->startOfMonth()->subMonth();
            $fin = now()->startOfMonth()->subMonth()->endOfMonth();
            $titulo = "Boletín Tseyor de " . $inicio->translatedFormat('F Y');
        } else {
            $mes = $mes - 2;
            if ($mes % 2 == 0) {
                $mes--;
            }
            if ($mes < 1) {
                $mes += 12;
                $anyo = $anyo - 1;
            }
            $inicio = now()->startOfMonth()->subMonths(2);
            $fin = $inicio->copy()->addMonth()->endOfMonth();
            $titulo = "Boletín Tseyor de " . $inicio->translatedFormat('F Y') . ' y ' .
                $fin->translatedFormat('F Y');
        }

        $comunicados = Comunicado::where('fecha_comunicado', '>=', $inicio)
            ->where('fecha_comunicado', '<=', $fin)
            ->get();
        $libros = Libro::where('created_at', '>=', $inicio)
            ->where('created_at', '<=', $fin)
            ->get();
        $entradas = Entrada::where('created_at', '>=', $inicio)
            ->where('created_at', '<=', $fin)
            ->get();

        $groups = [];

        if(count($comunicados) > 0) {
            $g = "## Comunicados\n";
            foreach ($comunicados as $comunicado) {
                // tiene que ser una url al comunicado
                $url = url("/comunicados/{$comunicado->slug}");
                $g .= "\n\n### [{$comunicado->titulo}]($url)\n\n";
                $g .= "{$comunicado->descripcion}\n\n";
            }
            $groups[] = $g;
        }
        if(count($libros) > 0) {
            $g = "## Libros\n";
            foreach ($libros as $libro) {
                $url = url("/libros/{$libro->slug}");
                $g .= "\n\n### [{$libro->titulo}]($url)\n\n";
                $g .= "{$libro->descripcion}\n\n";
            }
            $groups[] = $g;
        }
        if(count($entradas) > 0) {
            $g = "## Blog\n";
            foreach ($entradas as $entrada) {
                $url = url("/entrada/{$entrada->slug}");
                $g .= "\n\n### [{$entrada->titulo}]($url)\n\n";
                $g .= "{$entrada->descripcion}\n\n";
            }
            $groups[] = $g;
        }

        $md = "# \n\n¡Hola! Te presentamos los últimos contenidos de Tseyor.\n\n" . implode("\n\n", $groups);

        $html = Markdown::toHtml($md);

        return [
            "titulo" => $titulo,
            "texto" => $html,
            "inicio" => $inicio,
            "fin" => $fin,
            "mes" => $mes,
            "anyo" => $anyo,
            "semana" => $semana
        ];
    }
}
