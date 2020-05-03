<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'profile_picture', 'date_of_birth'
    ];

    protected $hidden = [
        'password', 'remember_token', 'email_verified_at'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function posts() {
        return $this->hasMany('App\Post');
    }

    public function following() {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id');
    }

    public function followers() {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id');
    }

    public function likes() {
        return $this->hasMany('App\Like');
    }

    


    public function getUserPosts($page, $type) {
        $posts = $this->posts()
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->with('postable')
            ->withCount('likes')
            ->when(isset($type), function($query) use ($type) {
                $modelPath = 'App\\' . ucfirst($type) . 'Post';
                return $query->where('postable_type', $modelPath);
            })
            ->when(isset($page), 
                function($query) {
                    return $query->simplePaginate(5);
                },
                function($query) {
                    return $query->get();
                }
            );         
        
        return $posts;
    }


    public function getTimelinePosts($page, $type) {
        $following = $this->following()
            ->with(['posts' => function($query) use ($page, $type) {
                    $query->when(isset($type), function($queryChild) use ($type) {
                        $modelPath = 'App\\' . ucfirst($type) . 'Post';
                        return $queryChild->where('postable_type', $modelPath);
                    });

                    $query->orderBy('created_at', 'desc');

                    $query->when(isset($page), function($queryChild) {
                        return $queryChild->simplePaginate(5);
                    });

                    $query->with('user');
                    $query->with('postable');
                    $query->withCount('likes');
            }])
            ->get();

        $followingPosts = $following->flatMap(function ($values) {
            return $values->posts;
        });

        $userPosts = $this->getUserPosts($page, $type);

        $timelinePosts = $userPosts->merge($followingPosts);    

        $sorted = $timelinePosts->sortByDesc('created_at');

        return $sorted->values()->all();
    }
}
