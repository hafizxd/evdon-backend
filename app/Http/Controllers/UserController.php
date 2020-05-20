<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\User;
use App\Follow;

class UserController extends Controller
{
    public function __construct() {
        return $this->middleware('auth:api');
    }


    public function showUsers(Request $request) {
        $users = User::when(isset($request->search), function ($query) use ($request) {
                    return $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                $user->profile_picture = $_SERVER['HTTP_HOST'] . Storage::url('public/profile_pictures/' . $user->profile_picture);
                return $user;
            });

        return response()->json([
            'status' => 'success',
            'data'   => $users
        ], 200);
    }


    public function show($id) {
        $user = User::withCount('following')->withCount('followers')->findOrFail($id);
        
        if (isset($user->profile_picture)) $user->profile_picture = $_SERVER['HTTP_HOST'] . Storage::url('public/profile_pictures/' . $user->profile_picture);

        return response()->json([
            'status' => 'success',
            'data'    => $user
        ], 200);
    }


    public function update(Request $request) {
        $request->validate([
            'name'            => 'required|max:30',
            'date_of_birth'   => 'nullable|date|date_format:Y-m-d'
        ]);

        $user = auth()->user();
        $user->update($request->all());
        
        return response()->json(['status' => 'success'], 200);
    }


    public function updateProfilePicture(Request $request) {
        $request->validate([
            'profile_picture' => 'mimes:jpeg,jpg,png'
        ]);
        
        $user = auth()->user();
        
        if (isset($request->profile_picture)) {
            $filename = time() . '.' . $request->file('profile_picture')->extension();
            $request->file('profile_picture')->storeAs('public/profile_pictures', $filename);
        }

        if (isset($user->profile_picture)) Storage::delete('public/profile_pictures/' . $user->profile_picture);

        $user->update([
            'profile_picture' => isset($filename) ? $filename : null
        ]);

        return response()->json([ 'status' => 'success' ], 200);
    }


    public function follow($id) {
        $user_following = User::findOrFail($id);
        $user_follower = auth()->user();
        
        $follows = Follow::where('follower_id', $user_follower->id)->where('following_id', $user_following->id)->first();
        
        if (isset($follows)) {
            return response()->json([
                'status' => 'error',
                'message' => 'This user has been followed.'
            ], 400);
        }

        $user_follower->following()->attach($id);

        return response([ 'status' => 'success' ], 200);
    }


    public function unfollow($id) {
        $user = auth()->user();
        $user->following()->detach($id);

        return response([ 'status' => 'success' ], 200);
    }


    public function showFollowing($id) {
        $user = User::with('following')->withCount('following')->findOrFail($id);
        $following = $user->following;

        return response()->json([
            'status' => 'success',
            'data'   => $following
        ], 200);
    }


    public function showFollowers($id) {
        $user = User::with('followers')->withCount('following')->findOrFail($id);
        $followers = $user->followers;

        return response()->json([
            'status' => 'success',
            'data'   => $followers
        ], 200);
    }
}
