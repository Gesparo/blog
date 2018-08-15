<?php

namespace App\Http\Controllers;

use App\Post;
use App\Rating;
use Illuminate\Http\Request;

class PostRatableController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        Rating::create($request->all());

        $average = round(
                Rating::where('post_id', $request->get('post_id'))->avg('rating'),
                2);

        // If this operation will be slow, wi should use Queue Jobs for this
        Post::where('id', $request->get('post_id'))
            ->limit(1)
            ->update(['avg_rating' => $average]);

        return response()->json(['average' => $average]);
    }
}
