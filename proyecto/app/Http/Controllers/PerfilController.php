<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    public function index()
    {
        // Obtener la información del usuario autenticado
        $usuario = Auth::user();
        
        // Pasar el objeto de usuario a la vista
        return view('perfil', compact('usuario'));
    }
}