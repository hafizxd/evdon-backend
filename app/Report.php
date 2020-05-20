<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id', 'post_id', 'status'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
