<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\TwoFactorCodeNotification;
use App\Mail\VerifyEmail;
use App\Models\Campaign;
use App\Models\Opdb;
use App\Models\UserInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function user()
    {
        $user = Auth::user();
        return response()->json($user);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'companyName' => 'required',
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

        $user = User::create([
            'companyName' => $request->companyName,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'countryCode' => $request->countryCode,
            'dialCode' => $request->dialCode,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $userInfo = new UserInfo([
            'userId' => $user->id,
            'companyName' => $request->companyName,
            'userFirstAndLastName' => $request->firstName . ' ' . $request->lastName,
            'userEmail' => $request->email,
        ]);
        $userInfo->save();

        $opdb = new Opdb([
            'userId' => $user->id,
            'opDataBase' => '',
            'scrip' => '',
            'otherDocs' => '',
            'brief' => ''
        ]);
        $opdb->save();

        $user->createAsStripeCustomer();

        // Mail::to($user->email)->send(new VerifyEmail($user));

        $campaign = new Campaign();
        $campaign->userId = $user->id;
        $campaign->campaignName = $request->companyName;
        $campaign->createPlanDate = now();
        // $campaign->planName = 'Nombre del Plan';
        $campaign->state = false;
        $campaign->save();

        $user = User::find($user->id);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['data' => $user, 'access_token' => $token]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        if (!$user->activo) {
            Auth::logout();
            return response()->json(['message' => 'Tu cuenta está desactivada. Contacta al administrador.'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'data' => $user
        ]);
    }


    public function logout()
    {
        auth()->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return [
            'message' => 'You have successfully logged out and the token was succesfully deleted'
        ];
    }

    public function settingsProfilePhoto(Request $request)
    {
        $user = Auth::user();
        $file = $request->file('profilePhoto');

        if ($file) {
            $ftpDisk = Storage::disk('ftp');
            $filePath = 'profilePhotos/' . $user->id . '/' . $file->getClientOriginalName();
            $ftpDisk->put($filePath, file_get_contents($file));

            DB::table('users')
                ->where('id', $user->id)
                ->update(['profilePhoto' => $filePath]);

            return response()->json(['message' => 'Foto de perfil actualizada correctamente']);
        } else {
            return response()->json(['message' => 'No se ha proporcionado ninguna foto de perfil'], 400);
        }
    }

    public function settingsPersonalDetails(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'countryCode' => 'required',
            'dialCode' => 'required',
            'telephone' => 'required',
        ]);

        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'firstName' => $request->input('firstName'),
                'lastName' => $request->input('lastName'),
                'countryCode' => $request->input('countryCode'),
                'dialCode' => $request->input('dialCode'),
                'telephone' => $request->input('telephone'),
            ]);

        $user = User::find($user->id);

        return response()->json(['data' => $user]);
    }

    public function settingsPasswordUpdate(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'currentPassword' => 'required',
            'password' => 'required',
        ]);

        if (Hash::check($request->currentPassword, $user->password)) {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'password' => Hash::make($request->password),
                ]);

            $user = Auth::user();

            return response()->json(['data' => $user], 200);
        } else {
            return response()->json(['message' => 'La contraseña actual no es válida'], 422);
        }
    }

    public function generateTwoFactorCode(Request $request)
    {
        $user = Auth::user();

        if (!$user->authenticationEnabled) {
            return response()->json(['message' => 'La autenticación de dos pasos no está habilitada para este usuario.'], 400);
        }

        $code = Str::random(6);

        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'authenticationCode' => Hash::make($code),
            ]);

        Mail::to($user->email)->send(new TwoFactorCodeNotification($code));


        return response()->json(['message' => 'Código de autenticación generado y enviado.']);
    }


    public function enableTwoFactorAuthentication(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'verificationCode' => 'required',
        ]);

        $authenticationCode = DB::table('users')->where('id', $user->id)->value('authenticationCode');

        if (!password_verify($request->verificatioCode, $authenticationCode)) {
            return response()->json(['message' => 'El código de verificación es incorrecto.'], 400);
        }

        DB::table('users')->where('id', $user->id)->update(['authenticationEnabled' => true]);

        $user = Auth::user();

        return response()->json(['message' => 'Autenticación de dos pasos habilitada para el usuario.', 'data' => $user]);
    }

    public function verifyEmail(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        DB::table('users')->where('id', $user->id)->update(['emailVerifiedAt' => now()]);

        return response()->json(['message' => 'Correo electrónico verificado con éxito']);
    }
}
