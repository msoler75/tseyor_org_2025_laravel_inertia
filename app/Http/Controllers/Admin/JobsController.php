<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\Comunicado;
use App\Models\Informe;
use App\Jobs\ProcesarAudios;

class JobsController extends Controller
{
    /**
     * Reencola todas las tareas fallidas
     * @return \Illuminate\Http\Response
     */
    public function retryFailedJobs()
    {
        Artisan::call('queue:retry all');
        Alert::add('success', 'Todas las tareas han sido encoladas')->flash();
        return redirect()->to('/admin/job-failed');
    }


    /**
     * Reencola una tarea fallida
     * @return \Illuminate\Http\Response
     */
    public function retryJob($id)
    {
        Artisan::call('queue:retry ' . $id);
        Alert::add('success', 'La tarea $id ha sido encoladas')->flash();
        return redirect()->to('/admin/job-failed');
    }


    /**
     * Elimina tareas fallidas
     * @return \Illuminate\Http\Response
     */
    public function flushJobs() {
        Artisan::call('queue:flush');
        Alert::add('success', 'Tareas eliminadas')->flash();
        return redirect()->to('/admin/job-failed');
    }


    /**
     * Detecta contenidos con audios a procesar y crea las jobs
     * @return \Illuminate\Http\Response
     */
    public function detectAudiosToProcess() {
        $tareas = 0;
        // SELECT id, titulo, audios FROM `comunicados` WHERE audios LIKE '%upload%';
        $comunicados = Comunicado::select(['id', 'fecha_comunicado'])->where('audios', 'LIKE', '%upload%')->get();
        foreach($comunicados as $comunicado) {
            $año = date('Y', strtotime($comunicado->fecha_comunicado));
            $folder = "/almacen/medios/comunicados/audios/$año";
            dispatch(new ProcesarAudios(Comunicado::class, $comunicado->id, $folder));
            $tareas++;
        }

        $informes = Informe::where('audios', 'LIKE', '%upload%')->with('equipo')->get();
        foreach($informes as $informe) {
            $año = $informe->created_at->year;
            $folder = "/almacen/medios/informes/audios/{$informe->equipo->slug}/$año/{$informe->id}";
            dispatch(new ProcesarAudios(Informe::class, $informe->id, $folder));
            $tareas++;
        }

        Alert::add('success', "Se han agregado $tareas tareas")->flash();
        return redirect()->to('/admin/job');
    }

}
