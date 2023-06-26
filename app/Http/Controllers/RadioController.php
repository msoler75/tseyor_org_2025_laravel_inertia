<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Evento;
use App\Pigmalion\SEO;

class RadioController extends Controller
{
    public function index()
    {
        return Inertia::render('Radio/Index')
        ->withViewData(SEO::get('radio'));
    }
}
