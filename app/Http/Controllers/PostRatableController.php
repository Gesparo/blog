<?php

namespace App\Http\Controllers;

use App\Rating;
use App\Trending;
use Illuminate\Http\Request;

class PostRatableController extends Controller
{
    public function store(Request $request, Trending $trending)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        Rating::create($request->all());

        $average = round(
                Rating::where('post_id', $request->get('post_id'))->avg('rating'),
                2);

        $trending->set($request->get('post_id'), $average);

        return response()->json(['average' => $average]);
    }
}
