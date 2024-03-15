<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function word(Request $request) {

        $docx = 'C:\Users\Marcel\Downloads\demo.docx';
        $md = \App\Pigmalion\Markdown::fromDocx($docx);

        // dd($md);

        return \App\Pigmalion\Markdown::toHtml($md);
    }
}
