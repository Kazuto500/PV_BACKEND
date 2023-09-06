<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Stripe\Stripe;
use Stripe\Exception\CardException;

class AuthController extends Controller
{

    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'companyName' => 'required',
            'firstName' => 'required',
            'lastName' => 'required',
            'telephone' => 'required',
            'email' => 'required',
            'password' => 'required',
            "role" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'companyName' => $request->companyName,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $user->createAsStripeCustomer();

        //event(new Registered($user));

        $user = User::find($user->id);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['data' => $user, 'access_token' => $token]);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'accessToken' => $token,
            'user' => $user
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return [
            'message' => 'You have successfully logged out and the token was succesfully deleted'
        ];
    }

    public function settingsProfilePhotoUrl(Request $request, $id)
    {
        $user = User::find($id);

        $request->validate([
            'profilePhotoUrl' => 'required'
        ]);

        $user->profilePhotoUrl = $request->input('profilePhotoUrl');

        $user->save();

        return response()->json(['data' => $user]);
    }

    public function settingsPersonalDetails(Request $request, $id)
    {
        $user = User::find($id);

        $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'telephone' => 'required',
        ]);

        $user->firstName = $request->input('firstName');
        $user->lastName = $request->input('lastName');
        $user->telephone = $request->input('telephone');

        $user->save();

        return response()->json(['data' => $user]);
    }

    public function settingsPasswordUpdate(Request $request, $id)
    {
        $user = User::find($id);

        $request->validate([
            'current_password' => 'required',
            'password' => 'required',
        ]);

        if (Hash::check($request->current_password, $user->password)) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return response()->json(['data' => $user], 200);
        } else {
            return response()->json(['message' => 'La contraseña actual no es válida'], 422);
        }
    }
}
