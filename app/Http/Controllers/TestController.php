<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comunicado;
use App\Pigmalion\Markdown;

class TestController extends Controller
{
    //
    public function docx(Request $request)
    {

        // SELECT titulo FROM comunicados WHERE texto LIKE "%[^3]%"
        $comunicados = Comunicado::select('titulo', 'numero')
            ->where('texto', 'like', '%[^3]%')
            ->orderBy('id')
            ->paginate();

        //$docx = 'D:\tseyor.org\biblioteca\comunicados\nuevos\doc\(47) 051014.docx';
        //$md = Markdown::fromDocx($docx);
        //return Markdown::toHtml($md);
        //echo "notas: " . Markdown::$notasEncontradas;

        return view("tests.docx", ['comunicados' => $comunicados]);
    }

    public function docxShow(Request $request, $num)
    {
        $page = $request->page ?? 1;

        $comunicados = Comunicado::select('titulo', 'numero')
            ->where('texto', 'like', '%[^3]%')
            ->orderBy('id')
            ->paginate();

        $carpeta = 'D:\tseyor.org\biblioteca\comunicados\nuevos\doc';
        // buscar en $carpeta algun archivo que tenga este patrón de nombre : ($num) *.docx
        $archivo = glob("$carpeta/($num) *.docx");
        // si ha encontrado un archivo
        if ($archivo && count($archivo))
            $md = Markdown::fromDocx($archivo[0]);
         else
        $md = "<no encontrado>";

        return view("tests.docx", ['page'=>$page, 'comunicados' => $comunicados, 'md' => $md, 'html'=>Markdown::toHtml($md), 'archivo' => $archivo[0] ?? "<no encontrado>", 'notas' => Markdown::$notasEncontradas]);

    }


}
