<?php

namespace App\Http\Controllers;

use App\Models\Opdb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OpdbController extends Controller
{
    public function updateOpDataBase(Request $request)
    {
        $user = Auth::user();

        $opdb = Opdb::where('userId', $user->id)->first();

        if (!$opdb) {
            return response()->json(['message' => 'Registro Opdb no encontrado'], 404);
        }

        $file = $request->file('opDataBase');

        if ($file) {
            if ($opdb->opDataBase) {
                Storage::disk('ftp')->delete($opdb->opDataBase);
            }

            $filePath = Storage::disk('ftp')->put('/', $file);

            $opdb->opDataBase = $filePath;
            $opdb->save();

            return response()->json(['message' => 'Archivo opDataBase actualizado con éxito']);
        } else {
            return response()->json(['message' => 'No se proporcionó ningún archivo'], 400);
        }
    }

    public function updateBrief(Request $request, $id)
    {
        $opdb = Opdb::find($id);

        if (!$opdb) {
            return response()->json(['message' => 'Registro Opdb no encontrado'], 404);
        }

        $file = $request->file('brief');

        if ($file) {
            if ($opdb->brief) {
                Storage::disk('ftp')->delete($opdb->brief);
            }

            $filePath = Storage::disk('ftp')->put('/', $file);

            $opdb->brief = $filePath;
            $opdb->save();

            return response()->json(['message' => 'Archivo brief actualizado con éxito']);
        } else {
            return response()->json(['message' => 'No se proporcionó ningún archivo'], 400);
        }
    }

    public function updateOtherDocs(Request $request, $id)
    {
        $opdb = Opdb::find($id);

        if (!$opdb) {
            return response()->json(['message' => 'Registro Opdb no encontrado'], 404);
        }

        $otherDocs = $request->file('otherDocs');

        if ($otherDocs) {
            foreach ($opdb->otherDocs as $existingDoc) {
                Storage::disk('ftp')->delete($existingDoc->path);
                $existingDoc->delete();
            }

            foreach ($otherDocs as $file) {
                $filePath = Storage::disk('ftp')->put('/', $file);
                $opdb->otherDocs()->create(['path' => $filePath]);
            }

            return response()->json(['message' => 'Archivos otherDocs actualizados con éxito']);
        } else {
            return response()->json(['message' => 'No se proporcionaron archivos otherDocs'], 400);
        }
    }

    public function updateScrip(Request $request, $id)
    {
        $user = Auth::user();

        $opdb = Opdb::where('userId', $user->id)->first();

        if (!$opdb) {
            return response()->json(['message' => 'Registro Opdb no encontrado'], 404);
        }

        $newScrip = $request->input('scrip');

        $opdb->scrip = $newScrip;
        $opdb->save();

        return response()->json(['message' => 'Campo "scrip" actualizado con éxito']);
    }

    public function downloadOpDataBase()
    {
        $user = Auth::user();

        $opdb = Opdb::where('userId', $user->id)->first();

        if (!$opdb) {
            return response()->json(['message' => 'No se encontró un registro Opdb asociado a este usuario'], 404);
        }

        $filePath = $opdb->opDataBase;

        if (Storage::exists($filePath)) {
            return response()->download(storage_path('app/' . $filePath));
        } else {
            return response()->json(['message' => 'El archivo no existe en el almacenamiento'], 404);
        }
    }

    public function downloadBrief()
    {
        $user = Auth::user();

        $opdb = Opdb::where('userId', $user->id)->first();

        if (!$opdb) {
            return response()->json(['message' => 'No se encontró un registro Opdb asociado a este usuario'], 404);
        }

        $filePath = $opdb->brief;

        if (Storage::exists($filePath)) {
            return response()->download(storage_path('app/' . $filePath));
        } else {
            return response()->json(['message' => 'El archivo no existe en el almacenamiento'], 404);
        }
    }

    public function downloadOtherDocs()
    {
        $user = Auth::user();

        $opdb = Opdb::where('userId', $user->id)->first();

        if (!$opdb) {
            return response()->json(['message' => 'No se encontró un registro Opdb asociado a este usuario'], 404);
        }

        $otherDocs = $opdb->otherDocs;

        if ($otherDocs->isEmpty()) {
            return response()->json(['message' => 'No hay archivos "otherDocs" disponibles para descargar'], 404);
        }

        $zipFileName = 'otherDocs_' . $opdb->id . '.zip';

        $zip = new \ZipArchive();
        $zip->open($zipFileName, \ZipArchive::CREATE);

        foreach ($otherDocs as $otherDoc) {
            $filePath = $otherDoc->path;
            $fileContent = Storage::get($filePath);
            $zip->addFromString(basename($filePath), $fileContent);
        }

        $zip->close();

        return response()->download($zipFileName, $zipFileName)->deleteFileAfterSend();
    }
}
