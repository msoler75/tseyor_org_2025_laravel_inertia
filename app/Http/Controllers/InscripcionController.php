<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inscripcion;

class InscripcionController extends Controller
{
    //
    public function store(Request $request)
{
    $inscripcion = new Inscripcion;
    $inscripcion->name = $request->name;
    $inscripcion->day = $request->day;
    $inscripcion->month = $request->month;
    $inscripcion->year = $request->year;
    $inscripcion->city = $request->city;
    $inscripcion->region = $request->region;
    $inscripcion->email = $request->email;
    $inscripcion->phone = $request->phone;
    $inscripcion->contact = $request->contact;
    $inscripcion->agreement = $request->agreement;
    $inscripcion->save();

    return redirect()->back();
}
}
