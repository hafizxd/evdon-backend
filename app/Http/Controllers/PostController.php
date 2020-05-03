<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\CompetitionPost;
use App\CompletionPost;
use App\EventPost;
use App\User;
use App\Like;

class PostController extends Controller
{
    public function __construct() {
        return $this->middleware('auth:api');
    }


    public function showUserPosts(Request $request, $id) {
        $request->validate([
            'page'   => 'integer',
            'type' => 'in:competition,completion,event'
        ]);

        $posts = User::findOrFail($id)->getUserPosts($request->page, $request->type)->toArray();
        $posts = isset($posts['data']) ? $posts['data'] : $posts;
        
        $userPosts = $this->formatResponsePost($posts);

        return response()->json([
            'status' => 'success',
            'data'   => $userPosts
        ], 200);
    }


    public function showTimelinePosts(Request $request) {
        $request->validate([
            'page'   => 'integer',
            'type' => 'in:competition,completion,event'
        ]);

        $posts = auth()->user()->getTimelinePosts($request->page, $request->type);
        $posts = collect($posts)->toArray();

        $timelinePosts = $this->formatResponsePost($posts);

        return response()->json([
            'status' => 'success',
            'data'   => $timelinePosts
        ], 200);
    }


    public function store(Request $request) {
        $request->validate([ 'type' => 'required|in:competition,completion,event' ]);

        $rules = $this->pickRules($request->type);
        $request->validate($rules);

        $instantiatePost = new Post();
        $post = $instantiatePost->createPost($request->type);

        $childPost = $this->pickPostableClass($request->type);

        $array = $request->all();
        $array['post_id'] = $post->id;
        $createdPost = $childPost->create($array);

        $post->update([ 'postable_id' => $createdPost->id ]);

        return response([ 'status' => 'success' ], 201);
    }


    public function update(Request $request, $id) {
        $post = auth()->user()->posts()->findOrFail($id);

        $request->validate([ 'type' => 'required|in:competition,completion,event' ]);

        $rules = $this->pickRules($post->type);
        $request->validate($rules);

        $childPost = $this->pickPostableClass($request->type);

        $childPost->updatePost($post->id, $request);

        return response()->json(['status' => 'success'], 200);
    }


    public function delete($id) {
        $post = auth()->user()->posts()->findOrFail($id);
        $post->delete();

        return response([ 'status' => 'success' ], 200);
    }


    public function showLikes($id) {
        $post = Post::findOrFail($id);
        $likes = $post->likes()->with('user')->get();
        $likesCount = count($likes);

        $users = [];
        foreach ($likes as $like) {
            array_push($users, $like->user);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'likes_count' => $likesCount,
                'users'       => $users
            ]
        ], 200);
    }


    public function likePost($id) {
        $post = Post::findOrFail($id);
        $user = auth()->user();

        $like = Like::where('user_id', $user->id)->where('post_id', $post->id)->first();
        
        if (isset($like)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This post has been liked.'
            ], 400);
        }

        Like::create([
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);

        return response()->json([ 'status' => 'success' ], 201);
    }


    public function unlikePost($id) {
        $post = Post::findOrFail($id);
        $user = auth()->user();

        $like = Like::where('user_id', $user->id)->where('post_id', $post->id)->first();

        if (isset($like)) $like->delete();

        return response()->json([ 'status' => 'success' ], 201);
    }



    protected function formatResponsePost($posts) {
        $resultPosts = [];
        foreach ($posts as $post)  {
            unset($post['user_id'], $post['postable_id'], $post['postable_type'], $post['postable']['id'], $post['postable']['post_id']);
            $post = array_merge($post, $post['postable']);
            unset($post['postable']);

            $user = $post['user'];
            unset($post['user']);
            $post['user'] = $user;

            array_push($resultPosts, $post);
        }

        return $resultPosts;
    }


    protected function pickRules($type) {
        $competition_rules = [
            'rank'         => 'integer',
            'name'         => 'required',
            'date'         => 'required|date|date_format:Y-m-d',
            'participants' => 'integer'
        ];

        $completion_rules = [
            'activity' => 'required',
            'object'   => 'required',
            'date'     => 'required|date|date_format:Y-m-d',
            'link'     => 'url',
            'rating'   => 'digits_between:0,10'
        ];

        $event_rules = [
            'event_type' => 'required',
            'name'       => 'required',
            'date'       => 'required|date|date_format:Y-m-d'
        ];
        
        if ($type === 'competition') $rules = $competition_rules;
        else if ($type === 'completion') $rules = $completion_rules;
        else if ($type === 'event') $rules = $event_rules;

        return $rules;
    }


    protected function pickPostableClass($type) {
        if ($type === 'competition') $childPost = new CompetitionPost();
        else if ($type === 'completion') $childPost = new CompletionPost();
        else if ($type === 'event') $childPost = new EventPost();

        return $childPost;
    }
}
