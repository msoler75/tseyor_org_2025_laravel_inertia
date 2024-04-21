<?php

require __DIR__ . "/../../App/Pigmalion/Markdown.php";

use App\Pigmalion\Markdown;

$html = file_get_contents(__DIR__ . "/inputImg.html");

$html = Markdown::limpiarHtml($html);

echo substr($html, 0, 1024) . "...";
