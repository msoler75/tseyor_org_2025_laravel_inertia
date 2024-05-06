<?php

//NO FUNCIONA


 require __DIR__.'/../../vendor/autoload.php';

use App\Pigmalion\Markdown;

$html = file_get_contents(__DIR__ . "/inputImg.html");

$md = Markdown::toMarkdown($html);

echo substr($md, 0, 1024) . "...";
