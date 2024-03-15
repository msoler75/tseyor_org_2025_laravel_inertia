<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function word(Request $request) {

        $docx = 'C:\Users\Marcel\Downloads\(1229) 231105 (1).docx';
        $md = \App\Pigmalion\Markdown::fromDocx($docx);
        return \App\Pigmalion\Markdown::toHtml($md);
    }
}
