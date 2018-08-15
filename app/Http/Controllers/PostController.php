<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Show list of posts
     */
    public function index()
    {
        $limit = \request()->get('limit', 50);
        $limit = is_numeric($limit) ? (int) $limit : 50;

        // we should cache response for better performance
        return Post::orderBy('avg_rating', 'desc')
            ->take($limit)
            ->get(['title', 'body']);
    }

    /**
     * Add new post
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:3|max:191|unique:posts,title',
            'body' => 'required|min:3',
            'login' => 'required',
            'user_ip' => 'required',
        ]);

        /** @var User $user */
        $user = User::firstOrCreate(['login' => $request->get('login')]);

        return response()->json($user->posts()->create($request->all()));
    }
}
