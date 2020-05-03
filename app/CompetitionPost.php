<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompetitionPost extends Model
{
    protected $fillable = [
        'rank', 'name', 'date', 'level', 'place', 'specific_place', 'participants', 'description', 'post_id'
    ];

    public $timestamps = false;

    public function post() {
        return $this->morphOne('App\Post', 'postable');
    }


    public function updatePost($id, $request) {
        $updatedPost = $this->where('post_id', $id)->update([
            'rank'           => $request->rank,
            'name'           => $request->name,
            'date'           => $request->date,
            'level'          => $request->level,
            'place'          => $request->place,
            'specific_place' => $request->specific_place,
            'participants'   => $request->participants,
            'description'    => $request->description
        ]);

        return $updatedPost;
    }
}
