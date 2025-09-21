<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InspeccionesController extends Controller
{
    public function index()
    {
        return view('gestion-inspecciones');
    }
}