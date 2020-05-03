<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class AuthController extends Controller
{
    public function __construct() {
        return $this->middleware('auth:api', ['except' => ['register', 'login']]);
    }

    public function register(Request $request) {
        $request->validate([
            'name'     => 'required|max:30',
            'email'    => 'required|email|unique:users',
            'password' => 'required',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password)
        ]);
        
        $credentials = request(['email', 'password']);
        
        $token = auth()->attempt($credentials);
         
        return $this->respondWithToken($token);
    }


    public function login(Request $request) {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
         
        return $this->respondWithToken($token);
    }

    
    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'data'         => auth()->user(),
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }
}
