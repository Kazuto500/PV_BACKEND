<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

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
            "passwordConfirm" => "required|same:password",
        ]);

        if ($validator->fails() || $request->password !== $request->passwordConfirm) {
            $errors = $validator->errors()->toArray();
            if ($request->password !== $request->passwordConfirm) {
                $errors['passwordConfirm'] = ['The passwords do not match'];
            }
            return response()->json(['errors' => $errors], 400);
        }

        $user = User::create([
            'companyName' => $request->companyName,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $user->assignRole('user');

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['data' => $user, 'access_token']);
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
}
