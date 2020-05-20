<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'postable_id', 'postable_type', 'type'
    ];

    protected $attributes = [
        'sempak' => 'pe'
    ];

    public function test() {
        return $this->attributes['sempak'];
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function postable() {
        return $this->morphTo();
    }

    public function likes() {
        return $this->hasMany('App\Like');
    }

    public function reports() {
        return $this->hasMany('App\Report');
    }



    public function createPost($type) {
        $userId = auth()->user()->id;
        $modelPath = 'App\\' . ucfirst($type) . 'Post';

        $array = [
            'user_id'       => $userId,
            'postable_type' => $modelPath,
            'type'          => $type
        ];

        $createdPost = $this->create($array);

        return $createdPost;
    }


    public function isLiked() {
        $user = auth()->user();
        $likes = $this->likes()->get();

        if (!empty($likes)) $likedByUser = $likes->where('user_id', $user->id)->first();

        $isLiked = isset($likedByUser) ? true : false;

        return $isLiked;
    }


    public function isReported() {
        $user = auth()->user();
        $reports = $this->reports()->get();
        
        if (!empty($reports)) $reportedByUser = $reports->where('user_id', $user->id)->first();

        $isReported = isset($reportedByUser) ? true : false;

        return $isReported;
    }


}
