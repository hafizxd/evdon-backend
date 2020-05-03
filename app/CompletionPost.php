<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompletionPost extends Model
{
    protected $fillable = [
        'activity', 'object', 'date', 'title', 'place', 'link', 'rating', 'review', 'post_id'
    ];

    public $timestamps = false;

    public function post() {
        return $this->morphOne('App\Post', 'postable');
    }


    public function updatePost($id, $request) {
        $updatedPost = $this->where('post_id', $id)->update([
            'activity' => $request->activity,
            'object'   => $request->object,
            'date'     => $request->date,
            'title'    => $request->title,
            'place'    => $request->place,
            'link'     => $request->link,
            'rating'   => $request->rating,
            'review'   => $request->review
        ]);

        return $updatedPost;
    }
}
