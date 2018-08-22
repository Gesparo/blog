<?php
/**
 * Created by PhpStorm.
 * User: gesparo
 * Date: 21.08.2018
 * Time: 22:41
 */

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

class Trending
{
    /**
     * Fetch all trending posts.
     *
     * @param int $limit
     * @return array
     */
    public function get(int $limit = 50) :Collection
    {
        return collect(Redis::zrevrange($this->getCacheKey(), 0, $limit - 1));
    }

    /**
     * Set rating for specific post
     *
     * @param int $postId
     * @param float $rating
     * @return void
     */
    public function set(int $postId, float $rating) :void
    {
        Redis::zadd($this->getCacheKey(), $rating, $postId);
    }

    /**
     * Get redis cache key
     *
     * @return string
     */
    public function getCacheKey() :string
    {
        return app()->environment('testing') ? 'test_post_rating' : 'post_rating';
    }

    public function reset()
    {
        Redis::del($this->getCacheKey());
    }
}