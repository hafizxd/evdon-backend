<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'postable_id', 'postable_type', 'type'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function postable() {
        return $this->morphTo();
    }

    public function likes() {
        return $this->hasMany('App\Like');
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


}
