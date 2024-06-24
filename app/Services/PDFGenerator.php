<?php


namespace App\Services;

use App\Models\ContenidoBaseModel;
use App\Pigmalion\DiskUtil;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFGenerator {

    /**
     * Genera el pdf de un contenido
     * @param ContenidoBaseModel $contenido
     * @return \Illuminate\Http\Response
     */
    public static function generatePdf(ContenidoBaseModel $contenido, string $vista = 'contenido-pdf')
    {
        $nombreArchivo = ($contenido->titulo ?? $contenido->nombre) . ' - TSEYOR.pdf';

        // comprobaremos si existe ya el archivo pdf generado

        $pdf_path = $contenido->pdfPath; // attribute with accesor

        $pdf_full_path = Storage::disk('public')->path($pdf_path);

        $headers = [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        // dd(dirname($pdf_full_path));
        DiskUtil::ensureDirExists(dirname($pdf_full_path));

        if (file_exists($pdf_full_path) && filemtime($pdf_full_path) > $contenido->updated_at->getTimestamp()) {
            return response()->file($pdf_full_path, $headers);
        }

        $texto = $contenido->texto;

        // reducimos las imagenes de los guías para que no sean tan grandes
        $texto = preg_replace_callback("#\!\[\]\(\/almacen\/medios\/guias\/con_nombre\/.+?\.jpg\)\{width=(\d+),height=(\d+)\}#", function ($match) {
            $w = intval($match[1]);
            $h = intval($match[2]);
            $r = $w / $h;
            $h = 250;
            $w = $h * $r;
            return str_replace("width=" . $match[1] . ",height=" . $match[2], "width=$w,height=$h", $match[0]);
        }, $texto);


        $html = \App\Pigmalion\Markdown::toHtml($texto);

        $startP = strpos($html, "<p");
        $previousHasImage = false;
        $previousStartP = 0;
        while ($startP !== false) {
            $endP = strpos($html, "</p>", $startP);
            $part = substr($html, $startP, $endP - $startP + 4);
            // echo "($startP, $endP) ".str_replace("<", "&lt;", $part)."<hr>";
            if (strpos($part, "<img") !== false) {
                $partReplace = "<p has-image " . substr($part, 2);
                $html = str_replace($part, $partReplace, $html);
                $part = $partReplace;
                $thisHasImage = true;
            } else
                $thisHasImage = false;


            // aquí comprobamos si este párrafo p, además de tener una imagen, tiene texto <em>
            if($thisHasImage && preg_match("/<em>(.*)<\/em>/", $part, $matches)) {
                $nota = $matches[1];
                preg_match("/<img [^>]+>/", $part, $matches);
                $img = $matches[0];
                $html = str_replace($part, "<p has-image>$img</p><p image-note>$nota</p>", $html);
                $thisHasImage = false; // borramos el flag
            }

            // genera un element figure con la imagen y el pie de foto
            else if ($previousHasImage && !$thisHasImage) {
                $endTag = strpos($part, ">");
                $ppart = substr($part, 0, $endTag);
                if (preg_match('/style=.+text-align:\s*center/', $ppart)) {
                    $bothParts = substr($html, $previousStartP, $endP - $previousStartP + 4);
                    // si termina en < lo quitamos
                    if(substr($bothParts, -1) === "<")
                        $bothParts = substr($bothParts, 0, -1);
                    preg_match("/<img [^>]+>/", $bothParts, $matches);
                    $img = $matches[0];
                    preg_match("/<p[^>]*>(.*)<\/p>/", $part, $matches);
                    $nota = $matches[1];
                    // echo str_replace("<", "&lt;", $bothParts)."<hr>";
                    $html = str_replace($bothParts, "<p has-image>$img</p><p image-note>$nota</p>", $html);
                }
            }

            $previousHasImage = $thisHasImage;
            $previousStartP = $startP;
            $startP = strpos($html, "<p", $startP + 1);
        }

        // dd($html);

        // para que procese las imagenes en el pdf:
        // método 1: reemplazar todas las imagenes sus rutas relativas con rutas absolutas de disco (NO FUNCIONA)
        // método 2: codificarlas en base64 (ACTUAL MÉTODO)
        $html = preg_replace_callback('/<img([^>]+)src="([^">]+)"/', function ($matches) {

            if (preg_match("/^https?:\/\//", $matches[2])) {
                $url = $matches[2];
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                // curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
                // seguir redirecciones:
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                $raw = curl_exec($ch);
                curl_close($ch);
                // si hay error descargando la imagen, reintentamos

                //   codificamos el contenido de la imagen en base64
                return '<img' . $matches[1] . 'src="data:image/png;base64,' . base64_encode($raw) . '"';
            }

            $fullpath = DiskUtil::getRealPath(urldecode($matches[2]));
            //dd($matches);
            // $prefix = ""; // "file://";
            // $r = '<img' . $matches[1] . 'src="' . $prefix.$fullpath .'"'; // método 1
            // dd($fullpath);
            if (!$fullpath) {
                return $matches[0];
            }
            $r = '<img' . $matches[1] . 'src="data:image/png;base64,' . base64_encode(file_get_contents($fullpath)) . '"'; // método 2
            return $r;
        }, $html);

        // Contenido HTML completo con etiquetas <html>, <head> y <body>
        $pdf = Pdf::loadView($vista, [
            'titulo' => $contenido->titulo ?? $contenido->nombre,
            'texto' => $html,
        ]);

        // guardamos el pdf generado
        DiskUtil::ensureDirExists(dirname($pdf_full_path));
        $pdf->save($pdf_full_path);

        // descargamos el archivo pdf
        // return $pdf->download($nombreArchivo);

        // mostramos el contenido del pdf en la página
        // return $pdf->stream($nombreArchivo);
        return response($pdf->stream($nombreArchivo), 200, $headers);
    }


}
