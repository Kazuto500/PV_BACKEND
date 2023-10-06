<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Opdb;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdministrationController extends Controller
{
    public function registerAgent(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'countryCode' => 'required',
            'dialCode' => 'required',
            'telephone' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $userAgent = User::create([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'countryCode' => $request->countryCode,
            'dialCode' => $request->dialCode,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $agent = new Agent();
        $agent->userId = $userAgent->id;
        $agent->agentName = $request->firstName . ' ' . $request->lastName;
        $agent->dateRegister = now();
        $agent->state = false;
        $agent->adminName = $user->firstName . ' ' . $request->lastName;
        $agent->save();

        $userAgent = User::find($userAgent->id);

        return response()
            ->json(['data' => $userAgent]);
    }

    public function toggleStateAgent()
    {
        $user = Auth::user();

        $agent = Agent::where('userId', $user->id)->first();

        if (!$agent) {
            return response()->json(['message' => 'Agente no encontrada para este usuario'], 404);
        }

        $agent->update(['state' => !$agent->state]);

        return response()->json(['message' => 'Estado de agente cambiado exitosamente', 'new_state' => $agent->state]);
    }

    public function updateScrip(Request $request)
    {
        $user = Auth::user();

        $opdb = Opdb::where('userId', $user->id)->first();

        if (!$opdb) {
            return response()->json(['message' => 'Registro Opdb no encontrado'], 404);
        }

        $opdb->scrip = $request->input('scrip');

        $opdb->save();

        return response()->json(['message' => 'Campo "scrip" actualizado con éxito', 'data' => $opdb]);
    }

    public function updateBrief(Request $request)
    {
        $user = Auth::user();

        $opdb = Opdb::where('userId', $user->id)->first();

        if (!$opdb) {
            return response()->json(['message' => 'Registro Opdb no encontrado'], 404);
        }

        $opdb->brief = $request->input('brief');

        $opdb->save();

        return response()->json(['message' => 'Campo "brief" actualizado con éxito', 'data' => $opdb]);
    }

    public function updateOpDataBase(Request $request, $id)
    {
        $user = Auth::user();

        $opdb = Opdb::where('id', $id)->where('userId', $user->id)->first();

        if (!$opdb) {
            return response()->json(['message' => 'Registro Opdb no encontrado'], 404);
        }

        $fileData = $request->input('fileData');

        if ($fileData) {
            $fileBinaryData = base64_decode($fileData);

            $fileName = uniqid() . '.bin';

            Storage::disk('ftp')->put($fileName, $fileBinaryData);

            $opdb->opDataBase = $fileName;

            $opdb->save();

            return response()->json(['message' => 'Campo "opDataBase" actualizado con éxito', 'data' => $opdb]);
        } else {
            return response()->json(['message' => 'No se ha proporcionado ningún archivo'], 400);
        }
    }

    public function updateOtherDocs(Request $request, $id)
    {
        $user = Auth::user();

        $opdb = Opdb::where('id', $id)->where('userId', $user->id)->first();

        if (!$opdb) {
            return response()->json(['message' => 'Registro Opdb no encontrado'], 404);
        }

        $otherDocsData = $request->input('otherDocsData');

        if ($otherDocsData) {
            $opdb->otherDocs()->delete();

            foreach ($otherDocsData as $otherDocData) {
                $otherDocBinaryData = base64_decode($otherDocData);

                $otherDocFileName = uniqid() . '.bin';

                Storage::disk('ftp')->put($otherDocFileName, $otherDocBinaryData);

                $opdb->otherDocs()->create(['path' => $otherDocFileName]);
            }

            return response()->json(['message' => 'Campo "otherDocs" actualizado con éxito', 'data' => $opdb]);
        } else {
            return response()->json(['message' => 'No se ha proporcionado ningún archivo'], 400);
        }
    }
}
