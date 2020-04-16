<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\User;

class UserController extends Controller
{
    public function __construct() {
        return $this->middleware('auth:api');
    }

    public function get_profile() {

    }

    public function update_profile(Request $request) {
        $request->validate([
            'name' => 'max:30',
            'profile_picture' => 'jpeg, jpg, png',
            'date_of_birth' => 'nullable|date|date_format:Y-m-d'
        ]);

        $id = auth()->user()->id;
        $user = User::findOrFail($id);

        if (isset($request->profile_picture)) {
            if (isset($user->profile_picture)) Storage::delete('public/profile_pictures/' . $user->profile_picture);

            $filename = time() . '.' . $request->file('profile_picture')->extension();
            $request->file('profile_picture')->storeAs('public/profile_pictures', $filename);
        }

        User::findOrFail(1)->update([
            'name' => isset($request->name) ? $request->name : $user->name,
            'profile_picture' => isset($filename) ? $filename : $user->profile_picture,
            'date_of_birth' => $request->date_of_birth
        ]);
        
        return response()->json(['message' => 'success'], 200);
    }
}
