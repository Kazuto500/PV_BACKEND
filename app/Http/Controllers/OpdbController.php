<?php

namespace App\Http\Controllers;

use App\Models\Opdb;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OpdbController extends Controller
{
    public function upload(Request $request)
    {
        $user = Auth::user(); // Obtener el usuario autenticado

        $file = $request->file('file');

        if ($file) {

            $filePath = $file->store('uploads'); // Almacena el archivo en la carpeta 'uploads'

            $newFile = new Opdb();
            $newFile->opDataBase = $filePath; // Almacena la ruta/nombre del archivo
            $newFile->scrip = $request->input('scrip');
            $newFile->otherDocs = $request->input('otherDocs'); // Almacena la ruta/nombre del archivo
            $newFile->brief = $request->input('brief');
            $newFile->userId = $user->id;
            $newFile->save();

            return response()
                ->json(['data' => $newFile]);
        } else {
            return response()->json(['message' => 'No se ha proporcionado ningún archivo'], 400);
        }
    }

    public function download($id)
    {
        $file = Opdb::find($id);

        if ($file) {
            $user = Auth::user();
            if ($file->userId === $user->id) {
                $filePath = $file->opDataBase; // Ruta o nombre del archivo almacenado

                // Verifica si el archivo existe en el almacenamiento
                if (Storage::exists($filePath)) {
                    $fileContent = Storage::get($filePath);

                    // Devuelve el archivo como respuesta
                    return response(['data' => $fileContent, 200]);
                } else {
                    return response()->json(['message' => 'El archivo no existe en el almacenamiento'], 404);
                }
            } else {
                return response()->json(['message' => 'No tienes permiso para descargar este archivo'], 403);
            }
        } else {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }
    }
}
