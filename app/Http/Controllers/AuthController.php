<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request) {

        $credentials = $request->validate([
            'name' => 'required',
            'email' => 'email|required',
            'password' => 'required'
        ]);

        try {

            $email = User::where('email', $credentials['email'])->first();

            if ($email) {
                return response()->json([
                    'success' => false,
                    'error' => 'Email has been created'
                ], 409);
            }

            $user = User::create([
                'name' => $credentials['name'],
                'email' => $credentials['email'],
                'password' => Hash::make($credentials['password'])
            ]);

            $token = JWTAuth::fromUser($user);
    
            return response()->json([
                'authenticated' => true,
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error ' . $e
            ], 500);
        }

    }

    public function signIn(Request $request) {

        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'authenticated' => true,
                'token' => $token,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'error' => 'user not found'
        ], 404);

    }
}
