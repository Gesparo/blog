<?php

namespace App\Http\Controllers;

use App\Generator\Builder;
use Illuminate\Http\Request;

class GeneratorController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'users_limit' => 'nullable|integer|min:1',
            'ips_limit' => 'nullable|integer|min:1',
            'posts_limit' => 'nullable|integer|min:1',
            'rating_limit' => 'nullable|integer|min:1',
            'rating_range_limit' => 'nullable|integer|min:1',
            'visualization' => 'nullable|boolean',
        ]);

        $visualization = (bool) $request->get('visualization', false);
        $limitOfUsers = (int) $request->get('users_limit', 100);
        $limitOfIps = (int) $request->get('ips_limit', 100);
        $limitOfPosts = (int) $request->get('posts_limit', 100);
        $limitOfRatings = (int) $request->get('rating_limit', 50);
        $rangeLimitForOneRating = (int) $request->get('rating_range_limit', 50);

        if ($limitOfRatings > $limitOfPosts) {
            $limitOfRatings = $limitOfPosts;
        }

        $generator = (new Builder())
            ->limitUsers($limitOfUsers)
            ->limitIps($limitOfIps)
            ->limitPosts($limitOfPosts)
            ->limitRating($limitOfRatings)
            ->limitRatingForOnePost($rangeLimitForOneRating)
            ->visualize($visualization)
            ->create();

        $generator->start();
    }
}
