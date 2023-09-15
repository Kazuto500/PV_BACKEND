<?php

namespace App\Http\Controllers;

use App\Models\Opdb;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpdbController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user(); // Obtener el usuario autenticado
        $opdb = Opdb::where('user_id', $user->id)->get();
        return response()->json(['data' => $opdb]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        // Acceder al usuario autenticado
        $user = User::find($id);

        // Crear una nueva instancia de Opdb
        $newOpdb = new Opdb();
        $newOpdb->opDataBase = $request->input('opDataBase');
        $newOpdb->scrip = $request->input('scrip');
        $newOpdb->otherDocs = $request->input('otherDocs');
        $newOpdb->brief = $request->input('brief');

        // Asignar el ID del usuario actual al campo user_id
        $newOpdb->user_id = $user->id;

        // Guardar la nueva caja en la base de datos
        $newOpdb->save();

        return response()
            ->json(['data' => $newOpdb]);
    }
}
