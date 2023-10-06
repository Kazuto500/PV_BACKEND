<?php

namespace App\Http\Controllers;

use App\Models\AgentForm;
use App\Http\Controllers\Controller;
use App\Models\CallRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AgentFormController extends Controller
{
    public function createAgentForm(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'userFirstName' => 'required',
            'userLastName' => 'required',
            'numberContact' => 'required',
            'contactType' => 'required',
            'callResult' => 'required',
            'audioFile' => 'required',
            'comments' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $audioData = $request->input('audioFile');

        if ($audioData) {

            $audioBinaryData = base64_decode($audioData);

            $fileName = Str::random(20) . '.mp3';

            Storage::disk('ftp')->put($fileName, $audioBinaryData);

            $agentForm = AgentForm::create([
                'userFirstName' => $request->userFirstName,
                'userLastName' => $request->userLastName,
                'numberContact' => $request->numberContact,
                'contactType' => $request->contactType,
                'callResult' => $request->callResult,
                'audioFile' => $fileName,
                'comments' => $request->comments,
                'userId' => $user->id,
            ]);

            CallRecord::create([
                'clientName' => $request->userFirstName . ' ' . $request->userLastName,
                'recordingDate' => now()->toDateString(),
                'recordingTime' => now()->toTimeString(),
                'audioFile' => $fileName,
                'agentFormId' => $agentForm->id,
            ]);

            return response()->json(['data' => $agentForm], 201);
        } else {
            return response()->json(['message' => 'No se ha proporcionado ningún archivo de audio'], 400);
        }
    }
}
