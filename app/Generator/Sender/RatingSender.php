<?php
/**
 * Created by PhpStorm.
 * User: gesparo
 * Date: 19.08.2018
 * Time: 10:54.
 */

namespace App\Generator\Sender;

use App\Post;
use App\Rating;
use Ixudra\Curl\Facades\Curl;

class RatingSender extends DataSender
{
    /**
     * Emulation of sending data to route.
     *
     * @param mixed ...$args
     * @return mixed
     */
    protected function sendFake(...$args)
    {
        [$post, $rating] = $args[0];

        // it is not good idea to duplicate route and generator logic, but it is the simplest solution
        Rating::create(['post_id' => $post->id, 'rating' => $rating]);

        $average = round(
            Rating::where('post_id', $post->id)->avg('rating'),
            2);

        Post::where('id', $post->id)
            ->limit(1)
            ->update(['avg_rating' => $average]);

        return true;
    }

    /**
     * Send data to route.
     *
     * @param mixed ...$args
     * @return mixed
     */
    protected function sendRoute(...$args)
    {
        [$post, $rating] = $args[0];

        Curl::to(route('ratable.store'))
            ->withData(['post_id' => $post->id, 'rating' => $rating])
            ->asJson()
            ->post();

        return true;
    }
}
