<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventPost extends Model
{
    protected $fillable = [
        'event_type', 'name', 'date', 'organizer', 'place', 'important_people', 'review', 'post_id'
    ];

    public $timestamps = false;

    public function post() {
        return $this->morphOne('App\Post', 'postable');
    }


    public function updatePost($id, $request) {
        $updatedPost = $this->where('post_id', $id)->update([
            'event_type'       => $request->event_type,
            'name'             => $request->name,
            'date'             => $request->date,
            'organizer'        => $request->organizer,
            'place'            => $request->place,
            'important_people' => $request->important_people,
            'review'           => $request->review
        ]);

        return $updatedPost;
    }
}
