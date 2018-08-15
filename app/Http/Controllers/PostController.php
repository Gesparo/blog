<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
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
