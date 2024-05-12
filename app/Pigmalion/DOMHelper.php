<?php

namespace App\Pigmalion;

use Illuminate\Support\Facades\Log;


class DOMHelper
{

    /*
        Ejemplo de uso:

        $html = '<p>Este es un nuevo contenido.</p><p>Otro párrafo.</p>';
        $elemento = new DOMDocument()->createElement('div');
        setInnerHTML($elemento, $html);

        echo $elemento->ownerDocument->saveHTML($elemento);
    */

    public static function setInnerHTML(\DOMElement $element, $html)
    {
        if (empty(trim($html))) $html = "<i></i>";

        Log::info("setInnerHTml: " . $html);
        // Crear un nuevo DOMDocument para procesar el HTML
        $tempDoc = new \DOMDocument();
        // Establecer la codificación del documento a UTF-8
        $tempDoc->encoding = 'UTF-8';
        // Suprimir los errores de carga
        libxml_use_internal_errors(true);
        // Cargar el HTML en el documento temporal
        @$tempDoc->loadHTML('<?xml encoding="UTF-8">' . $html); // Añade un fragmento XML válido
        libxml_clear_errors();

        // Limpiar el contenido actual del elemento
        while ($element->hasChildNodes()) {
            $element->removeChild($element->firstChild);
        }

        // Copiar cada nodo del DOMDocument temporal al documento principal y luego agregarlos al elemento
        foreach ($tempDoc->documentElement->childNodes as $node) {
            $nodeCloned = $element->ownerDocument->importNode($node, true); // Importar el nodo al documento principal
            $element->appendChild($nodeCloned);
        }
    }
}
