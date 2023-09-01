<?php

namespace App\Http\Controllers;

use App\Models\Opdb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpdbController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Acceder al usuario autenticado
        $user = Auth::user();

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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Opdb $opdb)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Opdb $opdb)
    {
        //
    }
}
