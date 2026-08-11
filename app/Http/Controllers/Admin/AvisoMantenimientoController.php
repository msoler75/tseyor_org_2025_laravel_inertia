<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use App\Services\OpenRouterService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

class AvisoMantenimientoController extends CrudController
{
    public function setup()
    {
        CRUD::setModel(Setting::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/aviso-mantenimiento');
        CRUD::setEntityNameStrings('aviso de mantenimiento', 'avisos de mantenimiento');
    }

    public function edit()
    {
        if (! backpack_user()) {
            abort(403, 'Acceso denegado');
        }

        $setting = Setting::firstOrNew(
            ['name' => 'aviso_mantenimiento'],
            [
                'description' => 'Aviso de mantenimiento programado',
                'value' => '{}',
            ]
        );

        $data = json_decode($setting->value, true) ?: [];

        $this->data['entry'] = $setting;
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = 'Editar aviso de mantenimiento';
        $this->data['aviso'] = $data;
        $this->data['breadcrumbs'] = [
            trans('backpack::crud.admin') => backpack_url('dashboard'),
            'Aviso de mantenimiento' => false,
        ];

        return view('admin.aviso_mantenimiento', $this->data);
    }

    public function update(Request $request)
    {
        if (! backpack_user()) {
            abort(403, 'Acceso denegado');
        }

        $setting = Setting::firstOrNew(['name' => 'aviso_mantenimiento']);

        if ($request->has('clear')) {
            $setting->value = '{}';
            $setting->description = 'Aviso de mantenimiento programado';
            $setting->save();
            \Alert::success('Aviso de mantenimiento eliminado.')->flash();

            return redirect()->to(backpack_url('aviso-mantenimiento/edit'));
        }

        $data = [
            'titulo' => $request->input('titulo', ''),
            'descripcion' => $request->input('descripcion', ''),
            'inicio' => $request->input('inicio'),
            'fin' => $request->input('fin'),
            'zona_horaria_original' => $request->input('zona_horaria_original', ''),
            'duracion_estimada' => $request->input('duracion_estimada', ''),
            'url_info' => $request->input('url_info', ''),
            'raw_email_text' => $request->input('raw_email_text', ''),
        ];

        $data = array_filter($data, fn ($v) => $v !== null && $v !== '');

        $setting->description = $data['titulo'] ?: 'Aviso de mantenimiento programado';
        $setting->value = json_encode($data, JSON_UNESCAPED_UNICODE);
        $setting->save();

        \Alert::success('Aviso de mantenimiento guardado.')->flash();

        return redirect()->to(backpack_url('aviso-mantenimiento/edit'));
    }

    public function analizarEmail(Request $request)
    {
        if (! backpack_user()) {
            abort(403, 'Acceso denegado');
        }

        $rawText = $request->input('raw_text', '');

        if (empty($rawText)) {
            return response()->json(['error' => 'Texto vacío'], 400);
        }

        try {
            $service = app(OpenRouterService::class);
            $resultado = $service->analizarEmailMantenimiento($rawText);

            return response()->json([
                'mantenimiento' => $resultado,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
