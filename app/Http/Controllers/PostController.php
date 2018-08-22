<?php

namespace App\Http\Controllers;

use App\Post;
use App\Repository\PostRepository;
use App\Trending;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PostController extends Controller
{
    /**
     * Show list of posts.
     * @param Trending $trending
     */
    public function index(Trending $trending)
    {
        $limit = \request()->get('limit', 50);
        $limit = is_numeric($limit) ? (int) $limit : 50;

        // we should cache response for better performance
        return (new PostRepository($trending))->popular($limit);
    }

    /**
     * Add new post.
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
